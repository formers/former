<?php
namespace Former;

use \HTML;

class Helpers
{
  /**
   * Build an inline help
   *
   * @param  string $value      The help text
   * @param  array  $attributes Its attributes
   * @return string             A .help-inline p
   */
  public static function inlineHelp($value, $attributes = array())
  {
    $attributes = static::addClass($attributes, 'help-inline');

    return '<span '.HTML::attributes($attributes).'>'.$value.'</span>';
  }

  /**
   * Build a block help
   *
   * @param  string $value      The help text
   * @param  array  $attributes Its attributes
   * @return string             A .help-block p
   */
  public static function blockHelp($value, $attributes = array())
  {
    $attributes = static::addClass($attributes, 'help-block');

    return '<p '.HTML::attributes($attributes).'>'.$value.'</p>';
  }

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
}