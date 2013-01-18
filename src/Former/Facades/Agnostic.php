<?php
/**
 * Agnostic
 *
 * Agnostic facade to use Former anywhere
 */
namespace Former\Facades;

class Agnostic extends FormerBuilder
{
  /**
   * Build the Former application
   *
   * @return Container
   */
  protected static function getApp()
  {
    $app = static::buildContainer();

    // Illuminate -------------------------------------------------- /

    $app->alias('Symfony\Component\HttpFoundation\Request', 'request');
    $app->bind('files', 'Illuminate\Filesystem\Filesystem');
    $app->bind('url', 'Illuminate\Routing\UrlGenerator');
    $app->instance('Illuminate\Container\Container', $app);

    $app->bind('session', function($app) {
      $request   = new \Illuminate\Http\Request;
      $encrypter = new \Illuminate\Encryption\Encrypter('foobar');
      $cookie    = new \Illuminate\Cookie\CookieJar($request, $encrypter, array());

      return new \Illuminate\Session\CookieStore($cookie);
    });

    $app->bind('Symfony\Component\HttpFoundation\Request', function($app) {
      $request = new \Illuminate\Http\Request;
      $request->setSessionStore($app['session']);

      return $request;
    });

    $app->bind('config', function($app) {
      $fileloader = new \Illuminate\Config\FileLoader($app['files'], 'src/');

      return new \Illuminate\Config\Repository($fileloader, 'config');
    });

    $app->bind('loader', function($app) {
      return new \Illuminate\Translation\FileLoader($app['files'], 'src/config');
    });

    $app->bind('translator', function($app) {
      return new \Illuminate\Translation\Translator($app['loader'], 'fr', 'en');
    });

    // Former ------------------------------------------------------ /

    $app = static::buildMeido($app);
    $app = static::buildFramework($app);
    $app = static::buildFormer($app);

    return $app;
  }
}
