<?php
namespace Former\Traits;

use Former\Traits\Field;
use HtmlObject\Element;

/**
 * Base helpers and common methods to all frameworks
 */
abstract class Framework
{
  /**
   * The Container
   *
   * @var Container
   */
  protected $app;

  /**
   * Form types that trigger special styling
   *
   * @var array
   */
  protected $availableTypes = array();

  /**
   * The field states available
   * @var array
   */
  protected $states = array();

  /**
   * The default label width (for horizontal forms)
   *
   * @var string
   */
  protected $labelWidth;

  /**
   * The default field width (for horizontal forms)
   *
   * @var string
   */
  protected $fieldWidth;

  /**
   * The default offset for fields (for horizontal form fields
   * with no label, so usually equal to the default label width)
   *
   * @var string
   */
  protected $fieldOffset;

  /**
   * The default HTML tag used for icons
   *
   * @var string
   */
  protected $iconTag;

  /**
   * The default set for icon fonts
   *
   * @var string
   */
  protected $iconSet;

  /**
   * The default prefix icon names
   *
   * @var string
   */
  protected $iconPrefix;

  ////////////////////////////////////////////////////////////////////
  //////////////////////// CURRENT FRAMEWORK /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the name of the current framework
   *
   * @return string
   */
  public function current()
  {
    return basename(str_replace('\\', '/', get_class($this)));
  }

  /**
   * Check if the current framework matches something
   *
   * @param  string $framework
   *
   * @return boolean
   */
  public function is($framework)
  {
    return $framework == $this->current();
  }

  /**
   * Check if the current framework doesn't match something
   *
   * @param  string $framework
   *
   * @return boolean
   */
  public function isnt($framework)
  {
    return $framework != $this->current();
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// COMMON METHODS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * List form types triggered special styling form current framework
   *
   * @return array
   */
  public function availableTypes()
  {
    return $this->availableTypes;
  }

  /**
   * Filter a field state
   *
   * @param string $state
   *
   * @return string
   */
  public function filterState($state)
  {
    // Filter out wrong states
    return in_array($state, $this->states) ? $state : null;
  }

  /**
   * Framework error state
   *
   * @return string
   */
  public function errorState()
  {
    return 'error';
  }


  /**
   * Returns corresponding inline class of a field
   *
   * @param Field $field
   *
   * @return string
   */
  public function getInlineLabelClass($field)
  {
    return 'inline';
  }

  /**
   * Set framework defaults from its config file
   */
  protected function setFrameworkDefaults()
  {
    $this->setFieldWidths($this->getFrameworkOption('labelWidths'));
    $this->setIconDefaults();
  }

  protected function setFieldWidths($widths) {}

  /**
   * Override framework defaults for icons with config values where set
   */
  protected function setIconDefaults()
  {
    $this->iconTag    = $this->getFrameworkOption('icon.tag');
    $this->iconSet    = $this->getFrameworkOption('icon.set');
    $this->iconPrefix = $this->getFrameworkOption('icon.prefix');
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
    if (!$iconType) {
      return false;
    }

    // icon settings can be overridden for a specific icon
    $tag    = array_get($iconSettings, 'tag', $this->iconTag);
    $set    = array_get($iconSettings, 'set', $this->iconSet);
    $prefix = array_get($iconSettings, 'prefix', $this->iconPrefix);

    return Element::create($tag, null, $attributes)->addClass("$set $prefix-$iconType");
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// HELPERS //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Add classes to a field
   *
   * @param Field $field
   * @param array $classes
   */
  protected function addClassesToField($field, $classes)
  {
    // If we found any class, add them
    if ($classes) {
      $field->addClass(implode(' ', $classes));
    }

    return $field;
  }

  /**
   * Prepend an array of classes with a string
   *
   * @param array  $classes The classes to prepend
   * @param string $with    The string to prepend them with
   *
   * @return array A prepended array
   */
  protected function prependWith($classes, $with)
  {
    return array_map(function ($class) use ($with) {
      return $with.$class;
    }, $classes);
  }

  /**
   * Create a label for a field
   *
   * @param Field  $field
   * @param string $label The field label if non provided
   *
   * @return string A label
   */
  public function createLabelOf(Field $field, Element $label = null)
  {
    // Get the label and its informations
    if (!$label) {
      $label = $field->getLabel();
    }

    // Get label "for"
    $for = $field->id ?: $field->getName();

    // Get label text
    $text = $label->getValue();
    if (!$text) {
      return false;
    }

    // Append required text
    if ($field->isRequired()) {
      $text .= $this->app['former']->getOption('required_text');
    }

    // Render plain label if checkable, else a classic one
    $label->setValue($text);
    if (!$field->isCheckable()) {
      $label->for($for);
    }

    return $label;
  }

  /**
   * Get an option for the current framework
   *
   * @param string $option
   *
   * @return string
   */
  protected function getFrameworkOption($option)
  {
    return $this->app['config']->get("former::{$this->current()}.$option");
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// WRAP BLOCKS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Wraps all label contents with potential additional tags.
   *
   * @param  string $label
   *
   * @return string A wrapped label
   */
  public function wrapLabel($label)
  {
    return $label;
  }
}
