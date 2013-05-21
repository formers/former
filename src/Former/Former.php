<?php
namespace Former;

use Closure;
use Former\Interfaces\FrameworkInterface;
use Illuminate\Container\Container;
use Underscore\Methods\ArraysMethods as Arrays;

/**
 * Helps the user interact with it and its classes
 * Various form helpers for repopulation, rules, etc.
 */
class Former
{

  // Instances ----------------------------------------------------- /

  /**
   * The current environment
   *
   * @var Illuminate\Container
   */
  protected $app;

  /**
   * The current field being worked on
   *
   * @var Field
   */
  protected $field;

  /**
   * The current form framework
   *
   * @var FrameworkInterface
   */
  protected $formFramework;

  /**
   * The current form being worked on
   *
   * @var Form
   */
  protected $form;

  /**
   * The Populator instance
   *
   * @var Populator
   */
  public $populator;

  // Informations -------------------------------------------------- /

  /**
   * The form's errors
   *
   * @var Message
   */
  protected $errors;

  /**
   * An array of rules to use
   *
   * @var array
   */
  protected $rules = array();

  /**
   * An array of field macros
   *
   * @var array
   */
  protected $macros = array();

  /**
   * The labels created so far
   *
   * @var array
   */
  public $labels = array();

  // Namespaces ---------------------------------------------------- /

  /**
   * The namespace of Form elements
   */
  const FORMSPACE = 'Former\Form\\';

  /**
   * The namespace of fields
   */
  const FIELDSPACE = 'Former\Form\Fields\\';

  /**
   * Build a new Former instance
   *
   * @param Illuminate\Container\Container $app
   */
  public function __construct(Container $app, Populator $populator, FrameworkInterface $framework)
  {
    $this->app           = $app;
    $this->populator     = $populator;
    $this->formFramework = $framework;
    Helpers::setApp($this, $this->app['translator']);
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// INTERFACE /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Acts as a router that redirects methods to all of Former classes
   *
   * @param  string $method     The method called
   * @param  array  $parameters An array of parameters
   *
   * @return mixed
   */
  public function __call($method, $parameters)
  {
    // Dispatch to Form\Elements
    if ($element = Dispatch::toElements($this->app, $method, $parameters)) {
      return $element;
    }

    // Dispatch to Form\Form
    if ($form = Dispatch::toForm($this, $method, $parameters)) {
      return $this->form = $form;
    }

    // Dispatch to Form\Group
    if ($group = Dispatch::toGroup($this, $method, $parameters)) {
      return $group;
    }

    // Dispatch to Form\Actions
    if ($actions = Dispatch::toActions($this, $method, $parameters)) {
      return $actions;
    }

    // Dispatch to macros
    if ($macro = Dispatch::toMacros($this, $method, $parameters)) {
      return $macro;
    }

    // Checking for any supplementary classes
    $classes = explode('_', $method);
    $method  = array_pop($classes);

    // Dispatch to the different Form\Fields
    $field = Dispatch::toFields($this, $method, $parameters);
    $field = $this->getFramework()->getFieldClasses($field, $classes);

    return $this->field = $field;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// MACROS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Register a macro with Former
   *
   * @param  string  $name         The name of the macro
   * @param  Closure $macro        The macro itself
   *
   * @return mixed
   */
  public function macro($name, Closure $macro)
  {
    $this->macros[$name] = $macro;
  }

  /**
   * Check if a macro exists
   *
   * @param  string  $name
   *
   * @return boolean
   */
  public function hasMacro($name)
  {
    return isset($this->macros[$name]);
  }

  /**
   * Get a registered macro
   *
   * @param  string $name
   *
   * @return Closure
   */
  public function getMacro($name)
  {
    return $this->macros[$name];
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// POPULATOR ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Add values to populate the array
   *
   * @param mixed $values Can be an Eloquent object or an array
   */
  public function populate($values)
  {
    $this->populator->setValues($values);
  }

  /**
   * Set the value of a particular field
   *
   * @param string $field The field's name
   * @param mixed  $value Its new value
   */
  public function populateField($field, $value)
  {
    $this->populator->setValue($field, $value);
  }

  /**
   * Get the value of a field
   *
   * @param string $field The field's name
   * @return mixed
   */
  public function getValue($field, $fallback = null)
  {
    return $this->populator->getValue($field, $fallback);
  }

  /**
   * Fetch a field value from both the new and old POST array
   *
   * @param  string $name     A field name
   * @param  string $fallback A fallback if nothing was found
   * @return string           The results
   */
  public function getPost($name, $fallback = null)
  {
    $oldValue = $this->app['request']->old($name, $fallback);

    return $this->app['request']->get($name, $oldValue);
  }

  /**
   * Get the Populator binded to Former
   *
   * @return Populator
   */
  public function getPopulator()
  {
    return $this->populator;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// TOOLKIT /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set the errors to use for validations
   *
   * @param Message $validator The result from a validation
   */
  public function withErrors($validator = null)
  {
    // Try to get the errors form the session
    if($this->app['session']->has('errors')) $errors = $this->app['session']->get('errors');

    // If we're given a raw Validator, go fetch the errors in it
    if(method_exists($validator, 'getMessageBag')) $errors = $validator->getMessageBag();
    if($validator instanceof \Laravel\Validator) $errors = $validator->errors;

    // If we found errors, bind them to the form
    if(isset($errors)) $this->errors = $errors;
    elseif($validator) $this->errors = $validator;
  }

  /**
   * Add live validation rules
   *
   * @param  array *$rules An array of Laravel rules
   */
  public function withRules()
  {
    $rules = func_get_args();
    if (sizeof($rules) == 1 and is_string($rules[0])) $rules = explode('|', $rules[0]);
    else $rules = call_user_func_array('array_merge', func_get_args());

    // Parse the rules according to Laravel conventions
    foreach ($rules as $name => $fieldRules) {
      $expFieldRules = $fieldRules;
      if (!is_array($expFieldRules)) {
        $expFieldRules = explode('|', $expFieldRules);
      }

      foreach ($expFieldRules as $rule) {

        // If we have a rule with a value
        if (($colon = strpos($rule, ':')) !== false) {
          $parameters = str_getcsv(substr($rule, $colon + 1));
       }

       // Exclude unsupported rules
       $rule = is_numeric($colon) ? substr($rule, 0, $colon) : $rule;

       // Store processed rule in Former's array
       if(!isset($parameters)) $parameters = array();
       $this->rules[$name][$rule] = $parameters;
      }
    }
  }

  /**
   * Switch the framework used by Former
   *
   * @param string $framework The name of the framework to use
   */
  public function framework($framework = null)
  {
    if (!$framework) return $this->app['former']->getFramework()->current();

    $class = __NAMESPACE__.'\Framework\\'.$framework;
    $this->formFramework = $this->app->make($class);
  }

  /**
   * Get the current framework
   *
   * @return FrameworkInterface
   */
  public function getFramework()
  {
    return $this->formFramework;
  }

  /**
   * Get a class out of the Contaienr
   *
   * @param string $dependency The class
   *
   * @return object
   */
  public function getContainer($dependency = null)
  {
    if ($dependency) return $this->app[$dependency];
    return $this->app;
  }

  /**
   * Get an option from the config
   *
   * @param string $option  The option
   * @param mixed $default Optional fallback
   *
   * @return mixed
   */
  public function getOption($option, $default = null)
  {
    $prefix = $this->app['config']->get('former::framework');
    $prefix = !empty($prefix) ? 'former::' : 'config.';

    return $this->app['config']->get($prefix.$option, $default);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// BUILDERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Closes a form
   *
   * @return string A form closing tag
   */
  public function close()
  {
    if (!$this->form) return false;

    $closed = $this->form()->close();

    // Destroy instances
    $this->form = null;
    $this->populator->reset();

    // Reset all values
    $this->errors = null;
    $this->rules  = null;

    return $closed;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the errors for the current field
   *
   * @param  string $name A field name
   * @return string       An error message
   */
  public function getErrors($name = null)
  {
    if (!$this->field) return false;

    // Get name and translate array notation
    if(!$name) $name = $this->field->getName();
    $name = preg_replace('/\[([a-z]+)\]/', '.$1', $name);

    if ($this->errors) {
      return $this->errors->first($name);
    }
  }

  /**
   * Get a rule from the Rules array
   *
   * @param  string $name The field to fetch
   * @return array        An array of rules
   */
  public function getRules($name)
  {
    return Arrays::get($this->rules, $name);
  }

  /**
   * Returns the current Form
   *
   * @return Form
   */
  public function form()
  {
    return $this->form;
  }

  /**
   * Get the current field instance
   *
   * @return Field
   */
  public function field()
  {
    if(!$this->field) return false;

    return $this->field;
  }

}
