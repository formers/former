<?php
/**
 * FormerObject
 *
 * Base Former object that allows chained attributes setting, adding
 * classes to the existing ones, and provide types helpers
 */
namespace Former\Traits;

use \Former\Helpers;
use \Underscore\Types\Arrays;

abstract class FormerObject
{
  /**
   * The FormerObject's attribute
   * @var array
   */
  protected $attributes = array();

  /**
   * The field value
   * @var string
   */
  protected $value;

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Dynamically set attributes
   *
   * @param  string $method     An attribute
   * @param  array  $parameters Its value(s)
   */
  public function __call($method, $parameters)
  {
    // Replace underscores
    $method = str_replace('_', '-', $method);

    // Get value and set it
    $value = Arrays::get($parameters, 0, 'true');
    $this->setAttribute($method, $value);

    return $this;
  }

  /**
   * Get a private attribute or a field attribute
   *
   * @param  string $attribute The desired attribute
   *
   * @return string            Its value
   */
  public function __get($attribute)
  {
    return Arrays::get($this->attributes, $attribute);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// GETTERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the object's name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Get the object's value
   *
   * @return mixed
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Get all of the Object's attributes
   *
   * @return array
   */
  public function getAttributes()
  {
    return $this->attributes;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// ATTRIBUTES ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set an attribute
   *
   * @param string $attribute An attribute
   * @param string $value     Its value
   */
  public function setAttribute($attribute, $value = null)
  {
    $this->attributes[$attribute] = $value;

    return $this;
  }

  /**
   * Set a bunch of parameters at once
   *
   * @param array $attributes The attributes to add to the existing ones
   *
   * @return FormerObject
   */
  public function setAttributes($attributes)
  {
    $this->attributes = array_merge($this->attributes, (array) $attributes);

    return $this;
  }

  /**
   * Replace all attributes with the provided array
   *
   * @param array $attributes The attributes to replace with
   *
   * @return FormerObject
   */
  public function replaceAttributes($attributes)
  {
    $this->attributes = (array) $attributes;

    return $this;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// OBJECT CLASSES //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Add one or more classes to the current field
   *
   * @param string $class The class to add
   */
  public function addClass($class)
  {
    if(is_array($class)) $class = implode(' ', $class);

    $this->attributes = Helpers::addClass($this->attributes, $class);

    return $this;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// OBJECT TYPE ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the object's type
   *
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Change a object's type
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  /**
   * Check if an object is of a certain type
   *
   * @param  string $types* The type(s) to check for
   * @return boolean
   */
  public function isOfType()
  {
    $types = func_get_args();

    return in_array($this->type, $types);
  }
}
