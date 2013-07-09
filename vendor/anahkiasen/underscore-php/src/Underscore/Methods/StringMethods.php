<?php
namespace Underscore\Methods;

use Illuminate\Support\Str;
use Underscore\Types\String;

/**
 * Methods to manage strings
 */
class StringMethods extends Str
{

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// CREATE  /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Create a string from a number
   *
   * @param  integer $count A number
   * @param  string  $many  If many
   * @param  string  $one   If one
   * @param  string  $zero  If one
   * @return string         A string
   */
  public static function accord($count, $many, $one, $zero = null)
  {
    if($count == 1) $output = $one;
    else if($count == 0 and !empty($zero)) $output = $zero;
    else $output = $many;

    return sprintf($output, $count);
  }

  /**
   * Generates a random suite of words
   *
   * @param integer  $words  The number of words
   * @param integer  $length The length of each word
   *
   * @return string
   */
  public static function randomStrings($words, $length = 10)
  {
    return String::from('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
      ->shuffle()
      ->split($length)
      ->slice(0, $words)
      ->implode(' ')
      ->obtain();
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// ANALYZE /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get a String's length
   *
   * @param string $string
   *
   * @return integer
   */
  public static function length($string)
  {
    return mb_strlen($string);
  }

  /**
   * Check if a string is an IP
   *
   * @return boolean
   */
  public static function isIp($string)
  {
    return filter_var($string, FILTER_VALIDATE_IP) !== false;
  }

  /**
   * Check if a string is an email
   *
   * @return boolean
   */
  public static function isEmail($string)
  {
    return filter_var($string, FILTER_VALIDATE_EMAIL) !== false;
  }

  /**
   * Check if a string is an url
   *
   * @return boolean
   */
  public static function isUrl($string)
  {
    return filter_var($string, FILTER_VALIDATE_URL) !== false;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// FETCH FROM ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Find one or more needles in one or more haystacks
   *
   * @param  array|string $string        The haystack(s) to search in
   * @param  array|string $needle        The needle(s) to search for
   * @param  boolean      $caseSensitive Whether the function is case sensitive or not
   * @param  boolean      $absolute      Whether all needle need to be found or whether one is enough
   * @return boolean      Found or not
   */
  public static function find($string, $needle, $caseSensitive = false, $absolute = false)
  {
    // If several needles
    if (is_array($needle) or is_array($string)) {

      if (is_array($needle)) {
        $sliceFrom = $needle;
        $sliceTo   = $string;
      } else {
        $sliceFrom = $string;
        $sliceTo   = $needle;
      }

      $found = 0;
      foreach ($sliceFrom as $need) {
        if(static::find($sliceTo, $need, $absolute, $caseSensitive)) $found++;
      }

      return ($absolute) ? count($sliceFrom) == $found : $found > 0;
    }

    // If not case sensitive
    if (!$caseSensitive) {
      $string = strtolower($string);
      $needle = strtolower($needle);
    }

    // If string found
    $pos = strpos($string, $needle);

    return !($pos === false);
  }

  /**
   * Slice a string with another string
   */
  public static function slice($string, $slice)
  {
    $sliceTo   = static::sliceTo($string, $slice);
    $sliceFrom = static::sliceFrom($string, $slice);

    return array($sliceTo, $sliceFrom);
  }

  /**
   * Slice a string from a certain point
   */
  public static function sliceFrom($string, $slice)
  {
    $slice = strpos($string, $slice);

    return substr($string, $slice);
  }

  /**
   * Slice a string up to a certain point
   */
  public static function sliceTo($string, $slice)
  {
    $slice = strpos($string, $slice);

    return substr($string, 0, $slice);
  }

  /**
   * Get the base class in a namespace
   *
   * @param  string $string
   *
   * @return string
   */
  public static function baseClass($string)
  {
    $string = static::replace($string, '\\', '/');

    return basename($string);
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////////// ALTER //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Prepend a string with another
   *
   * @param string $string The string
   * @param string $with   What to prepend with
   *
   * @return string
   */
  public static function prepend($string, $with)
  {
    return $with.$string;
  }

  /**
   * Append a string to another
   *
   * @param string $string The string
   * @param string $with   What to append with
   *
   * @return string
   */
  public static function append($string, $with)
  {
    return $string.$with;
  }

  /**
   * Remove part of a string
   */
  public static function remove($string, $remove)
  {
    // If we only have one string to remove
    if(!is_array($remove)) $string = str_replace($remove, null, $string);

    // Else, use Regex
    else $string =  preg_replace('#(' .implode('|', $remove). ')#', null, $string);

    // Trim and return
    return trim($string);
  }

  /**
   * Correct arguments order for str_replace
   */
  public static function replace($string, $replace, $with)
  {
    return str_replace($replace, $with, $string);
  }

  /**
   * Toggles a string between two states
   *
   * @param  string  $string The string to toggle
   * @param  string  $first  First value
   * @param  string  $second Second value
   * @param  boolean $loose  Whether a string neither matching 1 or 2 should be changed
   * @return string          The toggled string
   */
  public static function toggle($string, $first, $second, $loose = false)
  {
    // If the string given match none of the other two, and we're in strict mode, return it
    if (!$loose and !in_array($string, array($first, $second))) {
      return $string;
    }

    return $string == $first ? $second : $first;
  }

  /**
   * Slugifies a string
   */
  public static function slugify($string, $separator = '-')
  {
    $string = str_replace('_', ' ', $string);

    return static::slug($string, $separator);
  }

  /**
   * Explode a string into an array
   */
  public static function explode($string, $with, $limit = null)
  {
    if (!$limit) return explode($with, $string);

    return explode($with, $string, $limit);
  }

  /**
   * Lowercase a string
   *
   * @param string $string
   *
   * @return string
   */
  public static function lower($string)
  {
    return mb_strtolower($string, 'UTF-8');
  }

  /**
   * Lowercase a string
   *
   * @param string $string
   *
   * @return string
   */
  public static function upper($string)
  {
    return mb_strtoupper($string, 'UTF-8');
  }

  /**
   * Convert a string to title case
   *
   * @param string $string
   *
   * @return string
   */
  public static function title($string)
  {
    return mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CASE SWITCHERS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Convert a string to PascalCase
   *
   * @param string  $string
   *
   * @return string
   */
  public static function toPascalCase($string)
  {
    return static::studly($string);
  }

  /**
   * Convert a string to snake_case
   *
   * @param string  $string
   *
   * @return string
   */
  public static function toSnakeCase($string)
  {
    return preg_replace_callback('/([A-Z])/', function($match) {
      return '_'.strtolower($match[1]);
    }, $string);
  }

  /**
   * Convert a string to camelCase
   *
   * @param string  $string
   *
   * @return string
   */
  public static function toCamelCase($string)
  {
    return static::camel($string);
  }

}
