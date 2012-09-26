<?php
/**
 * Form
 *
 * Construct and manages the form wrapping all fields
 */
namespace Former;

class Framework
{
  /**
   * The current framework being used
   * @var string
   */
  private static $framework = 'zurb';

  /**
   * The field sizes available
   * @var array
   */
  private static $sizes = array(
    'bootstrap' => array(
      'mini', 'small', 'medium', 'large', 'xlarge', 'xxlarge',
      'span1', 'span2', 'span3', 'span4', 'span5', 'span6', 'span7',
      'span8', 'span9', 'span10', 'span11', 'span12'),

    'zurb' => array(
      'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight',
      'nine', 'ten', 'eleven', 'twelve'),
  );

  /**
   * Change the framework currently being used by Former
   *
   * @param  string $framework A framework, or null for none
   */
  public static function useFramework($framework = null)
  {
    if (in_array($framework, array('bootstrap', 'zurb')) or
        is_null($framework)) {
      static::$framework = $framework;
    }
  }

  /**
   * Filter a size asked according to the framework
   *
   * @param  array  $sizes An array of asked classes
   * @return string        A field size
   */
  public static function getFieldSizes($sizes)
  {
    // List all available sizes
    $available = array_get(static::$sizes, static::$framework, array());

    if ($sizes = array_intersect($available, $sizes)) {

      // Get size from array and format it
      $size = $sizes[key($sizes)];
      if(static::$framework == 'bootstrap') {
        $size = starts_with($size, 'span') ? $size : 'input-'.$size;
      }
      elseif(static::$framework == 'zurb') $size = $size. ' columns';
      else $size = null;

      return $size;
  }
}
