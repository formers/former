<?php namespace Former;

use Illuminate\Support\ServiceProvider;

define('FORMER_VERSION', '2.6.0');

class FormerServiceProvider extends ServiceProvider
{
  public function register()
  {
    // Register config file
    $this->app['config']->package('anahkiasen/former', __DIR__.'/../config');

    // Register alias
    $this->app['former'] = $this->app->share(function($app)
    {
      return new Former($app);
    });
  }
}
