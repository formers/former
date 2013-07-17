<?php
namespace Former\Interfaces;

/**
 * Mandatory methods on all fields
 */
interface FieldInterface
{
  /**
   * Renders the field
   *
   * @return string
   */
  public function render();
}
