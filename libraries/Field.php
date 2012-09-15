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

public static function lol()
{
  return $this->attributes;
}

  /**
   * Set up a Field instance
   *
   * @param string $type A field type
   */
  public function __construct($type, $name, $label, $value, $attributes)
  {
    // Field
    $this->type = $type;
    $this->setAttributes($attributes);
    $this->name($name);
    $this->value($value);

    // Control group
    $this->controlGroup = new ControlGroup($label);
  }

  /**
   * Dynamically set attributes
   *
   * @param  string $method     An attribute
   * @param  array  $parameters Its value(s)
   */
  public function __call($method, $parameters)
  {
    // Dynamic attributes
    switch($method) {
      case 'autofocus':
      case 'disabled':
      case 'multiple':
      case 'required':
        $this->setAttribute($method, 'true');
        break;
      default:
        $this->setAttribute($method, $parameters[0]);
        break;
    }

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
    $this->attributes = $merge
      ? array_merge($this->attributes, $attributes)
      : $attributes;
  }

  /**
   * Whether the current field is required or not
   *
   * @return boolean
   */
  public function isRequired()
  {
    return isset($this->attributes['required']);
  }
}