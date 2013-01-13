<?php
namespace Former\Form\Fields;

use \Former\Traits\Field;

class Textarea extends Field
{

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Outputs a textarea
   *
   * @return string
   */
  public function render()
  {
    return $this->app['form']->textarea($this->name, $this->value, $this->attributes);
  }
}
