<?php
/**
 * Legacy
 *
 * Makes Former Laravel 3 compatible
 */
namespace Former\Facades;

use \Laravel\Config;

class LaravelThree extends FormerBuilder
{
  /**
   * Build a Laravel 3 application
   *
   * @return Container
   */
  protected static function getApp()
  {
    $app = static::buildContainer();

    // Laravel ----------------------------------------------------- /

    $app['url'] = $app->share(function($app) {
      return new Legacy\Redirector('Url');
    });

    $app['session'] = $app->share(function($app) {
      return new Legacy\Session;
    });

    $app['config'] = $app->share(function($app) {
      return new Legacy\Config;
    });

    $app['request'] = $app->share(function($app) {
      return new Legacy\Redirector('Input');
    });

    $app['translator'] = $app->share(function($app) {
      return new Legacy\Translator;
    });

    // Former ------------------------------------------------------ /

    // Load configuration from the new to the old place
    Config::set('former::config', include __DIR__.'/../../config/config.php');

    $app = static::buildMeido($app);
    $app = static::buildFramework($app, 'former::');
    $app = static::buildFormer($app);

    return $app;
  }
}