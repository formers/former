<?php
/**
 * Radio
 *
 * Creating radios elements since 1873
 */
namespace Former\Fields;

class Radio extends \Former\Checkable
{
  protected $checkable = 'radio';

  /**
   * Create a serie of radios
   */
  public function radios()
  {
    $this->items(func_get_args());
  }
}
