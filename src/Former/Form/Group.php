<?php
/**
 * Group
 *
 * Helper class to build groups
 */
namespace Former\Form;

use \Former\Helpers;
use \BadMethodCallException;
use \Underscore\Types\Arrays;
use \Underscore\Types\String;

class Group
{
  protected $app;

  /**
   * The group attributes
   * @var array
   */
  private $attributes = array();

  /**
   * The current state of the group
   * @var string
   */
  private $state = null;

  /**
   * Whether the field should be displayed raw or not
   * @var boolean
   */
  private $raw = false;

  /**
   * The group label
   * @var string
   */
  private $label = array(
    'text'       => null,
    'attributes' => array()
  );

  /**
   * The group help
   * @var string
   */
  private $help = null;

  /**
   * An array of elements to preprend the field
   * @var array
   */
  private $prepend = array();

  /**
   * An array of elements to append the field
   * @var array
   */
  private $append = array();

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Creates a group
   *
   * @param string $label Its label
   */
  public function __construct($app, $label, $attributes = array())
  {
    // Get special classes
    $this->app = $app;
    $this->attributes = $this->app['former']->getFramework()->addGroupClasses($attributes);

    // Set group label
    $this->setLabel($label);

    return $this;
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
  private function open()
  {
    // If any errors, set state to errors
    $errors = $this->app['former']->getErrors();
    if($errors) $this->state('error');

    // Retrieve state and append it to classes
    if ($this->state) {
      $this->attributes = Helpers::addClass($this->attributes, $this->state);
    }

    // Required state
    if ($this->app['former']->field() and $this->app['former']->field()->isRequired()) {
      $this->attributes = Helpers::addClass($this->attributes, $this->app['former']->getOption('required_class'));
    }

    return '<div'.$this->app['html']->attributes($this->attributes). '>';
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
   * Closes a group
   *
   * @return string Closing tag
   */
  private function close()
  {
    return '</div>';
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
    $state = $this->app['former']->getFramework()->filterState($state);

    $this->state = $state;
  }

  /**
   * Adds a label to the group
   *
   * @param  string $label A label
   */
  public function setLabel($label)
  {
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

    $attributes = $this->app['former']->getFramework()->addLabelClasses(array());
    $label = $this->app['form']->label($this->label, $this->label, $attributes);

    return $label;
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

    $this->help['inline'] = $this->app['former']->getFramework()->createHelp($help, $attributes);
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
    if ($this->app['former']->getFramework()->isnt('TwitterBootstrap')) {
      throw new BadMethodCallException('This method is only available on the Bootstrap framework');
    }

    // Attempt to translate help text
    $help = Helpers::translate($help);

    // If no help text, do nothing
    if (!$help) return false;

    $this->help['block'] = $this->app['former']->getFramework()->createBlockHelp($help, $attributes);
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
    $icon = $this->app['former']->getFramework()->createIcon($icon, $attributes);

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
    $icon = $this->app['former']->getFramework()->createIcon($icon, $attributes);

    $this->append($icon);
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// HELPERS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Wraps content in a group
   *
   * @param mixed $contents The content
   *
   * @return string A group
   */
  public function wrap($contents, $label = null)
  {
    $group = $this->open();
      $group .= $label;
      $group .= $this->app['former']->getFramework()->wrapField($contents);
    $group .= $this->close();

    return $group;
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

  /**
   * Prints out the current label
   *
   * @param  string $field The field to create a label for
   * @return string        A <label> tag
   */
  private function getLabel($field = null)
  {
    // Don't create a label if none exist
    if (!$field or !$this->label) return null;

    // Wrap label in framework classes
    $this->label['attributes'] = $this->app['former']->getFramework()->addLabelClasses($this->label['attributes']);
    $this->label = $this->app['former']->getFramework()->createLabelOf($field, $this->label);

    return $this->label;
  }

  /**
   * Prints out the current help
   *
   * @return string A .help-block or .help-inline
   */
  private function getHelp()
  {
    $inline = Arrays::get($this->help, 'inline');
    $block  = Arrays::get($this->help, 'block');

    $errors = $this->app['former']->getErrors();
    if ($errors) $inline = $this->app['former']->getFramework()->createHelp($errors);
    return join(null, array($inline, $block));
  }

  /**
   * Format the field with prepended/appended elements
   *
   * @param  string $field The field to format
   * @return string        Field plus supplementary elements
   */
  private function prependAppend($field)
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
  private function placeAround($items, $place)
  {
    // Iterate over the items and place them where they should
    foreach ((array) $items as $item) {

      // Render the item if it's an object
      if (is_object($item) and method_exists($item, '__toString')) {
        $item  = $item->__toString();
      }

      // If the item is not a button, wrap it
      if (!String::startsWith($item, '<button')) {
        $item = '<span class="add-on">'.$item.'</span>';
      }

      $this->{$place}[] = $item;
    }
  }
}
