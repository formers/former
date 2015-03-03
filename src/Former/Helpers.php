<?php
namespace Former;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;

/**
 * Various helpers used by all Former classes
 */
class Helpers
{
	/**
	 * The IoC Container
	 *
	 * @var Container
	 */
	protected static $app;

	/**
	 * Bind a Container to the Helpers class
	 *
	 * @param Container $app
	 */
	public static function setApp(Container $app)
	{
		static::$app = $app;
	}

	/**
	 * Encodes HTML
	 *
	 * @param string $value The string to encode
	 *
	 * @return string
	 */
	public static function encode($value)
	{
		return htmlentities($value, ENT_QUOTES, 'UTF-8', true);
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////// LOCALIZATION HELPERS /////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Translates a string by trying several fallbacks
	 *
	 * @param  string $key      The key to translate
	 * @param  string $fallback The ultimate fallback
	 *
	 * @return string           A translated string
	 */
	public static function translate($key, $fallback = null)
	{
		// If nothing was given, return nothing, bitch
		if (!$key) {
			return null;
		}

		// If no fallback, use the key
		if (!$fallback) {
			$fallback = $key;
		}

		// Assure we don't already have a Lang object
		if (is_object($key) and method_exists($key, 'get')) {
			return $key->get();
		}

		$translation   = null;
		$translateFrom = static::$app['former']->getOption('translate_from');
		if (substr($translateFrom, -1) !== '/') {
			$translateFrom .= '.';
		}
		$translateFrom .= $key;

		// Convert a[b[c]] to a.b.c for nested translations [a => [b => 'B!']]
		if (strpos($translateFrom, ']') !== false) {
			$translateFrom = str_replace(array(']', '['), array('', '.'), $translateFrom);
		}

		// Search for the key itself, if it is valid
		$validKey = preg_match("/^[a-z0-9_-]+([.][a-z0-9 _-]+)+$/i", $key) === 1;
		if ($validKey && static::$app['translator']->has($key)) {
			$translation = static::$app['translator']->get($key);
		} elseif (static::$app['translator']->has($translateFrom)) {
			$translation = static::$app['translator']->get($translateFrom);
		}

		// Replace by fallback if invalid
		if (!$translation or is_array($translation)) {
			$translation = $fallback;
		}

		// Capitalize
		$capitalize = static::$app['former']->getOption('capitalize_translations');

		return $capitalize ? ucfirst($translation) : $translation;
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////// DATABASE HELPERS ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Transforms an array of models into an associative array
	 *
	 * @param  array|object    $query      The array of results
	 * @param  string|function $text       The value to use as text
	 * @param  string|array    $attributes The data to use as attributes
	 *
	 * @return array               A data array
	 */
	public static function queryToArray($query, $text = null, $attributes = null)
	{
		// Automatically fetch Lang objects for people who store translated options lists
		// Same of unfetched queries
		if (!$query instanceof Collection) {
			if (method_exists($query, 'get')) {
				$query = $query->get();
			}
			if (!is_array($query)) {
				$query = (array) $query;
			}
		}

		//Convert parametrs of old format to new format
		if (!is_callable($text)) {
			$optionTextValue = $text;
			$text = function ($model) use($optionTextValue) {
				if ($optionTextValue and isset($model->$optionTextValue)) {
					return $model->$optionTextValue;
				} elseif (method_exists($model, '__toString')) {
					return  $model->__toString();
				} else {
					return null;
				}
			};
		}

		if (!is_array($attributes)) {
			if (is_string($attributes)) {
				$attributes = ['value' => $attributes];
			} else {
				$attributes = ['value' => null];
			}
		}

		if (!isset($attributes['value'])) {
			$attributes['value'] = null;
		}
		//-------------------------------------------------

		// Populates the new options
		foreach ($query as $model) {
			// If it's an array, convert to object
			if (is_array($model)) {
				$model = (object) $model;
			}

			// Calculate option text
			$optionText = $text($model);

			// Skip if no text value found
			if (!$optionText) {
				continue;
			}

			//Collect option attributes
			foreach ($attributes as $optionAttributeName => $modelAttributeName) {
				if (is_callable($modelAttributeName)) {
					$optionAttributeValue = $modelAttributeName($model);
				} elseif ($modelAttributeName and isset($model->$modelAttributeName)) {
					$optionAttributeValue = $model->$modelAttributeName;
				} elseif($optionAttributeName === 'value') {
					//For backward compatibility
					if (method_exists($model, 'getKey')) {
						$optionAttributeValue = $model->getKey();
					} elseif (isset($model->id)) {
						$optionAttributeValue = $model->id;
					} else {
						$optionAttributeValue = $optionText;
					}
				} else {
					$optionAttributeValue = '';
				}

				//For backward compatibility
				if (count($attributes) === 1) {
					$array[$optionAttributeValue] = (string) $optionText;
				} else {
					$array[$optionText][$optionAttributeName] = (string) $optionAttributeValue;
				}
			}
		}

		return !empty($array) ? $array : $query;
	}
}
