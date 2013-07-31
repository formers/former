<?php
namespace Former\Framework;

use Former\Interfaces\FrameworkInterface;
use Former\Traits\Field;
use Former\Traits\Framework;
use HtmlObject\Element;
use HtmlObject\Input;
use Illuminate\Container\Container;

/**
 * The Zurb Foundation form framework
 */
class ZurbFoundation extends Framework implements FrameworkInterface
{

  /**
   * The field sizes available
   *
   * @var array
   */
  private $fields = array(
    'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight',
    'nine', 'ten', 'eleven', 'twelve'
  );

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
   * @param Container $app
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
    $classes = array_intersect($classes, $this->fields);

    return $classes;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// ADD CLASSES //////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function getFieldClasses(Field $field, $classes = array())
  {
    $classes = $this->filterFieldClasses($classes);

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

  public function createHelp($text, $attributes = array())
  {
    return Element::create('small', $text, $attributes);
  }

  public function createIcon($icon, $attributes = array())
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
      $return .= '<div class="two mobile-one columns"><span class="prefix">'.$item.'</span></div>';
    }

    $return .= '<div class="ten mobile-three columns">'.$field->render().'</div>';

    foreach ($append as $item) {
      $return .= '<div class="two mobile-one columns"><span class="postfix">'.$item.'</span></div>';
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
