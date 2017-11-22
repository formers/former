<?php
namespace Former;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Symfony\Component\Finder\Finder;

/**
 * Register the Former package with the Laravel framework
 */
class FormerServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register Former's package with Laravel
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app = static::make($this->app);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return string[]
	 */
	public function provides()
	{
		return array('former', 'Former\Former');
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////// CLASS BINDINGS /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Create a Former container
	 *
	 * @param  Container $app
	 *
	 * @return Container
	 */
	public static function make($app = null)
	{
		if (!$app) {
			$app = new Container();
		}

		// Bind classes to container
		$provider = new static($app);
		$app      = $provider->bindCoreClasses($app);
		$app      = $provider->bindFormer($app);

		return $app;
	}

	/**
	 * Bind the core classes to the Container
	 *
	 * @param  Container $app
	 *
	 * @return Container
	 */
	public function bindCoreClasses(Container $app)
	{
		// Cancel if in the scope of a Laravel application
		if ($app->bound('events')) {
			return $app;
		}

		// Core classes
		//////////////////////////////////////////////////////////////////

		$app->bindIf('files', 'Illuminate\Filesystem\Filesystem');
		$app->bindIf('url', 'Illuminate\Routing\UrlGenerator');

		// Session and request
		//////////////////////////////////////////////////////////////////

		$app->bindIf('session.manager', function ($app) {
			return new SessionManager($app);
		});

		$app->bindIf('session', function ($app) {
			return $app['session.manager']->driver('array');
		}, true);

		$app->bindIf('request', function ($app) {
			$request = Request::createFromGlobals();
			if (method_exists($request, 'setSessionStore')) {
				$request->setSessionStore($app['session']);
			} else if (method_exists($request, 'setLaravelSession')) {
				$request->setLaravelSession($app['session']);
			} else {
				$request->setSession($app['session']);
			}

			return $request;
		}, true);

		// Config
		//////////////////////////////////////////////////////////////////

		$app->bindIf('path.config', function ($app) {
			return __DIR__ . '/../config/';
		}, true);

		$app->bindIf('config', function ($app) {
			$config = new Repository;
			$this->loadConfigurationFiles($app, $config);
			return $config;
		}, true);

		// Localization
		//////////////////////////////////////////////////////////////////

		$app->bindIf('translation.loader', function ($app) {
			return new FileLoader($app['files'], 'src/config');
		});

		$app->bindIf('translator', function ($app) {
			$loader = new FileLoader($app['files'], 'lang');

			return new Translator($loader, 'fr');
		});

		return $app;
	}

	/**
	 * Load the configuration items from all of the files.
	 *
	 * @param  Container $app
	 * @param  Repository  $config
	 * @return void
	 */
	protected function loadConfigurationFiles($app, Repository $config)
	{
		foreach ($this->getConfigurationFiles($app) as $key => $path)
		{
			$config->set($key, require $path);
		}
	}

	/**
	 * Get all of the configuration files for the application.
	 *
	 * @param  $app
	 * @return array
	 */
	protected function getConfigurationFiles($app)
	{
		$files = array();

		foreach (Finder::create()->files()->name('*.php')->in($app['path.config']) as $file)
		{
			$files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
		}

		return $files;
	}

	/**
	 * Bind Former classes to the container
	 *
	 * @param  Container $app
	 *
	 * @return Container
	 */
	public function bindFormer(Container $app)
	{
		// Add config namespace
		$configPath = __DIR__ . '/../config/former.php';
		$this->mergeConfigFrom($configPath, 'former');
		$this->publishes([$configPath => $app['path.config'] . '/former.php']);
		
		$framework = $app['config']->get('former.framework');
		
		$app->bind('former.framework', function ($app) {
			return $app['former']->getFrameworkInstance($app['config']->get('former.framework'));
		});

		$app->singleton('former.populator', function ($app) {
			return new Populator();
		});

		$app->singleton('former.dispatcher', function ($app) {
			return new MethodDispatcher($app, Former::FIELDSPACE);
		});

		$app->singleton('former', function ($app) {
			return new Former($app, $app->make('former.dispatcher'));
		});
		$app->alias('former', 'Former\Former');

		Helpers::setApp($app);

		return $app;
	}
}
