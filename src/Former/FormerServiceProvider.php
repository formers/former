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

    $this->registerMeido();
    $this->registerFormer();
  }

  /**
   * Register the application bindings.
   *
   * @return void
   */
  public function registerFormer()
  {
    $this->app = Facades\Agnostic::buildFramework($this->app, 'former::');

    $this->app = Facades\Agnostic::buildFormer($this->app);
  }

  /**
   * Register Meido's bindings
   *
   * @return void
   */
  public function registerMeido()
  {
    $this->app->bind('html', function($app) {
      return new \Meido\HTML\HTML($app['url']);
    });

    $this->app->singleton('form', function($app) {
      return new \Meido\Form\Form($app['url']);
    });
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
