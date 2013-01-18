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

    $app->bind('url', function($app) {
      return new Legacy\Redirector('Url');
    });

    $app->bind('session', function($app) {
      return new Legacy\Session;
    });

    $app->bind('config', function($app) {
      return new Legacy\Config;
    });

    $app->bind('request', function($app) {
      return new Legacy\Redirector('Input');
    });

    $app->bind('translator', function($app) {
      return new Legacy\Translator;
    });

    $app->bind('html', function() {
      return new Legacy\Redirector('HTML');
    });
    $app->bind('form', function() {
      return new Legacy\Redirector('Form');
    });

    // Former ------------------------------------------------------ /

    // Load configuration from the new to the old place
    Config::set('former::config', include __DIR__.'/../../config/config.php');

    $app = static::buildFramework($app, 'former::');
    $app = static::buildFormer($app);

    return $app;
  }
}