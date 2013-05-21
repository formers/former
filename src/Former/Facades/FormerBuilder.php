<?php
namespace Former\Facades;

use Former\Former;
use Illuminate\Container\Container;

/**
 * Common building blocks to all environments
 */
abstract class FormerBuilder
{

  /**
   * The Container instance
   *
   * @var Container
   */
  protected static $app;

  /**
   * Static facade
   *
   * @param string $method
   * @param array  $parameters
   *
   * @return Former
   */
  public static function __callStatic($method, $parameters)
  {
    if (!static::$app) static::$app = static::getApp();
    $callable = array(static::$app['former'], $method);

    return call_user_func_array($callable, $parameters);
  }

  // Dependency binders -------------------------------------------- /

  /**
   * Add Framework to the app
   *
   * @param  Container $app
   * @param  string    $prefix Where to get config options from
   *
   * @return Container
   */
  public static function buildFramework(Container $app, $prefix = 'config.')
  {
    $framework = $app['config']->get($prefix.'framework');
    $app->bind('\Former\Interfaces\FrameworkInterface', function($app) use ($framework) {
      $frameworkClass = '\Former\Framework\\'.$framework;

      return new $frameworkClass($app);
    });

    return $app;
  }

  /**
   * Add Former to the app
   *
   * @param  Container $app
   *
   * @return Container
   */
  public static function buildFormer(Container $app)
  {
    $app->singleton('former', function($app) {
      return new Former(
        $app,
        $app->make('\Former\Populator'),
        $app->make('\Former\Interfaces\FrameworkInterface'));
    });

    return $app;
  }

}
