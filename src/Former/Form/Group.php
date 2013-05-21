<?php
namespace Former\Form;

use BadMethodCallException;
use Former\Former;
use Former\Helpers;
use HtmlObject\Element;
use HtmlObject\Traits\Tag;
use Underscore\Methods\ArraysMethods as Arrays;
use Underscore\Methods\StringMethods as String;

/**
 * Helper class to build groups
 */
class Group extends Tag
{

  /**
   * The Former instance
   *
   * @var Former
   */
  protected $former;

  /**
   * The current state of the group
   *
   * @var string
   */
  protected $state = null;

  /**
   * Whether the field should be displayed raw or not
   *
   * @var boolean
   */
  protected $raw = false;

  /**
   * The group label
   *
   * @var Element
   */
  protected $label;

  /**
   * The group help
   *
   * @var string
   */
  protected $help = null;

  /**
   * An array of elements to preprend the field
   *
   * @var array
   */
  protected $prepend = array();

  /**
   * An array of elements to append the field
   *
   * @var array
   */
  protected $append = array();

  /**
   * The group's element
   *
   * @var string
   */
  protected $element = 'div';

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Creates a group
   *
   * @param Former    $former     The Former instance
   * @param string    $label      Its label
   * @param array     $attributes Attributes
   */
  public function __construct(Former $former, $label, $attributes = array())
  {
    // Get special classes
    $this->former = $former;
    $this->addClass($this->former->getFramework()->getGroupClasses());

    // Set group label
    if ($label) $this->setLabel($label);
  }

  /**
   * Prints out the opening of the Control Group
   *
   * @return string A control group opening tag
   */
  public function __toString()
  {
    return $this->open().$this->getFormattedLabel();
  }

  /**
   * Opens a group
   *
   * @return string Opening tag
   */
  public function open()
  {
    // If any errors, set state to errors
    $errors = $this->former->getErrors();
    if($errors) $this->state('error');

    // Retrieve state and append it to classes
    if ($this->state) {
      $this->addClass($this->state);
    }

    // Required state
    if ($this->former->field() and $this->former->field()->isRequired()) {
      $this->addClass($this->former->getOption('required_class'));
    }

    return parent::open();
  }

  /**
   * Set the contents of the current group
   *
   * @param string $contents The group contents
   *
   * @return string A group
   */
  public function contents($contents)
  {
    return $this->wrap($contents, $this->getFormattedLabel());
  }

  /**
   * Wrap a Field with the current group
   *
   * @param  Field  $field A Field instance
   * @return string        A group
   */
  public function wrapField($field)
  {
    $label  = $this->getLabel($field);
    $field  = $this->prependAppend($field);
    $field .= $this->getHelp();

    return $this->wrap($field, $label);
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// FIELD METHODS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set the state of the group
   *
   * @param  string $state A Bootstrap state class
   */
  public function state($state)
  {
    // Filter state
    $state = $this->former->getFramework()->filterState($state);

    $this->state = $state;
  }

  /**
   * Set a class on the Group
   *
   * @param string $class The class to add
   */
  public function addGroupClass($class)
  {
    $this->addClass($class);
  }

  /**
   * Adds a label to the group
   *
   * @param  string $label A label
   */
  public function setLabel($label)
  {
    if (!($label instanceof Element)) {
      $label = Element::create('label', $label)->for($label);
    }

    $this->label = $label;
  }

  /**
   * Get the formatted group label
   *
   * @return string
   */
  public function getFormattedLabel()
  {
    if (!$this->label) return false;
    return $this->label->addClass($this->former->getFramework()->getLabelClasses());
  }

  /**
   * Disables the control group for the current field
   */
  public function raw()
  {
    $this->raw = true;
  }

  /**
   * Check if the current group is to be displayed or not
   *
   * @return boolean
   */
  public function isRaw()
  {
    return $this->raw == true;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// HELP BLOCKS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Alias for inlineHelp
   *
   * @param  string $help       The help text
   * @param  array  $attributes Facultative attributes
   */
  public function help($help, $attributes = array())
  {
    return $this->inlineHelp($help, $attributes);
  }

  /**
   * Add an inline help
   *
   * @param  string $help       The help text
   * @param  array  $attributes Facultative attributes
   */
  public function inlineHelp($help, $attributes = array())
  {
    // Attempt to translate help text
    $help = Helpers::translate($help);

    // If no help text, do nothing
    if (!$help) return false;

    $this->help['inline'] = $this->former->getFramework()->createHelp($help, $attributes);
  }

  /**
   * Add an block help
   *
   * @param  string $help       The help text
   * @param  array  $attributes Facultative attributes
   */
  public function blockHelp($help, $attributes = array())
  {
    // Reserved method
    if ($this->former->getFramework()->isnt('TwitterBootstrap')) {
      throw new BadMethodCallException('This method is only available on the Bootstrap framework');
    }

    // Attempt to translate help text
    $help = Helpers::translate($help);

    // If no help text, do nothing
    if (!$help) return false;

    $this->help['block'] = $this->former->getFramework()->createBlockHelp($help, $attributes);
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////// PREPEND/APPEND METHODS ///////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Prepend elements to the field
   */
  public function prepend()
  {
    $this->placeAround(func_get_args(), 'prepend');
  }

  /**
   * Append elements to the field
   */
  public function append()
  {
    $this->placeAround(func_get_args(), 'append');
  }

  /**
   * Prepends an icon to a field
   *
   * @param string $icon       The icon to prepend
   * @param array  $attributes Its attributes
   */
  public function prependIcon($icon, $attributes = array())
  {
    $icon = $this->former->getFramework()->createIcon($icon, $attributes);

    $this->prepend($icon);
  }

  /**
   * Append an icon to a field
   *
   * @param string $icon       The icon to prepend
   * @param array  $attributes Its attributes
   */
  public function appendIcon($icon, $attributes = array())
  {
    $icon = $this->former->getFramework()->createIcon($icon, $attributes);

    $this->append($icon);
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// HELPERS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Wraps content in a group
   *
   * @param mixed  $contents The content
   * @param string $label    The label to add
   *
   * @return string A group
   */
  public function wrap($contents, $label = null)
  {
    $group = $this->open();
      $group .= $label;
      $group .= $this->former->getFramework()->wrapField($contents);
    $group .= $this->close();

    return $group;
  }

  /**
   * Prints out the current label
   *
   * @param  string $field The field to create a label for
   * @return string        A <label> tag
   */
  protected function getLabel($field = null)
  {
    // Don't create a label if none exist
    if (!$field or !$this->label) return null;

    // Wrap label in framework classes
    $this->label->addClass($this->former->getFramework()->getLabelClasses());
    $this->label = $this->former->getFramework()->createLabelOf($field, $this->label);

    return $this->label;
  }

  /**
   * Prints out the current help
   *
   * @return string A .help-block or .help-inline
   */
  protected function getHelp()
  {
    $inline = Arrays::get($this->help, 'inline');
    $block  = Arrays::get($this->help, 'block');

    // Replace help text with error if any found
    $errors = $this->former->getErrors();
    if ($errors and $this->former->getOption('error_messages')) {
      $inline = $this->former->getFramework()->createHelp($errors);
    }

    return join(null, array($inline, $block));
  }

  /**
   * Format the field with prepended/appended elements
   *
   * @param  string $field The field to format
   * @return string        Field plus supplementary elements
   */
  protected function prependAppend($field)
  {
    if(!$this->prepend and !$this->append) return $field->render();

    // Prepare wrapping div
    $class = null;
    if($this->prepend) $class  = 'input-prepend';
    if($this->append)  $class .= ' input-append';

    // Build div
    $return = '<div class="' .$class. '">';
      $return .= join(null, $this->prepend);
      $return .= $field->render();
      $return .= join(null, $this->append);
    $return .= '</div>';

    return $return;
  }

  /**
   * Place elements around the field
   *
   * @param  array  $items An array of items to place
   * @param  string $place Where they should end up (prepend|append)
   */
  protected function placeAround($items, $place)
  {
    // Iterate over the items and place them where they should
    foreach ((array) $items as $item) {

      // Render the item if it's an object
      if (is_object($item) and method_exists($item, '__toString')) {
        $item  = $item->__toString();
      }

      // If the item is not a button, wrap it
      if (is_string($item) and !String::startsWith($item, '<button')) {
        $item = '<span class="add-on">'.$item.'</span>';
      }

      $this->{$place}[] = $item;
    }
  }

}
