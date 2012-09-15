<?php
/**
 *
 * Field
 *
 * Abstracts general fields parameters (type, value, name) and
 * reforms a correct form field depending on what was asked
 */
namespace Former;

abstract class Field
{
  /**
   * The field type
   * @var string
   */
  protected $type;

  /**
   * The field value
   * @var string
   */
  protected $value;

  /**
   * The field attributes
   * @var array
   */
  protected $attributes = array();

  /**
   * The field's control group
   * @var ControlGroup
   */
  protected $controlGroup;

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// INTERFACE /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set up a Field instance
   *
   * @param string $type A field type
   */
  public function __construct($type, $name, $label, $value, $attributes)
  {
    // Set base parameters
    $this->type = $type;
    $this->value = $value;
    $this->attributes = (array) $attributes;

    // Set magic parameters (repopulated value, translated label, etc)
    $this->ponder($name, $label);
    $this->repopulate();

    // Link Control group
    $this->controlGroup = new ControlGroup($this->label);
  }

  /**
   * Dynamically set attributes
   *
   * @param  string $method     An attribute
   * @param  array  $parameters Its value(s)
   */
  public function __call($method, $parameters)
  {
    $value = array_get($parameters, 0, 'true');
    $this->setAttribute($method, $value);

    return $this;
  }

  /**
   * Get a Field variable or an attribute
   *
   * @param  string $attribute The desired attribute
   * @return string            Its value
   */
  public function __get($attribute)
  {
    if(isset($this->$attribute)) return $this->$attribute;
    else return array_get($this->attributes, $attribute);
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// FUNCTIONS //////////////////////////////
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

  ////////////////////////////////////////////////////////////////////
  //////////////////////// SETTERS AND GETTERS ///////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Returns this Field's control group
   *
   * @return ControlGroup
   */
  public function getControl()
  {
    return $this->controlGroup;
  }

  /**
   * Adds a label to the control group
   *
   * @param  string $text A label
   * @return Field        A field
   */
  public function label($text)
  {
    $this->controlGroup->setLabel($text);

    return $this;
  }

  /**
   * Set the Field value
   *
   * @param string $value A new value
   */
  public function value($value)
  {
    $this->value = $value;
  }

  /**
   * Set an attribute
   *
   * @param string $attribute An attribute
   * @param string $value     Its value
   */
  public function setAttribute($attribute, $value = null)
  {
    $this->attributes[$attribute] = $value;
  }

  /**
   * Set a bunch of parameters at once
   *
   * @param array   $attributes An associative array of attributes
   * @param boolean $merge      Whether they should be merged to the old ones
   */
  public function setAttributes($attributes, $merge = true)
  {
    $attributes = (array) $attributes;

    $this->attributes = $merge
      ? array_merge($this->attributes, $attributes)
      : $attributes;
  }

  /**
   * Add a class to the current field
   *
   * @param string $class The class to add
   */
  public function addClass($class)
  {
    $this->attributes = Helpers::addClass($this->attributes, $class);
  }

  /**
   * Use values stored in Former to populate the current field
   */
  private function repopulate()
  {
    $value = Former::getValue($this->name);

    // If nothing found, replace by fallback
    if(!$value) $value = $this->value;

    // Overwrite value by POST if present
    $value = \Input::get($this->name, \Input::old($this->name, $value));

    $this->value = $value;
  }

  /**
   * Ponders a label and a field name, and tries to get the best out of it
   *
   * @param  string $label A label
   * @param  string $name  A field name
   * @return array         A label and a field name
   */
  private function ponder($name, $label)
  {
    // Check for the two possibilities
    if($label and !$name) $name = \Str::slug($label);
    elseif(!$label and $name) $label = $name;

    // Attempt to translate the label
    $label = Helpers::translate($label);
    $label = ucfirst($label);

    // Save values
    $this->name  = $name;
    $this->label = $label;
  }
}
