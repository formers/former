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
   * Filter a field state
   *
   * @param string $state
   * @return string
   */
  public function filterState($state)
  {
    // Filter out wrong states
    if (!in_array($state, $this->states)) return null;
    return $state;
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
    return Arrays::each($classes, function($class) use ($with) {
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
    if (!$label) $label = $field->getLabel();

    // Get label text
    $text = $label->getValue();
    if (!$text) return false;

    // Append required text
    if ($field->isRequired()) {
      $text .= $this->app['former']->getOption('required_text');
    }

    // Render plain label if checkable, else a classic one
    $label->setValue($text);
    if (!$field->isCheckable()) $label->for($field->getName());
    return $label;
  }

}
