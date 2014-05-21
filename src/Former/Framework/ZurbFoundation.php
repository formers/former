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
   * Form types that trigger special styling for this Framework
   *
   * @var array
   */
  protected $availableTypes = array('horizontal', 'vertical');

  /**
   * The field sizes available
   *
   * @var array
   */
  private $fields = array(
    1 => 'one',
    2 => 'two',
    3 => 'three',
    4 => 'four',
    5 => 'five',
    6 => 'six',
    7 => 'seven',
    8 => 'eight',
    9 => 'nine',
    10 => 'ten',
    11 => 'eleven',
    12 => 'twelve'
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
    $this->setFrameworkDefaults();
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
  ///////////////////// EXPOSE FRAMEWORK SPECIFICS ///////////////////
  ////////////////////////////////////////////////////////////////////

  protected function setFieldWidths($labelWidths)
  {
    $labelWidthClass = $fieldWidthClass = $fieldOffsetClass = '';

    $viewports = $this->getFrameworkOption('viewports');

    foreach ($labelWidths as $viewport => $columns) {
      if ($viewport) {
        $labelWidthClass .= $viewports[$viewport].$this->fields[$columns].' ';
        $fieldWidthClass .= $viewports[$viewport].$this->fields[12-$columns].' ';
        $fieldOffsetClass .= $viewports[$viewport].'offset-by-'.$this->fields[$columns].' ';
      }
    }

    $this->labelWidth = $labelWidthClass . 'columns';
    $this->fieldWidth = $fieldWidthClass . 'columns';
    $this->fieldOffset = $fieldOffsetClass . 'columns';
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// ADD CLASSES //////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function getFieldClasses(Field $field, $classes = array())
  {
    $classes = $this->filterFieldClasses($classes);

    return $this->addClassesToField($field, $classes);
  }

  public function getGroupClasses()
  {
    if ($this->app['former.form']->isOfType('horizontal')) {
      return 'row';
    } else {
      return null;
    }
  }

  /**
   * Add label classes
   *
   * @param  array $attributes An array of attributes
   * @return array An array of attributes with the label class
   */
  public function getLabelClasses()
  {
    if ($this->app['former.form']->isOfType('horizontal')) {
      return $this->getFrameworkOption('wrappedLabelClasses');
    } else {
      return null;
    }
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

  public function createHelp($text, $attributes = null)
  {
    if (is_null($attributes) or empty($attributes)) {
        $attributes = $this->getFrameworkOption('error_classes');
    }
    return Element::create('span', $text, $attributes);
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
   * Wraps all label contents with potential additional tags.
   *
   * @param  string $label
   *
   * @return string A wrapped label
   */
  public function wrapLabel($label)
  {
    if ($this->app['former.form']->isOfType('horizontal')) {
      return Element::create('div', $label)->addClass($this->labelWidth);
    } else {
      return $label;
    }
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
    if ($this->app['former.form']->isOfType('horizontal')) {
      return Element::create('div', $field)->addClass($this->fieldWidth);
    } else {
      return $field;
    }
  }

  /**
   * Wrap actions block with potential additional tags
   *
   * @param  Actions $action
   * @return string A wrapped actions block
   */
  public function wrapActions($actions)
  {
    if ($this->app['former.form']->isOfType('horizontal')) {
      return Element::create('div', $actions)->addClass(array($this->fieldOffset,$this->fieldWidth));
    } else {
      return $actions;
    }
  }

}
