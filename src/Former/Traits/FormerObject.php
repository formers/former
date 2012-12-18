<?php
/**
 * FormerObject
 *
 * Base Former object that allows chained attributes setting, adding
 * classes to the existing ones, and provide types helpers
 */
namespace Former\Traits;

use \Underscore\Arrays;

abstract class FormerObject
{
  /**
   * The Form open tag's attribute
   * @var array
   */
  protected $attributes = array();

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
   * Get a Field variable or an attribute
   *
   * @param  string $attribute The desired attribute
   * @return string            Its value
   */
  public function __get($attribute)
  {
    if(isset($this->$attribute)) return $this->$attribute;
    else return Arrays::get($this->attributes, $attribute);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// ATTRIBUTES //////////////////////////////
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
   * @param array   $attributes An associative array of attributes
   * @param boolean $merge      Whether they should be merged to the old ones
   */
  public function setAttributes($attributes, $merge = true)
  {
    $attributes = (array) $attributes;

    $this->attributes = $merge
      ? array_merge($this->attributes, $attributes)
      : $attributes;

    return $this;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// OBJECT CLASSES //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Add a class to the current field
   *
   * @param string $class The class to add
   */
  public function addClass($class)
  {
    if(is_array($class)) $class = implode(' ', $class);

    $this->attributes = $this->app['former.helpers']->addClass($this->attributes, $class);

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
   * Check if a object is of a certain type
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
