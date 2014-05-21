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
   * Form types that trigger special styling for this Framework
   *
   * @var array
   */
  protected $availableTypes = array('horizontal', 'vertical');

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
    $this->setFrameworkDefaults();
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
  ///////////////////// EXPOSE FRAMEWORK SPECIFICS ///////////////////
  ////////////////////////////////////////////////////////////////////

  protected function setFieldWidths($labelWidths)
  {
    $labelWidthClass = $fieldWidthClass = $fieldOffsetClass = '';

    $viewports = $this->getFrameworkOption('viewports');

    foreach ($labelWidths as $viewport => $columns) {
      if ($viewport) {
        $labelWidthClass .= $viewports[$viewport].'-'.$columns.' ';
        $fieldWidthClass .= $viewports[$viewport].'-'.(12-$columns).' ';
        $fieldOffsetClass .= $viewports[$viewport].'-offset-'.$columns.' ';
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
    if ($field->isButton()) {
      $classes = $this->filterButtonClasses($classes);
    } else {
      $classes = $this->filterFieldClasses($classes);
    }

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
      $return .= '<div class="large-2 small-3 columns"><span class="prefix">'.$item.'</span></div>';
    }

    $return .= '<div class="large-10 small-9 columns">'.$field->render().'</div>';

    foreach ($append as $item) {
      $return .= '<div class="large-2 small-3 columns"><span class="postfix">'.$item.'</span></div>';
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
      return $actions;
  }

}
