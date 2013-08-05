<?php
namespace Former\Framework;

use Former\Interfaces\FrameworkInterface;
use Former\Traits\Field;
use Former\Traits\Framework;
use HtmlObject\Element;
use HtmlObject\Input;
use Illuminate\Container\Container;

/**
 * The Zurb Foundation 4 form framework
 */
class ZurbFoundation4 extends Framework implements FrameworkInterface
{

  /**
   * The button types available
   *
   * @var array
   */
  private $buttons = array(
    'tiny', 'small', 'medium', 'large', 'success', 'radius', 'round', 'disabled', 'prefix', 'postfix',
  );

  /**
   * The field sizes available
   * Zurb Foundation 4 does not apply sizes to the form element, but to the wrapper div
   *
   * @var array
   */
  private $fields = array();

  /**
   * The field states available
   *
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
    // Filter classes
    $classes = array_intersect($classes, $this->buttons);
    $classes[] = 'button';

    return $classes;
  }

  public function filterFieldClasses($classes)
  {
    return null;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// ADD CLASSES //////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function getFieldClasses(Field $field, $classes = array())
  {
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

  public function getGroupClasses()
  {
    return null;
  }

  public function getLabelClasses()
  {
    return null;
  }

  public function getUneditableClasses()
  {
    return null;
  }

  public function getFormClasses($type)
  {
    return null;
  }

  public function getActionClasses()
  {
    return null;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// RENDER BLOCKS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function createHelp($text, $attributes = '')
  {
    return Element::create('small', $text, $attributes);
  }

  public function createIcon($icon, $attributes = '')
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

    return Input::create('text', $field->getName(), $field->getValue(), $field->getAttributes());
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// WRAP BLOCKS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Wrap an item to be prepended or appended to the current field.
   * For Zurb we return the item and handle the wrapping in prependAppend
   * as wrapping is dependent on whether we're prepending or appending.
   *
   * @param  string $field
   *
   * @return string A wrapped item
   */
  public function placeAround($item)
  {
    return $item;
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
    $return = '';

    foreach ($prepend as $item) {
      $return .= '<div class="large-2 small-3 columns"><span class="prefix">'.$item.'</span></div>';
    }

    $return .= '<div class="large-10 small-9 columns">'.$field->render().'</div>';

    foreach ($append as $item) {
      $return .= '<div class="large-2 small-3 columns"><span class="postfix">'.$item.'</span></div>';
    }

    return $return;
  }

  /**
   * Wraps all field contents with potential additional tags.
   *
   * @param  Field $field
   *
   * @return string A wrapped field
   */
  public function wrapField($field)
  {
    return Element::create('div', $field)->addClass('row collapse');
  }

}
