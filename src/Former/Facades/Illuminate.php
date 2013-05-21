<?php
namespace Former\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Former facade for the Laravel framework
 */
class Illuminate extends Facade
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
