<?php
namespace Former\Framework;

use Former\Interfaces\FrameworkInterface;
use Former\Traits\Field;
use Former\Traits\Framework;
use HtmlObject\Element;
use Illuminate\Container\Container;
use Underscore\Methods\ArraysMethods as Arrays;
use Underscore\Methods\StringMethods as String;

/**
 * The Twitter Bootstrap form framework
 */
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
    // $classes = array_intersect($classes, $this->buttons);

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
    $classes = array_intersect($classes, $this->fields);

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
  public function getFieldClasses(Field $field, $classes)
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
      $field->class(implode(' ', $classes));
    }

    return $field;
  }

  /**
   * Add group classes
   *
   * @return string A list of group classes
   */
  public function getGroupClasses()
  {
    return 'control-group';
  }

  /**
   * Add label classes
   *
   * @param  array $attributes An array of attributes
   * @return array An array of attributes with the label class
   */
  public function getLabelClasses()
  {
    return 'control-label';
  }

  /**
   * Add uneditable field classes
   *
   * @param  array $attributes The attributes
   * @return array An array of attributes with the uneditable class
   */
  public function getUneditableClasses()
  {
    return 'uneditable-input';
  }

  /**
   * Add form class
   *
   * @param  array  $attributes The attributes
   * @param  string $type       The type of form to add
   * @return array
   */
  public function getFormClasses($type)
  {
    return $type ? 'form-'.$type : null;
  }

  /**
   * Add actions block class
   *
   * @param  array  $attributes The attributes
   * @return array
   */
  public function getActionClasses()
  {
    return 'form-actions';
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
    return Element::create('span', $text, $attributes)->addClass('help-inline');
  }

  /**
   * Render a block help text
   *
   * @param string $text
   * @param array  $attributes
   *
   * @return string
   */
  public function createBlockHelp($text, $attributes = array())
  {
    return Element::create('p', $text, $attributes)->addClass('help-block');
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
    return Element::create('span', $field->getValue(), $field->getAttributes());
  }

  /**
   * Render an icon
   *
   * @param string $icon       The icon name
   * @param array  $attributes Its attributes
   *
   * @return string
   */
  public function createIcon($iconType, $attributes = array())
  {
    $icon = Element::create('i', null, $attributes);

    // White icon
    if (String::contains($iconType, 'white')) {
      $iconType = String::remove($iconType, 'white');
      $iconType = trim($iconType, '-');
      $icon->addClass('icon-white');
    }

    // Check for empty icons
    if (!$iconType) return false;

    // Create icon
    $icon->addClass('icon-'.$iconType);

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
    return Element::create('div', $field)->addClass('controls');
  }

}
