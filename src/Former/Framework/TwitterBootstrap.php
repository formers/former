<?php
/**
 * TwitterBootstrap
 *
 * The Twitter Bootstrap form framework
 */
namespace Former\Framework;

use \Former\Interfaces\FrameworkInterface;
use \Former\Traits\Field;
use \Former\Traits\Framework;
use \Illuminate\Container\Container;
use \Underscore\Types\Arrays;
use \Underscore\Types\String;

class TwitterBootstrap extends Framework implements FrameworkInterface
{
  /**
   * The button types available
   * @var array
   */
  private $buttons = array(
    'large', 'small', 'mini', 'block',
    'danger', 'info', 'inverse', 'link', 'primary', 'success', 'warning'
  );

  /**
   * The field sizes available
   * @var array
   */
  private $fields = array(
    'mini', 'small', 'medium', 'large', 'xlarge', 'xxlarge',
    'span1', 'span2', 'span3', 'span4', 'span5', 'span6', 'span7',
    'span8', 'span9', 'span10', 'span11', 'span12'
  );

  /**
   * The field states available
   * @var array
   */
  protected $states = array(
    'success', 'warning', 'error', 'info',
  );

  /**
   * Create a new TwitterBootstrap instance
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

  /**
   * Filter buttons classes
   *
   * @param  array $classes An array of classes
   * @return array A filtered array
   */
  public function filterButtonClasses($classes)
  {
    // Filter classes
    $classes = Arrays::intersect($classes, $this->buttons);

    // Prepend button type
    $classes = $this->prependWith($classes, 'btn-');
    $classes[] = 'btn';

    return $classes;
  }

  /**
   * Filter field classes
   *
   * @param  array $classes An array of classes
   * @return array A filtered array
   */
  public function filterFieldClasses($classes)
  {
    // Filter classes
    $classes = Arrays::intersect($classes, $this->fields);

    // Prepend field type
    $classes = Arrays::each($classes, function($class) {
      return String::startsWith($class, 'span') ? $class : 'input-'.$class;
    });

    return $classes;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// ADD CLASSES //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Add classes to a field
   *
   * @param Field $field
   * @param array $classes The possible classes to add
   *
   * @return Field
   */
  public function addFieldClasses(Field $field, $classes)
  {
    // Add inline class for checkables
    if ($field->isCheckable() and in_array('inline', $classes)) {
      $field->inline();
    }

    // Filter classes according to field type
    if ($field->isButton()) $classes = $this->filterButtonClasses($classes);
    else $classes = $this->filterFieldClasses($classes);

    // If we found any class, add them
    if ($classes) {
      $field->setAttribute('class', implode(' ', $classes));
    }

    return $field;
  }

  /**
   * Add group classes
   *
   * @param  array $attributes An array of attributes
   * @return array An array of attributes with the group class
   */
  public function addGroupClasses($attributes)
  {
    $attributes = $this->addClass($attributes, 'control-group');

    return $attributes;
  }

  /**
   * Add label classes
   *
   * @param  array $attributes An array of attributes
   * @return array An array of attributes with the label class
   */
  public function addLabelClasses($attributes)
  {
    $attributes = $this->addClass($attributes, 'control-label');

    return $attributes;
  }

  /**
   * Add uneditable field classes
   *
   * @param  array $attributes The attributes
   * @return array An array of attributes with the uneditable class
   */
  public function addUneditableClasses($attributes)
  {
    $attributes = $this->addClass($attributes, 'uneditable-input');

    return $attributes;
  }

  /**
   * Add form class
   *
   * @param  array  $attributes The attributes
   * @param  string $type       The type of form to add
   * @return array
   */
  public function addFormClasses($attributes, $type)
  {
    $attributes = $this->addClass($attributes, 'form-'.$type);

    return $attributes;
  }

  /**
   * Add actions block class
   *
   * @param  array  $attributes The attributes
   * @return array
   */
  public function addActionClasses($attributes)
  {
    $attributes = $this->addClass($attributes, 'form-actions');

    return $attributes;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// RENDER BLOCKS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Render an help text
   *
   * @param string $text
   * @param array  $attributes
   *
   * @return string
   */
  public function createHelp($text, $attributes = array())
  {
    // Add class
    $attributes = $this->addClass($attributes, 'help-inline');

    return '<span'.$this->attributes($attributes).'>'.$text.'</span>';
  }

  /**
   * Render a block help text
   *
   * @param string $text
   * @param array  $attributes
   *
   * @return string
   */
  public function createBlockHelp($text, $attributes, $attributes = array())
  {
    // Add class
    $attributes = $this->addClass($attributes, 'help-block');

    return '<p'.$this->attributes($attributes).'>'.$text.'</p>';
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
    return '<span'.$this->attributes($field->getAttributes()).'>'.$field->getValue().'</span>';
  }

  /**
   * Render an icon
   *
   * @param string $icon       The icon name
   * @param array  $attributes Its attributes
   *
   * @return string
   */
  public function createIcon($icon, $attributes, $attributes = array())
  {
    // White icon
    if (String::contains($icon, 'white')) {
      $icon = String::remove($icon, 'white');
      $icon = trim($icon, '-');
      $attributes = $this->addClass($attributes, 'icon-white');
    }

    // Check for empty icons
    if (!$icon) return false;

    // Create icon
    $attributes = $this->addClass($attributes, 'icon-'.$icon);
    $icon = '<i'.$this->attributes($attributes).'></i>';

    return $icon;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// WRAP BLOCKS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Wrap a field with potential additional tags
   *
   * @param  Field $field
   * @return string A wrapped field
   */
  public function wrapField($field)
  {
    return '<div class="controls">' .$field. '</div>';
  }
}
