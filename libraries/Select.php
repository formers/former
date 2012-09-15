<?php
namespace Former;

use \Form;

class Select extends Field
{
  private $options = array();

  public function options($options, $selected = null)
  {
    $this->options = $options;

    if($selected) $this->value = $selected;
  }

  public function __toString()
  {
    if($this->type == 'multiselect') $this->multiple();

    return Form::select($this->name, $this->options, $this->value, $this->attributes);
  }
}