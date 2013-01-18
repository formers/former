<?php
/**
 * Populator
 *
 * Populates the class with values, and fetches them
 * from various places
 */
namespace Former;

use \Underscore\Types\Arrays;
use \Underscore\Types\String;

class Populator
{
  /**
   * The populated values
   * @var array
   */
  private $values = array();

  ////////////////////////////////////////////////////////////////////
  //////////////////////// INDIVIDUAL VALUES /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set the value of a particular field
   *
   * @param string $field The field's name
   * @param mixed  $value Its new value
   */
  public function setValue($field, $value)
  {
    if (is_object($this->values)) {
      $this->values->$field = $value;
    } else {
      $this->values[$field] = $value;
    }
  }

  /**
   * Get the value of a field
   *
   * @param string $field The field's name
   *
   * @return mixed
   */
  public function getValue($field, $fallback = null)
  {
    // Plain array
    if (is_array($this->values)) return Arrays::get($this->values, $field, $fallback);

    // Transform the name into an array
    $value = $this->values;
    $field = String::contains($field, '.') ? explode('.', $field) : (array) $field;

    // Dive into the model
    foreach ($field as $r) {

      // Multiple results relation
      if (is_array($value)) {
        foreach ($value as $subkey => $submodel) {
          $value[$subkey] = isset($submodel->$r) ? $submodel->$r : $fallback;
        }
        continue;
      }

      // Single model relation
      if(isset($value->$r) or method_exists($value, 'get'.ucfirst($r))) $value = $value->$r;
      else {
        $value = $fallback;
        break;
      }
    }

    return $value;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// SWAPPERS //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Replace the values array
   *
   * @param  mixed $values The new values
   * @return void
   */
  public function populateWith($values)
  {
    $this->values = $values;
  }

  /**
   * Reset the current values array
   *
   * @return void
   */
  public function reset()
  {
    $this->values = array();
  }
}
