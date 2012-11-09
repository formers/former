<?php
/**
 * Checkbox
 *
 * My name is Bawksy you see
 */
namespace Former\Fields;

class Checkbox extends \Former\Checkable
{
  /**
   * The current checkable type
   * @var string
   */
  protected $checkable = 'checkbox';

  /**
   * Create a serie of checkboxes
   */
  public function checkboxes()
  {
    $this->items(func_get_args());
  }
}
