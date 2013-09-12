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
   * Form types that trigger special styling for this Framework
   *
   * @var array
   */
  protected $availableTypes = array('horizontal', 'vertical', 'inline', 'search');

  /**
   * The button types available
   *
   * @var array
   */
  private $buttons = array(
    'large', 'small', 'mini', 'block',
    'danger', 'info', 'inverse', 'link', 'primary', 'success', 'warning'
  );

  /**
   * The field sizes available
   *
   * @var array
   */
  private $fields = array(
    'mini', 'small', 'medium', 'large', 'xlarge', 'xxlarge',
    'span1', 'span2', 'span3', 'span4', 'span5', 'span6', 'span7',
    'span8', 'span9', 'span10', 'span11', 'span12'
  );

  /**
   * The field states available
   *
   * @var array
   */
  protected $states = array(
    'success', 'warning', 'error', 'info',
  );

  /**
   * The default HTML tag used for icons
   *
   * @var string
   */
  protected $iconTag = 'i';

  /**
   * The default group for icon fonts
   * By default Bootstrap uses images, so this isn't relevant
   * Font-Awesome up to version 3.2.1 doesn't it either
   *
   * @var string
   */
  protected $iconSet = null;

  /**
   * The default prefix icon names
   * "icon" works for Bootstrap 2 and Font-awesome
   *
   * @var string
   */
  protected $iconPrefix = 'icon';

  /**
   * Create a new TwitterBootstrap instance
   *
   * @param \Illuminate\Container\Container $app
   */
  public function __construct(Container $app)
  {
    $this->app = $app;
    $this->setIconDefaults();
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// FILTER ARRAYS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Filter buttons classes
   *
   * @param  array $classes An array of classes
   *
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
   *
   * @return array A filtered array
   */
  public function filterFieldClasses($classes)
  {
    // Filter classes
    $classes = array_intersect($classes, $this->fields);

    // Prepend field type
    $classes = Arrays::each($classes, function ($class) {
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
    return 'control-group';
  }

  /**
   * Add label classes
   *
   * @param  array $attributes An array of attributes
   *
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
   *
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
   *
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
   *
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
   * @param string $icon          The icon name
   * @param array  $attributes    Its general attributes
   * @param array  $iconSettings  Icon-specific settings
   *
   * @return string
   */
  public function createIcon($iconType, $attributes = array(), $iconSettings = array())
  {
    // Check for empty icons
    if (!$iconType) return false;

    //Create tag
    $tag = isset($iconSettings['tag']) ? $iconSettings['tag'] : $this->iconTag;
    $icon = Element::create($tag, null, $attributes);

    // White icons ignore user overrides to use legacy Bootstrap styling
    if (String::contains($iconType, 'white')) {
      $iconType = String::remove($iconType, 'white');
      $iconType = trim($iconType, '-');
      $icon->addClass('icon-white');
      $set = null;
      $prefix = 'icon';
    } else {
      $set = isset($iconSettings['set']) ? $iconSettings['set'] : $this->iconSet;
      $prefix = isset($iconSettings['prefix']) ? $iconSettings['prefix'] : $this->iconPrefix;
    }
    $icon->addClass("$set $prefix-$iconType");

    return $icon;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// WRAP BLOCKS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Wrap an item to be prepended or appended to the current field
   *
   * @param  string $item
   *
   * @return string A wrapped item
   */
  public function placeAround($item)
  {
    return Element::create('span', $item)->addClass('add-on');
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
    $class = array();
    if ($prepend) $class[] = 'input-prepend';
    if ($append) $class[] = 'input-append';

    $return = '<div class="'.join(' ', $class).'">';
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
   *
   * @return string A wrapped field
   */
  public function wrapField($field)
  {
    return Element::create('div', $field)->addClass('controls');
  }

  /**
   * Wrap actions block with potential additional tags
   *
   * @param  Actions $action
   * @return string A wrapped actions block
   */
  public function wrapActions($actions)
  {
      return $actions;
  }

}
