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

    $this->registerFormer();
    $this->registerMeido();
  }

  /**
   * Register the application bindings.
   *
   * @return void
   */
  public function registerFormer()
  {
    $framework = $this->app['config']->get('former::framework');
    $this->app->bind('Former\Interfaces\FrameworkInterface', '\Former\Framework\\'.$framework);
    $this->app->singleton('former', '\Former\Former');
  }

  public function registerMeido()
  {
    $this->app->bind('html', '\Meido\HTML\HTML');
    $this->app->singleton('form', '\Meido\Form\Form');
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
