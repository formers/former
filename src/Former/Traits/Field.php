<?php
namespace Former\Traits;

use Former\Form\Form;
use Former\Form\Group;
use Former\Former;
use Former\Helpers;
use Former\Interfaces\FieldInterface;
use Former\LiveValidation;
use Underscore\Methods\ArraysMethods as Arrays;
use Underscore\Methods\StringMethods as String;

/**
 * Abstracts general fields parameters (type, value, name) and
 * reforms a correct form field depending on what was asked
 */
abstract class Field extends FormerObject implements FieldInterface
{

  /**
   * The Former instance
   *
   * @var Former
   */
  protected $former;

  /**
   * The Form instance
   *
   * @var Former\Form
   */
  protected $form;

  /**
   * A label for the field (if not using Bootstrap)
   *
   * @var string
   */
  protected $label;

  /**
   * The field's group
   *
   * @var Group
   */
  protected $group;

  /**
   * The field's default element
   *
   * @var string
   */
  protected $element = 'input';

  /**
   * Whether the Field is self-closing or not
   *
   * @var boolean
   */
  protected $isSelfClosing = true;

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// INTERFACE ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set up a Field instance
   *
   * @param string $type A field type
   */
  public function __construct(Former $former, $type, $name, $label, $value, $attributes)
  {
    // Set base parameters
    $this->former     = $former;
    $this->attributes = (array) $attributes;
    $this->type       = $type;
    $this->value      = $value;

    // Compute and translate label
    $this->automaticLabels($name, $label);

    // Repopulate field
    if ($type != 'password') {
      $this->value = $this->repopulate();
    }

    // Apply Live validation rules
    if ($this->former->getOption('live_validation')) {
      $rules = new LiveValidation($this);
      $rules->apply($this->getRules());
    }

    // Link Control group
    if ($this->former->getFramework()->isnt('Nude')) {
      $groupClass = $this->isCheckable() ? 'CheckableGroup' : 'Group';
      $groupClass = Former::FORMSPACE.$groupClass;

      $this->group = new $groupClass($this->former, $this->label);
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
    elseif ($this->former->getFramework()->isnt('Nude') and Form::hasInstanceOpened()) {
      $html = $this->group->wrapField($this);
    }

    // Classic syntax
    else {
      $html  = $this->former->getFramework()->createLabelOf($this);
      $html .= $this->render();
    }

    return $html;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// PUBLIC INTERFACE ////////////////////////
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
      $this->former->form() and $this->former->form()->isOfType('inline') or
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
    return $this->former->getRules($this->name);
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
    $text  = Helpers::translate($text);
    $label = $this->former->label($text, $this->name, $attributes);

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
    // Get values from POST, populated, and manually set value
    $post      = $this->former->getPost($this->name);
    $populator = $this->form ? $this->form->getPopulator() : $this->former->getPopulator();
    $populate  = $populator->getValue($this->name);

    // Assign a priority to each
    if(!is_null($post))     return $post;
    if(!is_null($populate)) return $populate;

    return $fallback ?: $this->value;
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
    if (!$this->former->getOption('automatic_label')) {
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
