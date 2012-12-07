<?php
/**
 * Uneditable
 *
 * Uneditable inputs (which aren't actually fields but you know)
 */
namespace Former\Fields;

class Uneditable extends \Former\Field
{
  /**
   * Prints out the current tag
   *
   * @return string An uneditable input tag
   */
  public function render()
  {
    $this->attributes = $this->app['former.helpers']->addClass($this->attributes, 'uneditable-input');

    return
      '<span'.$this->app['former.laravel.html']->attributes($this->attributes).'>'.
        $this->app['former.laravel.html']->entities($this->value).
      '</span>';
  }
}
