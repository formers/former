<?php
/**
 * Form
 *
 * Construct and manages the form wrapping all fields
 */
namespace Former;

use \HTML;

class Framework
{
  /**
   * The current framework being used
   * @var string
   */
  private static $framework = 'bootstrap';

  /**
   * The availables states for a control group
   * @var array
   */
  private static $states = array(
    'bootstrap' => array('success', 'warning', 'error', 'info'),
    'zurb'     => array('error')
  );

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

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// FIELD ELEMENTS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Build an inline help
   *
   * @param  string $value      The help text
   * @param  array  $attributes Its attributes
   * @return string             A .help-inline p
   */
  public static function inlineHelp($value, $attributes = array())
  {
    // Return the correct syntax according to framework
    switch(static::$framework) {
      case 'bootstrap':
        $attributes = Helpers::addClass($attributes, 'help-inline');
        $help = '<span'.HTML::attributes($attributes).'>'.$value.'</span>';
        break;
      case 'zurb':
      default:
        $help = '<small' .HTML::attributes($attributes). '>' .$value. '</small>';
        break;
    }

    return $help;
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
    // Block helps are only available in Bootstrap
    if(self::isnt('bootstrap')) return static::inlineHelp($value, $attributes);

    $attributes = Helpers::addClass($attributes, 'help-block');

    return '<p '.HTML::attributes($attributes).'>'.$value.'</p>';
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// FIELD WRAPPERS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  public static function getFieldClasses($field)
  {
    // Wrap field in .controls if necessary
    if(self::is('bootstrap')) {
      $field = '<div class="controls">' .$field. '</div>';
    }

    return $field;
  }

  /**
   * Add the correct classes to a label
   *
   * @param  array $attributes The label's attributes
   * @return array             The modified attributes
   */
  public static function getLabelClasses($attributes)
  {
    if(static::is('bootstrap')) {
      $attributes = Helpers::addClass($attributes, 'control-label');
    }

    return $attributes;
  }

  /**
   * Add the correct classes to a group
   *
   * @param  array $attributes The group's attributes
   * @return array             The modified attributes
   */
  public static function getGroupClasses($attributes)
  {
    if(static::is('bootstrap')) {
      $attributes = Helpers::addClass($attributes, 'control-group');
    }

    return $attributes;
  }

  /**
   * Filter a size asked according to the framework
   *
   * @param  array  $sizes An array of asked classes
   * @return string        A field size
   */
  public static function getFieldSizes($sizes)
  {
    if(self::is(null)) return null;

    // List all available sizes
    $available = array_get(static::$sizes, static::$framework, array());

    // Filter sizes
    $sizes = array_intersect($available, $sizes);

    // Get size from array and format it
    $size = array_pop($sizes);
    if($size) {
      if(self::is('bootstrap')) $size = starts_with($size, 'span') ? $size : 'input-'.$size;
      elseif(self::is('zurb')) $size;
      else $size = null;
    }

    return $size;
  }

  /**
   * Filter a control-group state
   *
   * @param  string $state A state to apply
   * @return mixed         The filtered state or null
   */
  public static function getState($state)
  {
    if(in_array($state, static::$states[static::$framework])) return $state;
    else return null;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// INTERFACE ////////////////////////////
  ////////////////////////////////////////////////////////////////////

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
   * Check what is the current framework in use
   *
   * @param  string  $framework The framework to check against
   * @return boolean            In use or not
   */
  public static function is($framework)
  {
    return static::$framework == $framework;
  }

  /**
   * Check if a framework isn't the one in use
   *
   * @param  string  $framework The framework to check against
   * @return boolean            Not in use or not
   */
  public static function isnt($framework)
  {
    return static::$framework != $framework;
  }

}
