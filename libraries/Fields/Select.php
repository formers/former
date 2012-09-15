<?php
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
    $this->options = $options;
    $this->value = $selected;

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