<?php
/**
 * ZurbFoundation
 *
 * The Zurb Foundation form framework
 */
namespace Former\Framework;

use \Illuminate\Container\Container;
use \Former\Interfaces\FrameworkInterface;
use \Former\Traits\Field;
use \Former\Traits\Framework;
use \Underscore\Types\Arrays;

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
    // Filter classes
    $classes = Arrays::intersect($classes, $this->fields);

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

  public function createHelp($text, $attributes)
  {
    return '<small'.$this->attributes($attributes).'>'.$text.'</small>';
  }

  public function createIcon($icon, $attributes)
  {
    return $icon;
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
