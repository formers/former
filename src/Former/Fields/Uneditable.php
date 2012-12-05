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
  public function __toString()
  {
    $this->attributes = $this->app['former.helpers']->addClass($this->attributes, 'uneditable-input');

    return
      '<span'.$this->app['former.helpers']->attributes($this->attributes).'>'.
        $this->app['former.laravel.html']->entities($this->value).
      '</span>';
  }
}
