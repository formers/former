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

    // Search for the key itself
    if (static::$app['translator']->has($key)) {
      $translation = static::$app['translator']->get($key);
    } elseif (static::$app['translator']->has($translateFrom)) {
      $translation  = static::$app['translator']->get($translateFrom);
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
   * @param  array|object $query The array of results
   * @param  string       $value The attribute to use as value
   * @param  string       $key   The attribute to use as key
   * @return array               A data array
   */
  public static function queryToArray($query, $value = null, $key = null)
  {
    // Automatically fetch Lang objects for people who store translated options lists
    // Same of unfetched queries
    if (!$query instanceof Collection) {
      if (method_exists($query, 'get')) $query = $query->get();
      if (!is_array($query)) $query = (array) $query;
    }

    // Populates the new options
    foreach ($query as $model) {

      // If it's an array, convert to object
      if (is_array($model)) $model = (object) $model;

      // Calculate the value
      if ($value and isset($model->$value)) $modelValue = $model->$value;
      elseif (method_exists($model, '__toString')) $modelValue = $model->__toString();
      else $modelValue = null;

      // Calculate the key
      if ($key and isset($model->$key)) $modelKey = $model->$key;
      elseif (method_exists($model, 'getKey')) $modelKey = $model->getKey();
      elseif (isset($model->id)) $modelKey = $model->id;
      else $modelKey = $modelValue;

      // Skip if no text value found
      if (!$modelValue) continue;

      $array[$modelKey] = (string) $modelValue;
    }

    return isset($array) ? $array : $query;
  }
}
