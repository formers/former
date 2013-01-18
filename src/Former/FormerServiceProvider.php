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

    $this->registerBindings();
  }

  /**
   * Register the application bindings.
   *
   * @return void
   */
  public function registerBindings()
  {

    // Former

    $this->app['former'] = $this->app->share(function($app) {
      return new Former($app, new Populator);
    });

    $this->formFramework = $this->app->share(function($app) {
      $framework = '\Former\Framework\\'.$app['former']->getOption('framework');

      return new $framework($app);
    });

    $this->app['former.helpers'] = $this->app->share(function($app) {
      return new Helpers($app);
    });

    $this->app['former.laravel.file'] = new \Laravel\File($app);

    // Meido

    $this->app['form'] = $this->app->share(function($app) {
      return new \Meido\Form\Form($app['url']);
    });

    $this->app['html'] = $this->app->share(function($app) {
      return new \Meido\HTML\HTML($app['url']);
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
