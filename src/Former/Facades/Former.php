<?php
namespace Former\Facades;

use Illuminate\Support\Facade;

class Former extends Facade
{
  /**
   * Get the registered component.
   *
   * @return object
   */
  protected static function getFacadeAccessor()
  {
    return 'former';
  }
}
