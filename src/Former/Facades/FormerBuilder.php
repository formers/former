<?php
/**
 * FormerBuilder
 *
 * Common building blocks to all environments
 */
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
  public static function buildContainer()
  {
    return new Container;
  }

  /**
   * Add Meido classes to the app
   *
   * @param Container $app
   * @return Container
   */
  public static function buildMeido($app)
  {
    $app->bind('html', function($app) {
      return new \Meido\HTML\HTML($app['url']);
    });

    $app->singleton('form', function($app) {
      return new \Meido\Form\Form($app['url']);
    });

    return $app;
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