<?php
/**
 * Form
 *
 * Construct and manages the form wrapping all fields
 */
namespace Former;

use \Form;
use \HTML;

class Framework
{
  /**
   * The current framework being used
   * @var string
   */
  private static $framework;

  /**
   * The availables states for a control group
   * @var array
   */
  private static $states = array(
    'bootstrap' => array('success', 'warning', 'error', 'info'),
    'zurb'      => array('error')
  );

  /**
   * The button types available
   * @var array
   */
  private static $types = array(
    'bootstrap' => array(
      'large', 'small', 'mini',
      'block', 'info', 'inverse', 'link', 'primary', 'success', 'warning'),
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
    switch (static::current()) {
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
    if(static::isnt('bootstrap')) return static::inlineHelp($value, $attributes);

    $attributes = Helpers::addClass($attributes, 'help-block');

    return '<p '.HTML::attributes($attributes).'>'.$value.'</p>';
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// FIELD WRAPPERS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Creates a basic icon
   *
   * @param string $icon       The icon id
   * @param array  $attributes Facultative attributes
   *
   * @return string An icon tag
   */
  public static function icon($icon, $attributes = array())
  {
    if (static::is('bootstrap')) {

      // White icon
      if (str_contains($icon, 'white')) {
        $icon = trim(str_replace('white', null, $icon), '-');
        $attributes = Helpers::addClass($attributes, 'icon-white');
      }

      // Check for empty icons
      if (!$icon) return false;
      $attributes = Helpers::addClass($attributes, 'icon-'.$icon);

      // Create icon
      $icon = '<i'.HTML::attributes($attributes).'></i>';
    }

    return $icon;
  }

  /**
   * Creates a field for a label
   *
   * @param Field $field The field
   * @return string A field label
   */
  public static function label($field, $label = null)
  {
    // Get the label and its informations
    if (!$label) $label = $field->label;

    $attributes = array_get($label, 'attributes', array());
    $label = array_get($label, 'label');
    if (!$label) return false;

    // Append required text
    if ($field->isRequired()) {
      $label .= Config::get('required_text');
    }

    // Get the field name to link the label to it
    if ($field->isCheckable()) {
      return '<label'.HTML::attributes($attributes).'>'.$label.'</label>';
    }

    return HTML::decode(Form::label($field->name, $label, $attributes));
  }

  /**
   * Wrap fields in a control wrapper
   *
   * @param Field $field The field to wrap
   * @return string A wrapped field
   */
  public static function getFieldClasses($field)
  {
    // Wrap field in .controls if necessary
    if (static::is('bootstrap')) {
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
    if (static::is('bootstrap')) {
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
    if (static::is('bootstrap')) {
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
    if(static::is(null)) return null;

    // Filter sizes
    $sizes = static::getAvailable($sizes, 'sizes');

    // Get size from array and format it
    $size = array_pop($sizes);
    if ($size) {
      if(static::is('bootstrap')) $size = starts_with($size, 'span') ? $size : 'input-'.$size;
      elseif(static::is('zurb')) $size;
      else $size = null;
    }

    return $size;
  }

  /**
   * Filter a button type according to the framework
   *
   * @param  array $types An array of types
   * @return array        A filtered array
   */
  public static function getButtonTypes($types)
  {
    if(static::is(null)) return null;

    // Filter types
    $types = static::getAvailable($types, 'types');

    // Format classes
    if (static::is('bootstrap')) {
      $types = static::prependClasses($types, 'btn-');
      $types[] = 'btn';
    } else $types = null;

    return $types;
  }

  /**
   * Filter a control-group state
   *
   * @param  string $state A state to apply
   * @return mixed         The filtered state or null
   */
  public static function getState($state)
  {
    if(in_array($state, static::$states[static::current()])) return $state;
    else return null;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// INTERFACE ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get current framework in use
   *
   * @return string The framework used by Former
   */
  public static function current()
  {
    return Config::get('framework');
  }

  /**
   * Change the framework currently being used by Former
   *
   * @param  string $framework A framework, or null for none
   */
  public static function useFramework($framework = null)
  {
    if (in_array($framework, array('bootstrap', 'zurb')) or
        is_null($framework)) {
      Config::set('framework', $framework);
    }

    return static::current();
  }

  /**
   * Check what is the current framework in use
   *
   * @param  string  $framework The framework to check against
   * @return boolean            In use or not
   */
  public static function is($framework)
  {
    return static::current() == $framework;
  }

  /**
   * Check if a framework isn't the one in use
   *
   * @param  string  $framework The framework to check against
   * @return boolean            Not in use or not
   */
  public static function isnt($framework)
  {
    return static::current() != $framework;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// HELPERS ///////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Prepend an array of classes with a string
   *
   * @param  array  $classes An array of classes
   * @param  string $class   The string to prepend them with
   * @return array           An array of prepended classes
   */
  private static function prependClasses($classes, $class)
  {
    // Add prefix to each class
    foreach ($classes as $key => $value) {
      if($value != $class) $classes[$key] = $class.$value;
    }

    return $classes;
  }

  /**
   * Get all available classes from an array of classes
   *
   * @param  array  $classes An array of classes to filter
   * @param  string $from    The kind of classes to get
   * @return array           Filtered array
   */
  private static function getAvailable($classes, $from)
  {
    // List all available classes
    $available = array_get(static::$$from, static::current(), array());

    // Filter classes
    return array_intersect($available, $classes);
  }

}
