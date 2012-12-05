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
   * Illuminate application instance.
   * @var Illuminate/Foundation/Application
   */
  protected $app;

  /**
   * The current framework being used
   * @var string
   */
  private $framework;

  /**
   * The availables states for a control group
   * @var array
   */
  private $states = array(
    'bootstrap' => array('success', 'warning', 'error', 'info'),
    'zurb'      => array('error')
  );

  /**
   * The button types available
   * @var array
   */
  private $types = array(
    'bootstrap' => array(
      'large', 'small', 'mini',
      'block', 'info', 'inverse', 'link', 'primary', 'success', 'warning'),
  );

  /**
   * The field sizes available
   * @var array
   */
  private $sizes = array(
    'bootstrap' => array(
      'mini', 'small', 'medium', 'large', 'xlarge', 'xxlarge',
      'span1', 'span2', 'span3', 'span4', 'span5', 'span6', 'span7',
      'span8', 'span9', 'span10', 'span11', 'span12'),

    'zurb' => array(
      'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight',
      'nine', 'ten', 'eleven', 'twelve'),
  );

  public function __construct($app)
  {
    $this->app = $app;
  }

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
  public function inlineHelp($value, $attributes = array())
  {
    // Return the correct syntax according to framework
    switch ($this->current()) {
      case 'bootstrap':
        $attributes = $this->app['former.helpers']->addClass($attributes, 'help-inline');
        $help = '<span'.$this->app['former.helpers']->attributes($attributes).'>'.$value.'</span>';
        break;
      case 'zurb':
      default:
        $help = '<small' .$this->app['former.helpers']->attributes($attributes). '>' .$value. '</small>';
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
  public function blockHelp($value, $attributes = array())
  {
    // Block helps are only available in Bootstrap
    if($this->isnt('bootstrap')) return $this->inlineHelp($value, $attributes);

    $attributes = $this->app['former.helpers']->addClass($attributes, 'help-block');

    return '<p '.$this->app['former.helpers']->attributes($attributes).'>'.$value.'</p>';
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
  public function icon($icon, $attributes = array())
  {
    if ($this->is('bootstrap')) {

      // White icon
      if (String::contains($icon, 'white')) {
        $icon = trim(str_replace('white', null, $icon), '-');
        $attributes = $this->app['former.helpers']->addClass($attributes, 'icon-white');
      }

      // Check for empty icons
      if (!$icon) return false;
      $attributes = $this->app['former.helpers']->addClass($attributes, 'icon-'.$icon);

      // Create icon
      $icon = '<i'.$this->app['former.helpers']->attributes($attributes).'></i>';
    }

    return $icon;
  }

  /**
   * Creates a field for a label
   *
   * @param Field $field The field
   * @return string A field label
   */
  public function label($field, $label = null)
  {
    // Get the label and its informations
    if (!$label) $label = $field->label;

    $attributes = Arrays::get($label, 'attributes', array());
    $label = Arrays::get($label, 'label');
    if (!$label) return false;

    // Append required text
    if ($field->isRequired()) {
      $label .= Config::get('required_text');
    }

    // Get the field name to link the label to it
    if ($field->isCheckable()) {
      return '<label'.$this->app['former.helpers']->attributes($attributes).'>'.$label.'</label>';
    }

    return HTML::decode(Form::label($field->name, $label, $attributes));
  }

  /**
   * Wrap fields in a control wrapper
   *
   * @param Field $field The field to wrap
   * @return string A wrapped field
   */
  public function getFieldClasses($field)
  {
    // Wrap field in .controls if necessary
    if ($this->is('bootstrap')) {
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
  public function getLabelClasses($attributes)
  {
    if ($this->is('bootstrap')) {
      $attributes = $this->app['former.helpers']->addClass($attributes, 'control-label');
    }

    return $attributes;
  }

  /**
   * Add the correct classes to a group
   *
   * @param  array $attributes The group's attributes
   * @return array             The modified attributes
   */
  public function getGroupClasses($attributes)
  {
    if ($this->is('bootstrap')) {
      $attributes = $this->app['former.helpers']->addClass($attributes, 'control-group');
    }

    return $attributes;
  }

  /**
   * Filter a size asked according to the framework
   *
   * @param  array  $sizes An array of asked classes
   * @return string        A field size
   */
  public function getFieldSizes($sizes)
  {
    if($this->is(null)) return null;

    // Filter sizes
    $sizes = $this->getAvailable($sizes, 'sizes');

    // Get size from array and format it
    $size = array_pop($sizes);
    if ($size) {
      if($this->is('bootstrap')) $size = starts_with($size, 'span') ? $size : 'input-'.$size;
      elseif($this->is('zurb')) $size;
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
  public function getButtonTypes($types)
  {
    if($this->is(null)) return null;

    // Filter types
    $types = $this->getAvailable($types, 'types');

    // Format classes
    if ($this->is('bootstrap')) {
      $types = $this->prependClasses($types, 'btn-');
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
  public function getState($state)
  {
    if(in_array($state, $this->states[$this->current()])) return $state;
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
  public function current()
  {
    return Config::get('framework');
  }

  /**
   * Change the framework currently being used by Former
   *
   * @param  string $framework A framework, or null for none
   */
  public function useFramework($framework = null)
  {
    if (in_array($framework, array('bootstrap', 'zurb')) or
        is_null($framework)) {
      Config::set('framework', $framework);
    }

    return $this->current();
  }

  /**
   * Check what is the current framework in use
   *
   * @param  string  $framework The framework to check against
   * @return boolean            In use or not
   */
  public function is($framework)
  {
    return $this->current() == $framework;
  }

  /**
   * Check if a framework isn't the one in use
   *
   * @param  string  $framework The framework to check against
   * @return boolean            Not in use or not
   */
  public function isnt($framework)
  {
    return $this->current() != $framework;
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
  private function prependClasses($classes, $class)
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
  private function getAvailable($classes, $from)
  {
    // List all available classes
    $available = Arrays::get($this->from, $this->current(), array());

    // Filter classes
    return array_intersect($available, $classes);
  }

}
