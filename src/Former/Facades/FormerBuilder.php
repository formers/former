<?php
namespace Former\Facades;

use \Illuminate\Container\Container;

abstract class FormerBuilder
{
  /**
   * The Container instance
   * @var Container
   */
  protected static $app;

  /**
   * Static facade
   */
  public static function __callStatic($method, $parameters)
  {
    if (!static::$app) static::$app = static::getApp();
    $callable = array(static::$app['former'], $method);

    return call_user_func_array($callable, $parameters);
  }

  // Dependency binders -------------------------------------------- /

  /**
   * Build Container
   *
   * @return Container
   */
  protected static function buildContainer()
  {
    return new Container;
  }

  /**
   * Add Framework to the app
   *
   * @param Container $app
   * @return Container
   */
  public static function buildFramework($app, $prefix = 'config.')
  {
    $framework = $app['config']->get($prefix.'framework');
    $app->bind('\Former\Interfaces\FrameworkInterface', function($app) use ($framework) {
      $framework = '\Former\Framework\\'.$framework;
      return new $framework($app);
    });

    return $app;
  }

  /**
   * Add Former to the app
   *
   * @param Container $app
   * @return Container
   */
  public static function buildFormer($app)
  {
    $app->singleton('former', function($app) {
      return new \Former\Former(
        $app,
        $app->make('\Former\Populator'),
        $app->make('\Former\Interfaces\FrameworkInterface'));
    });

    return $app;
  }
}