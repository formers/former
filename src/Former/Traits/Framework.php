<?php
namespace Former\Traits;

use Former\Traits\Field;
use HtmlObject\Element;
use Underscore\Methods\ArraysMethods as Arrays;

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
   * Override framework defaults for icons with config values where set
   *
   */
  protected function setIconDefaults()
  {
    if ($iconTag = $this->app['former']->getOption('icon_tag')) {
      $this->iconTag = $iconTag;
    }
    
    if ($iconSet = $this->app['former']->getOption('icon_set')) {
      $this->iconSet = $iconSet;
    }

    if ($iconPrefix = $this->app['former']->getOption('icon_prefix')) {
      $this->iconPrefix = $iconPrefix;
    }
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

    // icon settings can be overridden for a specific icon
    $tag = isset($iconSettings['tag']) ? $iconSettings['tag'] : $this->iconTag;  
    $set = isset($iconSettings['set']) ? $iconSettings['set'] : $this->iconSet;  
    $prefix = isset($iconSettings['prefix']) ? $iconSettings['prefix'] : $this->iconPrefix;  

    return Element::create($tag, null, $attributes)->addClass("$set $prefix-$iconType");
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// HELPERS //////////////////////////////
  ////////////////////////////////////////////////////////////////////

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
    return Arrays::each($classes, function ($class) use ($with) {
      return $with.$class;
    });
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

}
