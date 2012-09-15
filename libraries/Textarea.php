<?php
namespace Former;

use \Form;

class Textarea extends Field
{
  public function __toString()
  {
    return Form::textarea($this->name, $this->value, $this->attributes);
  }
}