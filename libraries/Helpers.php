<?php
/**
 * Helpers
 *
 * Various helpers used by all Former classes
 */
namespace Former;

use \HTML, \Lang;

class Helpers
{
  /**
   * Adds a class to an attributes array
   *
   * @param  array  $attributes An array of attributes
   * @param  string $class      The class to add
   * @return array              The modified attributes array
   */
  public static function addClass($attributes, $class)
  {
    $attributes['class'] = isset($attributes['class'])
      ? $attributes['class']. ' ' .$class
      : $class;

    return $attributes;
  }

  /**
   * Translates a string by trying several fallbacks
   *
   * @param  string $key      The key to translate
   * @param  string $fallback The ultimate fallback
   * @return string           A translated string
   */
  public static function translate($key, $fallback = null)
  {
    if(!$fallback) $fallback = $key;

    // Assure we don't already have a Lang object
    if($key instanceof Lang) return $key->get();

    // Search for the key itself
    $translation = Lang::line($key)->get(null, '');

    // If not found, search in the field attributes
    if(!$translation) $translation =
      Lang::line(Former::$translateFrom.'.'.$key)->get(null,
      $fallback);

    return ucfirst($translation);
  }

  /**
   * Transforms a Fluent/Eloquent query to an array
   *
   * @param  object $query The query
   * @param  string $value The attribute to use as value
   * @param  string $key   The attribute to use as key
   * @return array         A data array
   */
  public static function queryToArray($query, $value, $key)
  {
    if(!$key) $key = 'id';

    // Fetch the Query if it hasn't been
    if($query instanceof \Laravel\Database\Eloquent\Query or
       $query instanceof \Laravel\Database\Query) {
      $query = $query->get();
    }

    // Populates the new options
    foreach($query as $model) {

      // If not an array, convert to object
      if(is_array($model)) $model = (object) $model;

      // Filter out wrong attributes
      if(!isset($model->$value)) continue;
      if(!isset($model->$key)) $key = $value;

      $array[$model->$key] = $model->$value;
    }

    return isset($array) ? $array : $query;
  }
}
