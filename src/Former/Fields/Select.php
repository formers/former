<?php
/**
 * Select
 *
 * Everything list-related (select, multiselect, ...)
 */
namespace Former\Fields;

use \Form;
use \Former\Helpers;
use \HTML;

class Select extends \Former\Field
{
  /**
   * The select options
   * @var array
   */
  private $options = array();

  /**
   * The select's placeholder
   * @var string
   */
  private $placeholder = null;

  /**
   * Easier arguments order for selects
   *
   * @param string $type       select or multiselect
   * @param string $name       Field name
   * @param string $label      Field label
   * @param array  $options    Its options
   * @param mixed  $selected   Selected entry
   * @param array  $attributes Attributes
   */
  public function __construct($type, $name, $label, $options, $selected, $attributes)
  {
    if($options) $this->options = $options;
    if($selected) $this->value = $selected;

    parent::__construct($type, $name, $label, $selected, $attributes);

    // Multiple models population
    if (is_array($this->value)) {
      $this->fromQuery($this->value);
      $this->value = $selected ?: null;
    }
  }

  /**
   * Set the select options
   *
   * @param  array   $options      The options as an array
   * @param  mixed   $selected     Facultative selected entry
   * @param  boolean $valuesAsKeys Whether the array's values should be used as
   *                               the option's values instead of the array's keys
   */
  public function options($_options, $selected = null, $valuesAsKeys = false)
  {
    // Automatically fetch Lang objects for people who store translated options lists
    if ($_options instanceof \Laravel\Lang) {
      $_options = $_options->get();
    }

    // If valuesAsKeys is true, use the values as keys
    if ($valuesAsKeys) {
      foreach($_options as $v) $options[$v] = $v;
    } else $options = $_options;

    $this->options = $options;

    if($selected) $this->value = $selected;
  }

  /**
   * Use the results from a Fluent/Eloquent query as options
   *
   * @param  array  $results  An array of Eloquent models
   * @param  string $value    The attribute to use as text
   * @param  string $key      The attribute to use as value
   */
  public function fromQuery($results, $value = null, $key = null)
  {
    $options = Helpers::queryToArray($results, $value, $key);

    if(isset($options)) $this->options = $options;
  }

  /**
   * Select a particular list item
   *
   * @param  mixed $selected Selected item
   */
  public function select($selected)
  {
    $this->value = $selected;
  }

  /**
   * Add a placeholder to the current select
   *
   * @param  string $placeholder The placeholder text
   */
  public function placeholder($placeholder)
  {
    $this->placeholder = Helpers::translate($placeholder);
  }

  /**
   * Returns the current options in memory for manipulations
   *
   * @return array The current options array
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Renders the select
   *
   * @return string A <select> tag
   */
  public function __toString()
  {
    // Multiselects
    if ($this->type == 'multiselect') {
      $this->multiple();
    }

    // Render select
    $select = Form::select($this->name, $this->options, $this->value, $this->attributes);

    // Add placeholder text if any
    if ($this->placeholder) {
      $placeholder = array('value' => '', 'disabled' => '');
      if(!$this->value) $placeholder['selected'] = '';
      $placeholder = '<option'.HTML::attributes($placeholder).'>' .$this->placeholder. '</option>';

      $select = preg_replace('#<select([^>]+)>#', '$0'.$placeholder, $select);
    }

    return $select;
  }
}
