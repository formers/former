<?php
/**
 * Helpers
 *
 * Various helpers used by all Former classes
 */
namespace Former;

use \Underscore\Types\String;
use \Illuminate\Translation\Translator;
use \Illuminate\Database\Eloquent\Collection;

class Helpers
{
  public function __construct($app)
  {
    $this->app = $app;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// HTML HELPERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Add a class to an attributes array
   *
   * @param  array  $attributes An array of attributes
   * @param  string $class      The class to add
   * @return array              The modified attributes array
   */
  public function addClass($attributes, $class)
  {
    if (!isset($attributes['class'])) $attributes['class'] = null;

    // Prevent adding a class twice
    if (!String::contains($attributes['class'], $class)) {
      $attributes['class'] = trim($attributes['class']. ' ' .$class);
    }

    return $attributes;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////// LOCALIZATION HELPERS ////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Translates a string by trying several fallbacks
   *
   * @param  string $key      The key to translate
   * @param  string $fallback The ultimate fallback
   * @return string           A translated string
   */
  public function translate($key, $fallback = null)
  {
    // If nothing was given, return nothing, bitch
    if(!$key) return null;

    // If no fallback, use the key
    if(!$fallback) $fallback = $key;

    // Assure we don't already have a Lang object
    if($key instanceof Translator) return $key->get();

    // Search for the key itself
    $translations = $this->app['config']->get('former::translate_from');
    if ($this->app['translator']->has($key)) {
      $translation = $this->app['translator']->get($key);
    } elseif ($this->app['translator']->has($translations.'.'.$key)) {
      $translation  = $this->app['translator']->get($translations.'.'.$key);
    } else {
      $translation = $fallback;
    }

    // If we were return a translations array
    if (is_array($translation)) $translation = $fallback;

    return ucfirst($translation);
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////// DATABASE HELPERS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Transforms a Fluent/Eloquent query to an array
   *
   * @param  object $query The query
   * @param  string $value The attribute to use as value
   * @param  string $key   The attribute to use as key
   * @return array         A data array
   */
  public function queryToArray($query, $value, $key)
  {
    // Automatically fetch Lang objects for people who store translated options lists
    // Same of unfetched queries
    if (method_exists($query, 'get')) $query = $query->get();
    if ($query instanceof Collection) $query = $query->toArray();

    if(!is_array($query)) $query = (array) $query;

    // Populates the new options
    foreach ($query as $model) {

      // If it's an array, convert to object
      if(is_array($model)) $model = (object) $model;

      // Calculate the value
      if($value and isset($model->$value)) $modelValue = $model->$value;
      elseif(method_exists($model, '__toString')) $modelValue = $model->__toString();
      else $modelValue = null;

      // Calculate the key
      if($key and isset($model->$key)) $modelKey = $model->$key;
      elseif(method_exists($model, 'getKey')) $modelKey = $model->getKey();
      elseif(isset($model->id)) $modelKey = $model->id;
      else $modelKey = $modelValue;

      // Skip if no text value found
      if(!$modelValue) continue;

      $array[$modelKey] = (string) $modelValue;
    }

    return isset($array) ? $array : $query;
  }
}
