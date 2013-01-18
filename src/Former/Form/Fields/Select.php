<?php
/**
 * Select
 *
 * Everything list-related (select, multiselect, ...)
 */
namespace Former\Form\Fields;

use \Former\Traits\Field;
use \Former\Helpers;

class Select extends Field
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

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

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
  public function __construct($app, $type, $name, $label, $options, $selected, $attributes)
  {
    if($options)  $this->options = $options;
    if($selected) $this->value = $selected;

    parent::__construct($app, $type, $name, $label, $selected, $attributes);

    // Multiple models population
    if (is_array($this->value)) {
      $this->fromQuery($this->value);
      $this->value = $selected ?: null;
    }
  }

  /**
   * Renders the select
   *
   * @return string A <select> tag
   */
  public function render()
  {
    $name = $this->name;

    // Multiselects
    if ($this->isOfType('multiselect')) {
      if (!isset($this->attributes['id'])) {
        $this->setAttribute('id', $name);
      }

      $this->multiple();
      $name .= '[]';
    }

    // Render select
    $select = $this->app['form']->select($name, $this->options, $this->value, $this->attributes);

    // Add placeholder text if any
    if ($this->placeholder) {
      $placeholder = array('value' => '', 'disabled' => '');
      if(!$this->value) $placeholder['selected'] = '';
      $placeholder = '<option'.$this->app['html']->attributes($placeholder).'>' .$this->placeholder. '</option>';

      $select = preg_replace('#<select([^>]+)>#', '$0'.$placeholder, $select);
    }

    return $select;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// FIELD METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

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

    return $this;
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
    $this->options = Helpers::queryToArray($results, $value, $key);

    return $this;
  }

  /**
   * Select a particular list item
   *
   * @param  mixed $selected Selected item
   */
  public function select($selected)
  {
    $this->value = $selected;

    return $this;
  }

  /**
   * Add a placeholder to the current select
   *
   * @param  string $placeholder The placeholder text
   */
  public function placeholder($placeholder)
  {
    $this->placeholder = Helpers::translate($placeholder);

    return $this;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Returns the current options in memory for manipulations
   *
   * @return array The current options array
   */
  public function getOptions()
  {
    return $this->options;
  }
}
