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
class TwitterBootstrap3 extends Framework implements FrameworkInterface
{

  /**
   * The button types available
   * @var array
   */
  private $buttons = array(
    'large', 'small', 'block', 'link',
    'default', 'primary', 'warning',  'danger', 'success', 'info',
  );

  /**
   * The field sizes available
   * @var array
   */
  private $fields = array(
    'col-1', 'col-2', 'col-3', 'col-4', 'col-5', 'col-6',
    'col-7', 'col-8', 'col-9', 'col-10', 'col-11', 'col-12',
    'col-sm-1', 'col-sm-2', 'col-sm-3', 'col-sm-4', 'col-sm-5', 'col-sm-6',
    'col-sm-7', 'col-sm-8', 'col-sm-9', 'col-sm-10', 'col-sm-11', 'col-sm-12',
    'col-lg-1', 'col-lg-2', 'col-lg-3', 'col-lg-4', 'col-lg-5', 'col-lg-6',
    'col-lg-7', 'col-lg-8', 'col-lg-9', 'col-lg-10', 'col-lg-11', 'col-lg-12',
  );

  /**
   * The field states available
   * @var array
   */
  protected $states = array(
    'has-warning', 'has-error', 'has-success',
  );

  protected $labelWidth = 'col-2';

  protected $fieldWidth = 'col-10';

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
      return String::startsWith($class, 'col') ? $class : 'input-'.$class;
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
    if ($field->isButton()) {
      $classes = $this->filterButtonClasses($classes);
    } else {
      $classes = $this->filterFieldClasses($classes);
    }

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
    return 'form-group';
  }

  /**
   * Add label classes
   *
   * @param  array $attributes An array of attributes
   * @return array An array of attributes with the label class
   */
  public function getLabelClasses()
  {
    return '';
  }

  /**
   * Add uneditable field classes
   *
   * @param  array $attributes The attributes
   * @return array An array of attributes with the uneditable class
   */
  public function getUneditableClasses()
  {
    return '';
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
    return '';
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
    return Element::create('span', $text, $attributes)->addClass('help-block');
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
    if (!$iconType) return false;
    return Element::create('span', null, $attributes)->addClass('glyphicon glyphicon-'.$iconType);
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// WRAP BLOCKS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Wrap an item to be prepended or appended to the current field
   *
   * @param  string $field
   *
   * @return string A wrapped item
   */
  public function placeAround($item)
  {
    return Element::create('span', $item)->addClass('input-group-addon');
  }

  /**
   * Wrap a field with prepended and appended items
   *
   * @param  Field $field
   * @param  array $prepend
   * @param  array $append
   *
   * @return string A field concatented with prepended and/or appended items
   */
  public function prependAppend($field, $prepend, $append)
  {
    $return = '<div class="input-group">';
      $return .= join(null, $prepend);
      $return .= $field->render();
      $return .= join(null, $append);
    $return .= '</div>';

    return $return;
  }

  /**
   * Wrap a field with potential additional tags
   *
   * @param  Field $field
   * @return string A wrapped field
   */
  public function wrapField($field)
  {
    return $field;
  }

}
