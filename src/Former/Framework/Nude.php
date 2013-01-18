<?php
/**
 * Nude
 *
 * Base HTML5 forms
 */
namespace Former\Framework;

use \Illuminate\Container\Container;
use \Former\Interfaces\FrameworkInterface;
use \Former\Traits\Field;
use \Former\Traits\Framework;

class Nude extends Framework implements FrameworkInterface
{
  /**
   * Create a new Nude instance
   *
   * @param \Illuminate\Container\Container $app
   */
  public function __construct(Container $app)
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

  public function addActionClasses($attributes)
  {
    return $attributes;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// RENDER BLOCKS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Create an help text
   */
  public function createHelp($text, $attributes)
  {
    return '<small'.$this->app['html']->attributes($attributes).'>'.$text.'</small>';
  }

  /**
   * Creates a basic icon
   */
  public function createIcon($icon, $attributes)
  {
    $attributes = $this->addClass($attributes, $icon);

    return '<i'.$this->attributes($attributes).'></i>';
  }

  /**
   * Render a disabled field
   *
   * @param Field $field
   *
   * @return string
   */
  public function createDisabledField(Field $field)
  {
    $field->disabled();

    return $this->app['form']->input('text', $field->getName(), $field->getValue(), $field->getAttributes());
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// WRAP BLOCKS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function wrapField($field)
  {
    return $field;
  }
}
