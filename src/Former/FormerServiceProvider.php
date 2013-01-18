<?php
/**
 * FormerServiceProvider
 *
 * Register the Former package with the Laravel framework
 */
namespace Former;

use Illuminate\Support\ServiceProvider;

class FormerServiceProvider extends ServiceProvider
{
  public function register()
  {
    // Register config file
    $this->app['config']->package('anahkiasen/former', __DIR__.'/../config');

    $this->app = Facades\Agnostic::buildMeido($this->app);
    $this->app = Facades\Agnostic::buildFramework($this->app, 'former::');
    $this->app = Facades\Agnostic::buildFormer($this->app);
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array('former');
  }
}
