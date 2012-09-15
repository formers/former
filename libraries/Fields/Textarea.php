<?php
namespace Former\Fields;

use \Form;

class Textarea extends \Former\Field
{
  public function __toString()
  {
    return Form::textarea($this->name, $this->value, $this->attributes);
  }
}