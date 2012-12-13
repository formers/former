<?php
/**
 * Nude
 *
 * Base HTML5 forms
 */
namespace Former\Framework;

use \Former\Interfaces\FrameworkInterface;
use \Former\Traits\Field;
use \Former\Traits\Framework;

class Nude extends Framework implements FrameworkInterface
{
  /**
   * Create a new Nude instance
   *
   * @param \Illuminate\Container $app
   */
  public function __construct(\Illuminate\Container $app)
  {
    $this->app = $app;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// FILTER ARRAYS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function filterButtonClasses($classes)
  {
    return $classes;
  }

  public function filterFieldClasses($classes)
  {
    return $classes;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// ADD CLASSES //////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function addFieldClasses(Field $field, $classes = array())
  {
    $classes = $this->filterFieldClasses($classes);

    // If we found any class, add them
    if ($classes) {
      $field->setAttribute('class', implode(' ', $classes));
    }

    return $field;
  }

  public function addGroupClasses($attributes)
  {
    return $attributes;
  }

  public function addLabelClasses($attributes)
  {
    return $attributes;
  }

  public function addUneditableClasses($attributes)
  {
    return $attributes;
  }

  public function addFormClasses($attributes)
  {
    return $attributes;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// RENDER BLOCKS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function createHelp($text, $attributes)
  {
    return '<small'.$this->app['former.helpers']->attributes($attributes).'>'.$text.'</small>';
  }

  public function createIcon($icon, $attributes)
  {
    $attributes = $this->addClass($attributes, $icon);

    return '<i'.$this->attributes($attributes).'></i>';
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// WRAP BLOCKS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function wrapField($field)
  {
    return $field;
  }
}
