<?php
/**
 * Former
 *
 * Superset of Field ; helps the user interact with it and its classes
 * Various form helpers for repopulation, rules, etc.
 */
namespace Former;

use \Underscore\Arrays;
use \Underscore\String;

class Former
{
  /**
   * Illuminate application instance.
   * @var Illuminate/Foundation/Application
   */
  protected $app;

  /**
   * The current field being worked on
   * @var Field
   */
  protected $field;

  /**
   * The current form being worked on
   * @var Form
   */
  protected $form;

  /**
   * Values populating the form
   * @var array
   */
  protected $values;

  /**
   * The form's errors
   * @var Message
   */
  protected $errors;

  /**
   * An array of rules to use
   * @var array
   */
  protected $rules = array();

  /**
   * The namespace of fields
   */
  const FIELDSPACE = 'Former\Form\Fields\\';

  public function __construct($app)
  {
    $this->app = $app;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// INTERFACE /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Creates a field instance
   *
   * @param  string $method     The field type
   * @param  array  $parameters An array of parameters
   * @return Former
   */
  public function __call($method, $parameters)
  {
    // Form opener
    if (strpos($method, 'open') !== false and strpos($method, 'open') >= 0) {
      $this->form = new Form\Form($this->app);

      return $this->form->open($method, $parameters);
    }

    // Avoid conflict with chained label method
    if ($method == 'label') {
      return $this->_label($parameters[0], Arrays::get($parameters, 1));
    }

    // Checking for any supplementary classes
    $classes = explode('_', $method);
    $method  = array_pop($classes);

    // Destroy previous field instance
    $this->field = null;

    // Picking the right Class
    $callClass = $this->app['former.helpers']->getClassFromMethod($method);

    // Listing parameters
    $class = self::FIELDSPACE.$callClass;
    $this->field = new $class(
      $this->app,
      $method,
      Arrays::get($parameters, 0),
      Arrays::get($parameters, 1),
      Arrays::get($parameters, 2),
      Arrays::get($parameters, 3),
      Arrays::get($parameters, 4),
      Arrays::get($parameters, 5)
    );

    // Add framework/provided classes
    $this->field = $this->app['former.framework']->addFieldClasses($this->field, $classes);

    return $this->field;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// TOOLKIT ///////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Add values to populate the array
   *
   * @param mixed $values Can be an Eloquent object or an array
   */
  public function populate($values)
  {
    $this->values = $values;
  }

  /**
   * Set a specific value in the population array
   *
   * @param string $key   The key to change
   * @param string $value The new value
   */
  public function populateField($key, $value)
  {
    if (is_object($this->values)) {
      $this->values->$key = $value;
    } else {
      $this->values[$key] = $value;
    }
  }

  /**
   * Get a value from the object/array
   *
   * @param  string $name     The key to retrieve
   * @param  string $fallback Fallback value if nothing found
   * @return mixed            Its value
   */
  public function getValue($name, $fallback = null)
  {
    // Object values
    if (is_object($this->values)) {

      // Transform the name into an array
      $value = $this->values;
      $name  = String::contains($name, '.') ? explode('.', $name) : (array) $name;

      // Dive into the model
      foreach ($name as $r) {

        // Multiple results relation
        if (is_array($value)) {
          foreach ($value as $subkey => $submodel) {
            $value[$subkey] = isset($submodel->$r) ? $submodel->$r : $fallback;
          }
          continue;
        }

        // Single model relation
        if(isset($value->$r) or method_exists($value, 'get_'.$r)) $value = $value->$r;
        else {
          $value = $fallback;
          break;
        }
      }

      return $value;
    }

    // Plain array
    return Arrays::get($this->values, $name, $fallback);
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
    $old = $this->app['request']->old($name, $fallback);

    return $this->app['request']->get($name, $old);
  }

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
    if(method_exists($validator, 'getMessages')) $errors = $validator->getMessages();

    // If we found errors, bind them to the form
    if(isset($errors)) $this->errors = $errors;
    else $this->errors = $validator;
  }

  /**
   * Add live validation rules
   *
   * @param  array *$rules An array of Laravel rules
   */
  public function withRules()
  {
    $rules = call_user_func_array('array_merge', func_get_args());

    // Parse the rules according to Laravel conventions
    foreach ($rules as $name => $fieldRules) {
      foreach (explode('|', $fieldRules) as $rule) {

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
    if (!$framework) return $this->app['former.framework']->current();

    $this->app['former.framework'] = $this->app->share(function($app) use ($framework) {
      $class = __NAMESPACE__.'\Framework\\'.$framework;

      return new $class($app);
    });
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

    // Destroy Form instance
    $this->form = null;

    // Reset all values
    $this->values = null;
    $this->errors = null;
    $this->rules  = null;

    return $closed;
  }

  /**
   * Generate a hidden field containing the current CSRF token.
   *
   * @return string
   */
  public function token()
  {
    $csrf = $this->app['session']->getToken();

    return $this->hidden($csrf, $csrf)->__toString();
  }

  /**
   * Creates a label tag
   *
   * @param  string $label      The label content
   * @param  string $name       The field the label's for
   * @param  array  $attributes The label's attributes
   * @return string             A <label> tag
   */
  public function _label($label, $name = null, $attributes = array())
  {
    $label = $this->app['former.helpers']->translate($label);

    return $this->app['former.laravel.form']->label($name, $label, $attributes);
  }

  /**
   * Creates a form legend
   *
   * @param  string $legend     The text
   * @param  array  $attributes Its attributes
   * @return string             A <legend> tag
   */
  public function legend($legend, $attributes = array())
  {
    $legend = $this->app['former.helpers']->translate($legend);

    return '<legend'.$this->app['former.helpers']->attributes($attributes).'>' .$legend. '</legend>';
  }

  /**
   * Writes the form actions
   *
   * @return string A .form-actions block
   */
  public function actions()
  {
    $buttons = func_get_args();

    $actions  = '<div class="form-actions">';
      $actions .= implode(' ', (array) $buttons);
    $actions .= '</div>';

    return $actions;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// HELPERS ///////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the errors for the current field
   *
   * @param  string $name A field name
   * @return string       An error message
   */
  public function getErrors($name = null)
  {
    // Get name and translate array notation
    if(!$name) $name = $this->field->name;
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
