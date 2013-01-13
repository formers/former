<?php
/**
 * FormerServiceProvider
 *
 * Register the Former package with the Laravel framework
 */
namespace Former;

use Illuminate\Support\ServiceProvider;

define('FORMER_VERSION', '3.0.0');

class FormerServiceProvider extends ServiceProvider
{
  public function register()
  {
    // Register config file
    $this->app['config']->package('anahkiasen/former', __DIR__.'/../config');

    $this->registerBindings();
  }

  /**
   * Register the application bindings.
   *
   * @return void
   */
  public function registerBindings()
  {
    $this->app['former'] = $this->app->share(function($app) {
      return new Former($app);
    });

    $this->app['former.framework'] = $this->app->share(function($app) {
      $framework = '\Former\Framework\\'.$app['config']->get('former::framework');
      return new $framework($app);
    });

    $this->app['former.helpers'] = $this->app->share(function($app) {
      return new Helpers($app);
    });

    $this->app['former.laravel.form'] = $this->app->share(function($app) {
      return new \Meido\Form\Form($app['url']);
    });

    $this->app['former.laravel.file'] = $this->app->share(function($app) {
      return new \Laravel\File($app);
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
