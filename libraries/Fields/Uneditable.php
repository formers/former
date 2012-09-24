<?php
/**
 * Uneditable
 *
 * Uneditable inputs (which aren't actually fields but you know)
 */
namespace Former\Fields;

use \Former\Helpers;
use \HTML;

class Uneditable extends \Former\Fields
{
  /**
   * Prints out the current tag
   *
   * @return string An uneditable input tag
   */
  public function __toString()
  {
    $this->attributes = Helpers::addClass($this->atteibutes, 'uneditable-input');

    return
      '<span'.HTML::attributes($attributes).'>'.
        HTML::entities($value).
      '</span>';
  }
}
