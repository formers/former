<?php
namespace Former\Facades;

use Former\FormerServiceProvider;
use Illuminate\Support\Facades\Facade;

/**
 * Former facade for the Laravel framework
 */
class Former extends Facade
{
	/**
	 * Get the registered component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		if (!static::$app) {
			static::$app = FormerServiceProvider::make();
		}

		return 'former';
	}
}
