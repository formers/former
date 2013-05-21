<?php
namespace Former\Facades;

use Illuminate\Config\FileLoader as ConfigLoader;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

/**
 * Agnostic facade to use Former anywhere
 */
class Agnostic extends FormerBuilder
{

  /**
   * Build the Former application
   *
   * @return Container
   */
  protected static function getApp()
  {
    $app = new Container;

    // Illuminate -------------------------------------------------- /

    $app->alias('Symfony\Component\HttpFoundation\Request', 'request');
    $app->bind('files', 'Illuminate\Filesystem\Filesystem');
    $app->bind('url', 'Illuminate\Routing\UrlGenerator');
    $app->instance('Illuminate\Container\Container', $app);
    $app->instance('Illuminate\Encryption\Encrypter', new Encrypter('foobar'));

    // Session ----------------------------------------------------- /

    $app->bind('session.manager', function($app) {
      return new SessionManager($app);
    });

    $app->singleton('session', function($app) {
      return $app['session.manager']->driver('array');
    });

    $app->singleton('Symfony\Component\HttpFoundation\Request', function($app) {
      $request = new Request;
      $request->setSessionStore($app['session']);

      return $request;
    });

    // Config ------------------------------------------------------ /

    $app->bind('config', function($app) {
      $fileloader = new ConfigLoader($app['files'], 'src/');

      return new Repository($fileloader, 'config');
    });

    // Localization ------------------------------------------------ /

    $app->bind('loader', function($app) {
      return new FileLoader($app['files'], 'src/config');
    });

    $app->bind('translator', function($app) {
      return new Translator($app['loader'], 'fr', 'en');
    });

    // Former ------------------------------------------------------ /

    $app = static::buildFramework($app);
    $app = static::buildFormer($app);

    return $app;
  }

}
