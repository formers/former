<?php
namespace HtmlObject\Traits;

/**
 * Static helpers used troughout the classes
 */
class Helpers
{

  /**
   * Get a value from an array
   *
   * @param array  $array
   * @param string $key
   * @param string $fallback
   *
   * @return mixed
   */
  public static function arrayGet($array, $key, $fallback = null)
  {
    return isset($array[$key]) ? $array[$key] : $fallback;
  }

  /**
   * Build a list of HTML attributes from an array
   *
   * @param  array  $attributes
   * @return string
   */
  public static function parseAttributes($attributes)
  {
    $html = array();

    foreach ((array) $attributes as $key => $value) {
      if (is_numeric($key)) $key = $value;
      if (!$value and !in_array($key, array('value'))) continue;

      $html[] = $key. '="' .static::entities($value). '"';
    }

    return (count($html) > 0) ? ' '.implode(' ', $html) : '';
  }

  /**
   * Convert HTML characters to HTML entities
   *
   * The encoding in $encoding will be used
   *
   * @param  string $value
   * @return string
   */
  protected static function entities($value)
  {
    return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
  }

}
