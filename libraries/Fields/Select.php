<?php
/**
 * Select
 *
 * Everything list-related (select, multiselect, ...)
 */
namespace Former\Fields;

use \Form;

class Select extends \Former\Field
{
  /**
   * The select options
   * @var array
   */
  private $options = array();

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
  }

  /**
   * Set the select options
   *
   * @param  array $options  The options as an array
   * @param  mixed $selected Facultative selected entry
   */
  public function options($options, $selected = null)
  {
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
  public function fromQuery($results, $value, $key = 'id')
  {
    // Fetch the Query if it hasn't been
    if($results instanceof \Laravel\Database\Eloquent\Query) {
      $results = $results->get();
    }

    // Populates the new options
    foreach($results as $model) {

      // Filter out wrong attributes
      if(!isset($model->$value)) continue;
      if(!isset($model->$key)) $key = $value;

      $options[$model->$key] = $model->$value;
    }

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
   * Renders the select
   *
   * @return string A <select> tag
   */
  public function __toString()
  {
    if($this->type == 'multiselect') $this->multiple();

    return Form::select($this->name, $this->options, $this->value, $this->attributes);
  }
}
