<?php
namespace Former\Framework;

use \Underscore\Arrays;

class ZurbFoundation extends Framework implements FrameworkInterface
{

  /**
   * The field sizes available
   * @var array
   */
  private $fields = array(
    'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight',
    'nine', 'ten', 'eleven', 'twelve'
  );

  /**
   * The field states available
   * @var array
   */
  protected $states = array(
    'error',
  );

  /**
   * Create a new ZurbFoundation instance
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
    // Filter classes
    $classes = Arrays::intersect($classes, $this->fields);

    return $classes;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// ADD CLASSES //////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function addGroupClasses($attributes)
  {
    return $attributes;
  }

  public function addLabelClasses($attributes)
  {
    return $attributes;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// RENDER BLOCKS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function createHelp($text, $attributes)
  {
    return '<small'.$this->attributes($attributes).'>'.$text.'</small>';
  }

  public function createIcon($icon, $attributes)
  {
    return $icon;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// WRAP BLOCKS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function wrapField($field)
  {
    return $field;
  }
}
