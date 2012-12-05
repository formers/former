<?php
/**
 * ControlGroup
 *
 * Helper class to build Bootstrap control-groups
 */
namespace Former;

use \Underscore\Arrays;

class ControlGroup
{
  protected $app;

  /**
   * The control group attributes
   * @var array
   */
  private $attributes = array();

  /**
   * The current state of the control group
   * @var string
   */
  private $state = null;

  /**
   * The control group label
   * @var string
   */
  private $label = array(
    'label'      => null,
    'attributes' => array()
  );

  /**
   * The control group help
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
  /////////////////////////////// BUILDERS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Creates a control group
   *
   * @param string $label Its label
   */
  public function __construct($app, $label)
  {
    // Get special classes
    $this->app = $app;
    $this->attributes = $this->app['former.framework']->getGroupClasses($this->attributes);

    // Set control group label
    $this->setLabel($label);

    return $this;
  }

  /**
   * Opens a control group
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
      $this->attributes = $this->app['former.helpers']->addClass($this->attributes, $this->state);
    }

    // Required state
    if ($this->app['former']->field()->isRequired()) {
      $this->attributes = $this->app['former.helpers']->addClass($this->attributes, $this->app['config']->get('former::required_class'));
    }

    return '<div'.$this->app['former.helpers']->attributes($this->attributes). '>';
  }

  /**
   * Prints out the current label
   *
   * @param  string $field The field to create a label for
   * @return string        A <label> tag
   */
  private function getLabel($field)
  {
    $this->label['attributes'] = $this->app['former.framework']->getLabelClasses($this->label['attributes']);

    return $this->app['former.framework']->label($field, $this->label);
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
    if ($errors) $inline = $this->app['former.framework']->inlineHelp($errors);
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
    if(!$this->prepend and !$this->append) return $field;

    // Prepare wrapping div
    $class = null;
    if($this->prepend) $class = 'input-prepend';
    if($this->append) $class .= ' input-append';

    // Build div
    $return = '<div class="' .$class. '">';
      $return .= join(null, $this->prepend);
      $return .= $field;
      $return .= join(null, $this->append);
    $return .= '</div>';

    return $return;
  }

  /**
   * Closes a control group
   *
   * @return string Closing tag
   */
  private function close()
  {
    return '</div>';
  }

  /**
   * Wrap a Field with the current control group
   *
   * @param  Field  $field A Field instance
   * @return string        A .control-group
   */
  public function wrapField($field)
  {
    $html = $this->open();
      $html  .= $this->getLabel($field);
      $field  = $this->prependAppend($field);
      $field .= $this->getHelp();
      $html  .= $this->app['former.framework']->getFieldClasses($field);
    $html .= $this->close();

    return $html;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////// PUBLIC INTERFACE //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set the state of the control group
   *
   * @param  string $state A Bootstrap state class
   */
  public function state($state)
  {
    // Filter state
    $state = $this->app['former.framework']->getState($state);
    if(!$state) return false;

    $this->state = $state;
  }

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
    // If no help text, do nothing
    if (empty($help)) return false;

    // Attempt to translate help text
    $help = $this->app['former.helpers']->translate($help);

    $this->help['inline'] = $this->app['former.framework']->inlineHelp($help, $attributes);
  }

  /**
   * Add an block help
   *
   * @param  string $help       The help text
   * @param  array  $attributes Facultative attributes
   */
  public function blockHelp($help, $attributes = array())
  {
    // If no help text, do nothing
    if (empty($help)) return false;

    // Attempt to translate help text
    $help = $this->app['former.helpers']->translate($help);

    $this->help['block'] = $this->app['former.framework']->blockHelp($help, $attributes);
  }

  /**
   * Prepend elements to the field
   */
  public function prepend()
  {
    $append = func_get_args();
    $this->placeAround($append, 'prepend');
  }

  /**
   * Append elements to the field
   */
  public function append()
  {
    $append = func_get_args();
    $this->placeAround($append, 'append');
  }

  /**
   * Prepends an icon to a field
   *
   * @param string $icon       The icon to prepend
   * @param array  $attributes Its attributes
   */
  public function prependIcon($icon, $attributes = array())
  {
    $icon = $this->app['former.framework']->icon($icon, $attributes);

    $this->placeAround($icon, 'prepend');
  }

  /**
   * Append an icon to a field
   *
   * @param string $icon       The icon to prepend
   * @param array  $attributes Its attributes
   */
  public function appendIcon($icon, $attributes = array())
  {
    $icon = $this->app['former.framework']->icon($icon, $attributes);

    $this->placeAround($icon, 'append');
  }

  /**
   * Adds a label
   *
   * @param  string $label A label
   * @return ControlGroup
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////////// HELPERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Place elements around the field
   *
   * @param  array  $items An array of items to place
   * @param  string $place Where they should end up (prepend|append)
   */
  private function placeAround($items, $place)
  {
    $items = (array) $items;

    // Iterate over the items and place them where they should
    foreach ($items as $i) {
      if (!($i instanceof \Bootstrapper\Buttons) and !starts_with($i, '<button')) {
        $i = '<span class="add-on">'.$i.'</span>';
      }
      $this->{$place}[] = $i;
    }
  }
}
