<?php
namespace Underscore\Methods;

use Closure;

/**
 * Abstract Collection type
 * Methods that apply to both objects and arrays
 */
abstract class CollectionMethods
{

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// ANALYZE //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Check if an array has a given key
   */
  public static function has($array, $key)
  {
    // Generate unique string to use as marker
    $unfound = StringMethods::random(5);

    return static::get($array, $key, $unfound) !== $unfound;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// FETCH FROM ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get a value from an collection using dot-notation
   *
   * @param array  $collection The collection to get from
   * @param string $key        The key to look for
   * @param mixed  $default    Default value to fallback to
   *
   * @return mixed
   */
  public static function get($collection, $key, $default = null)
  {
    if (is_null($key)) return $collection;

    // Crawl through collection, get key according to object or not
    foreach (explode('.', $key) as $segment) {

      // If object
      if (is_object($collection)) {
        if (!isset($collection->$segment)) return $default instanceof Closure ? $default() : $default;
        else $collection = $collection->$segment;

      // If array
      } else {
        if (!isset($collection[$segment])) return $default instanceof Closure ? $default() : $default;
        else $collection = $collection[$segment];
      }
    }

    return $collection;
  }

  /**
   * Set a value in a collection using dot notation
   *
   * @param mixed  $collection The collection
   * @param string $key        The key to set
   * @param mixed  $value      Its value
   *
   * @return mixed
   */
  public static function set($collection, $key, $value)
  {
    static::internalSet($collection, $key, $value);

    return $collection;
  }

  /**
   * Get a value from a collection and set it if it wasn't
   *
   * @param mixed  $collection The collection
   * @param string $key        The key
   * @param mixed  $default    The default value to set if it isn't
   *
   * @return mixed
   */
  public static function setAndGet(&$collection, $key, $default = null)
  {
    // If the key doesn't exist, set it
    if (!static::has($collection, $key)) {
      $collection = static::set($collection, $key, $default);
    }

    return static::get($collection, $key);
  }

  /**
   * Remove a value from an array using dot notation
   */
  public static function remove($collection, $key)
  {
    // Recursive call
    if (is_array($key)) {
      foreach($key as $k) static::internalRemove($collection, $k);

      return $collection;
    }

    static::internalRemove($collection, $key);

    return $collection;
  }

  /**
   * Fetches all columns $property from a multimensionnal array
   */
  public static function pluck($collection, $property)
  {
    $plucked = array_map(function($value) use ($property) {
      return ArraysMethods::get($value, $property);

    }, (array) $collection);

    // Convert back to object if necessary
    if (is_object($collection)) $plucked = (object) $plucked;

    return $plucked;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// ANALYZE //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get all keys from a collection
   */
  public static function keys($collection)
  {
    return array_keys((array) $collection);
  }

  /**
   * Get all values from a collection
   */
  public static function values($collection)
  {
    return array_values((array) $collection);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// ALTER ///////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Replace a key with a new key/value pair
   */
  public static function replace($collection, $replace, $key, $value)
  {
    $collection = static::remove($collection, $replace);
    $collection = static::set($collection, $key, $value);

    return $collection;
  }

  /**
   * Sort a collection by value, by a closure or by a property
   * If the sorter is null, the collection is sorted naturally
   */
  public static function sort($collection, $sorter = null, $direction = 'asc')
  {
    $collection = (array) $collection;

    // Get correct PHP constant for direction
    $direction = (strtolower($direction) == 'desc') ? SORT_DESC : SORT_ASC;

    // Transform all values into their results
    if ($sorter) {
      $results = ArraysMethods::each($collection, function($value) use ($sorter) {
        return is_callable($sorter) ? $sorter($value) : ArraysMethods::get($value, $sorter);
      });
    } else $results = $collection;

    // Sort by the results and replace by original values
    array_multisort($results, $direction, SORT_REGULAR, $collection);

    return $collection;
  }

  /**
   * Group values from a collection according to the results of a closure
   */
  public static function group($collection, $grouper)
  {
    $collection = (array) $collection;
    $result = array();

    // Iterate over values, group by property/results from closure
    foreach ($collection as $key => $value) {
      $key = is_callable($grouper) ? $grouper($value, $key) : ArraysMethods::get($value, $grouper);
      if (!isset($result[$key])) $result[$key] = array();

      // Add to results
      $result[$key][] = $value;
    }

    return $result;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Internal mechanic of set method
   */
  protected static function internalSet(&$collection, $key, $value)
  {
    if (is_null($key)) return $collection = $value;

    // Explode the keys
    $keys = explode('.', $key);

    // Crawl through the keys
    while (count($keys) > 1) {
      $key = array_shift($keys);

      // If we're dealing with an object
      if (is_object($collection)) {
        if (!isset($collection->$key) or !is_array($collection->$key)) {
          $collection->$key = array();
        }
        $collection =& $collection->$key;

      // If we're dealing with an array
      } else {
        if (!isset($collection[$key]) or !is_array($collection[$key])) {
          $collection[$key] = array();
        }
        $collection =& $collection[$key];
      }
    }

    // Bind final tree on the collection
    $key = array_shift($keys);
    if (is_array($collection)) $collection[$key] = $value;
    else $collection->$key = $value;
  }

  /**
   * Internal mechanics of remove method
   */
  protected static function internalRemove(&$collection, $key)
  {
    // Explode keys
    $keys = explode('.', $key);

    // Crawl though the keys
    while (count($keys) > 1) {
      $key = array_shift($keys);

      // If we're dealing with an object
      if (is_object($collection)) {
        if (!isset($collection->$key)) {
          return false;
        }
        $collection =& $collection->$key;

      // If we're dealing with an array
      } else {
        if (!isset($collection[$key])) {
          return false;
        }
        $collection =& $collection[$key];
      }
    }

    $key = array_shift($keys);
    if (is_object($collection)) unset($collection->$key);
    else unset($collection[$key]);
  }

}
