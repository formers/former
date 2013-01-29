<?php
/**
 * Field
 *
 * Abstracts general fields parameters (type, value, name) and
 * reforms a correct form field depending on what was asked
 */
namespace Former\Traits;

use \Former\Form\Form;
use \Former\Form\Group;
use \Former\Helpers;
use \Former\Interfaces\FieldInterface;
use \Former\LiveValidation;
use \Underscore\Types\Arrays;
use \Underscore\Types\String;

abstract class Field extends FormerObject implements FieldInterface
{
  /**
   * The field type
   * @var string
   */
  protected $type;

  /**
   * Illuminate application instance
   * @var Illuminate\Foundation\Application  $app
   */
  protected $app;

  /**
   * The Form instance
   * @var Former\Form
   */
  protected $form;

  /**
   * A label for the field (if not using Bootstrap)
   * @var string
   */
  protected $label = array(
    'label'      => null,
    'attributes' => array()
  );

  /**
   * The field's group
   * @var Group
   */
  protected $group;

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// INTERFACE ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set up a Field instance
   *
   * @param string $type A field type
   */
  public function __construct($app, $type, $name, $label, $value, $attributes)
  {
    // Set base parameters
    $this->app        = $app;
    $this->attributes = (array) $attributes;
    $this->type       = $type;
    $this->value      = $value;

    // Set magic parameters (repopulated value, translated label, etc)
    $this->automaticLabels($name, $label);

    if($type != 'password') $this->value = $this->repopulate();
    if ($this->app['former']->getOption('live_validation')) {
      $rules = new LiveValidation($this);
      $rules->apply($this->getRules());
    }

    // Link Control group
    if ($this->app['former']->getFramework()->isnt('Nude')) {
      $this->group = new Group($this->app, $this->label);
    }
  }

  /**
   * Redirect calls to the group if necessary
   */
  public function __call($method, $parameters)
  {
    // Redirect calls to the Control Group
    if (method_exists($this->group, $method)) {
      call_user_func_array(array($this->group, $method), $parameters);

      return $this;
    }

    return parent::__call($method, $parameters);
  }

  /**
   * Prints out the field
   *
   * @return string
   */
  public function __toString()
  {
    // Dry syntax (hidden fields, plain fields)
    if ($this->isUnwrappable()) $html = $this->render();

    // Control group syntax
    elseif ($this->app['former']->getFramework()->isnt('Nude') and Form::isOpened()) {
      $html = $this->group->wrapField($this);
    }

    // Classic syntax
    else {
      $html  = $this->app['former']->getFramework()->createLabelOf($this);
      $html .= $this->render();
    }

    return $html;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// FUNCTIONS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Whether the current field is required or not
   *
   * @return boolean
   */
  public function isRequired()
  {
    return isset($this->attributes['required']);
  }

  /**
   * Check if a field is unwrappable (no label)
   *
   * @return boolean
   */
  public function isUnwrappable()
  {
    return
      $this->app['former']->form() and $this->app['former']->form()->isOfType('inline') or
      $this->isOfType('hidden', 'link', 'submit', 'button', 'reset') or
      $this->group and $this->group->isRaw();
  }

  /**
   * Check if field is a checkbox or a radio
   *
   * @return boolean
   */
  public function isCheckable()
  {
    return $this->isOfType('checkboxes', 'radios');
  }

  /**
   * Check if the field is a button
   *
   * @return boolean
   */
  public function isButton()
  {
    return false;
  }

  /**
   * Get the rules applied to the current field
   *
   * @return array An array of rules
   */
  public function getRules()
  {
    return $this->app['former']->getRules($this->name);
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////// SETTERS AND GETTERS ///////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Apply a Live Validation rule by chaining
   *
   * @param string $rule The rule
   * @param mixed $parameters* The rule parameters
   */
  public function rule($rule)
  {
    $parameters = Arrays::removeFirst(func_get_args());

    $live = new LiveValidation($this);
    $live->apply(array(
      $rule => $parameters,
    ));

    return $this;
  }

  /**
   * Adds a label to the group/field
   *
   * @param  string $text       A label
   * @param  array  $attributes The label's attributes
   * @return Field              A field
   */
  public function label($text, $attributes = array())
  {
    $label = array(
      'text'       => Helpers::translate($text),
      'attributes' => $attributes);

    if($this->group) $this->group->setLabel($label);
    else $this->label = $label;
    return $this;
  }

  /**
   * Set the Field value no matter what
   *
   * @param string $value A new value
   */
  public function forceValue($value)
  {
    $this->value = $value;

    return $this;
  }

  /**
   * Classic setting of attribute, won't overwrite any populate() attempt
   *
   * @param  string $value A new value
   */
  public function value($value)
  {
    // Check if we already have a value stored for this field or in POST data
    $already = $this->repopulate();

    if(!$already) $this->value = $value;

    return $this;
  }

  /**
   * Change the field's name
   *
   * @param  string $name The new name
   */
  public function name($name)
  {
    $this->name = $name;

    // Also relink the label to the new name
    $this->label($name);

    return $this;
  }

  /**
   * Get the field's labels
   *
   * @return array
   */
  public function getLabel()
  {
    return $this->label;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// HELPERS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Use values stored in Former to populate the current field
   */
  private function repopulate($fallback = null)
  {
    if(is_null($fallback)) $fallback = $this->value;

    // Get values from POST, populated, and manually set value
    $post     = $this->app['former']->getPost($this->name);
    $populate = $this->app['former']->getValue($this->name);

    // Assign a priority to each
    if(!is_null($post)) $value = $post;
    elseif(!is_null($populate)) $value = $populate;
    else $value = $fallback;
    return $value;
  }

  /**
   * Ponders a label and a field name, and tries to get the best out of it
   *
   * @param  string $label A label
   * @param  string $name  A field name
   * @return array         A label and a field name
   */
  private function automaticLabels($name, $label)
  {
    // Disabled automatic labels
    if (!$this->app['former']->getOption('automatic_label')) {
      $this->name = $name;
      $this->label($label);

      return false;
    }

    // Check for the two possibilities
    if($label and is_null($name)) $name = String::slug($label);
    elseif(is_null($label) and $name) $label = $name;

    // Attempt to translate the label
    $label = Helpers::translate($label);

    // Save values
    $this->name  = $name;
    $this->label($label);
  }
}
