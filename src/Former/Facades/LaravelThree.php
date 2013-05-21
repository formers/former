<?php
namespace Former\Facades;

use Illuminate\Container\Container;
use Laravel\Config;

/**
 * Makes Former Laravel 3 compatible
 */
class LaravelThree extends FormerBuilder
{

  /**
   * Build a Laravel 3 application
   *
   * @return Container
   */
  protected static function getApp()
  {
    $app = new Container;

    // Laravel ----------------------------------------------------- /

    $app->bind('url', function($app) {
      return new Legacy\Redirector('Laravel\URL');
    });

    $app->bind('session', function($app) {
      return new Legacy\Session;
    });

    $app->bind('config', function($app) {
      return new Legacy\Config;
    });

    $app->bind('request', function($app) {
      return new Legacy\Redirector('Laravel\Input');
    });

    $app->bind('translator', function($app) {
      return new Legacy\Translator;
    });

    // Former ------------------------------------------------------ /

    // Load configuration from the new to the old place
    Config::set('former::config', include __DIR__.'/../../config/config.php');

    $app = static::buildFramework($app, 'former::');
    $app = static::buildFormer($app);

    return $app;
  }

}
