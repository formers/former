<?php
namespace Former;

use \Form;

class Select extends Field
{
  /**
   * The select options
   * @var array
   */
  private $options = array();

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