<?php
namespace Former\Facades;

use Former\FormerServiceProvider;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

/**
 * Former facade for the Laravel framework
 */
class Former extends Facade
{
  /**
   * Get the registered component.
   *
   * @return object
   */
  protected static function getFacadeAccessor()
  {
    // Bind Former classes
    if (!static::$app) {
      $app     = new Container;
      $factory = new FormerServiceProvider($app);
      $app     = $factory->bindCoreClasses($app);
      $app     = $factory->bindFormer($app);

      static::$app = $app;
    }

    return 'former';
  }
}
