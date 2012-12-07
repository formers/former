<?php
namespace Former\Fields;

use \Form;

class Textarea extends \Former\Traits\Field
{
  /**
   * Outputs a textarea
   *
   * @return string
   */
  public function render()
  {
    return $this->app['former.laravel.form']->textarea($this->name, $this->value, $this->attributes);
  }
}
