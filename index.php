<?php
require 'vendor/autoload.php';

// Build Former ---------------------------------------------------- /

$former = new Illuminate\Container\Container;

$former['former'] = $former->share(function($app) {
  return new Former\Former($app, new Former\Populator);
});

$former['former.framework'] = $former->share(function($app) {
  return new Former\Framework\TwitterBootstrap($app);
});

$former['former.helpers'] = new Former\Helpers($former);

// Illuminate ------------------------------------------------------ /

$former['url'] = $former->share(function($app) {
  $route = new Symfony\Component\Routing\RouteCollection;
  return new Illuminate\Routing\UrlGenerator($route, $app['request']);
});

$former['html'] = $former->share(function($app) {
  return new Meido\HTML\HTML($app['url']);
});

$former['form'] = $former->share(function($app) {
  return new Meido\Form\Form($app['url']);
});

$former['config'] = $former->share(function($app) {
  $filesystem = new Illuminate\Filesystem\Filesystem();
  $fileloader = new Illuminate\Config\FileLoader($filesystem, 'src/');
  return new Illuminate\Config\Repository($fileloader, 'config');
});

$former['request'] = $former->share(function($app) {
  $request   = new Illuminate\Http\Request;
  $encrypter = new Illuminate\Encryption\Encrypter('foobar');
  $cookie    = new Illuminate\Cookie\CookieJar($request, $encrypter, array());
  $request->setSessionStore(new Illuminate\Session\CookieStore($cookie));
  return $request;
});

// Try out --------------------------------------------------------- /

echo $former['former']->text('foo'); // Yaaaaay this works