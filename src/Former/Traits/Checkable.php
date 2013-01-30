<?php
/**
 * Checkable
 *
 * Abstract methods inherited by Checkbox and Radio
 */
namespace Former\Traits;

use \Underscore\Types\Arrays;
use \Former\Helpers;

abstract class Checkable extends Field
{
  /**
   * Renders the checkables as inline
   * @var boolean
   */
  protected $inline = false;

  /**
   * Add a text to a single element
   * @var string
   */
  protected $text = null;

   /**
   * Renders the checkables as grouped
   * @var boolean
   */
  protected $grouped = false;

  /**
   * The checkable items currently stored
   * @var array
   */
  protected $items = array();

  /**
   * The type of checkable item
   * @var string
   */
  protected $checkable = null;

  /**
   * An array of checked items
   * @var array
   */
  protected $checked = array();

  /**
   * The checkable currently being focused on
   * @var integer
   */
  protected $focus = null;

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// CORE METHODS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Apply methods to focused checkable
   */
  public function __call($method, $parameters)
  {
    $focused = $this->setOnFocused('attributes.'.$method, Arrays::get($parameters, 0));
    if ($focused) return $this;

    return parent::__call($method, $parameters);
  }

  /**
   * Prints out the currently stored checkables
   */
  public function render()
  {
    $html = null;

    // Multiple items
    if ($this->items) {
      foreach ($this->items as $key => $item) {
        $value = $this->isCheckbox() ? 1 : $key;
        $html .= $this->createCheckable($item, $value);
      }

      return $html;
    }

    // Single item
    return $this->createCheckable(array(
      'name'  => $this->name,
      'label' => $this->text,
      'value' => $this->value
    ));
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// FIELD METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Focus on a particular checkable
   *
   * @param integer $on The checkable to focus on
   */
  public function on($on)
  {
    if (!isset($this->items[$on])) return $this;
    else $this->focus = $on;

    return $this;
  }

  /**
   * Set the checkables as inline
   */
  public function inline()
  {
    $this->inline = true;

    return $this;
  }

  /**
   * Set the checkables as stacked
   */
  public function stacked()
  {
    $this->inline = false;

    return $this;
  }

  /**
   * Set the checkables as grouped
   */
  public function grouped()
  {
    $this->grouped = true;

    return $this;
  }

  /**
   * Add text to a single checkable
   *
   * @param  string $text The checkable label
   */
  public function text($text)
  {
    // Translate and format
    $text = Helpers::translate($text);

    // Apply on focused if any
    $focused = $this->setOnFocused('label', $text);
    if ($focused) return $this;

    $this->text = $text;

    return $this;
  }

  /**
   * Check a specific item
   *
   * @param  string $checked The checkable to check, or an array of checked items
   */
  public function check($checked = true)
  {
    // If we're setting all the checked items at once
    if (is_array($checked)) {
      $this->checked = $checked;

    // Checking an item in particular
    } elseif (is_string($checked) or is_int($checked)) {
      $this->checked[$checked] = true;

    // Only setting a single item
    } else {
      $this->checked[$this->name] = (bool) $checked;
    }

    return $this;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// INTERNAL METHODS ////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Creates a serie of checkable items
   *
   * @param array $_items Items to create
   */
  protected function items($_items)
  {
    // If passing an array
    if(sizeof($_items) == 1 and
       is_array($_items[0]))
         $_items = $_items[0];

    // Iterate through items, assign a name and a label to each
    $count = 0;
    foreach ($_items as $label => $name) {

      // Define a fallback name in case none is found
      $fallback = $this->isCheckbox()
        ? $this->name.'_'.$count
        : $this->name;

      // Grouped fields
      if ($this->isGrouped()) {
        $attributes['id'] = str_replace('[]', null, $fallback);
        $fallback = str_replace('[]', null, $this->name).'[]';
      }

      // If we haven't any name defined for the checkable, try to compute some
      if (!is_string($label) and !is_array($name)) {
        $label = $name;
        $name  = $fallback;
      }

      // If we gave custom information on the item, add them
      if (is_array($name)) {
        $attributes = $name;
        $name = Arrays::get($attributes, 'name', $fallback);
        unset($attributes['name']);
      }

      // Store all informations we have in an array
      $item = array(
        'name' => $name,
        'label' => Helpers::translate($label),
      );
      if(isset($attributes)) $item['attributes'] = $attributes;

      $this->items[] = $item;
      $count++;
    }
  }

  /**
   * Renders a checkable
   *
   * @param  string $item          A checkable item
   * @param  string $fallbackValue A fallback value if none is set
   * @return string
   */
  protected function createCheckable($item, $fallbackValue = 1)
  {
    // Extract informations
    extract($item);

    // Set default values
    if(!isset($attributes)) $attributes = array();
    if(isset($attributes['value'])) $value = $attributes['value'];
    if(!isset($value) or $value === $this->app['former']->getOption('unchecked_value')) $value = $fallbackValue;

    // If inline items, add class
    $isInline = $this->inline ? ' inline' : null;

    // Merge custom attributes with global attributes
    $attributes = array_merge($this->attributes, $attributes);
    if (!isset($attributes['id'])) $attributes['id'] = $name.$this->unique($name);

    // Create field
    $field = call_user_func(array($this->app['form'], $this->checkable), $name, $value, $this->isChecked($name, $value), $attributes);

    // Add hidden checkbox if requested
    if ($this->app['former']->getOption('push_checkboxes')) {
      $field = $this->app['form']->hidden($name, $this->app['former']->getOption('unchecked_value')) . $field;
    }

    // If no label to wrap, return plain checkable
    if(!$label) return $field;

    return '<label for="' .$attributes['id']. '" class="' .$this->checkable.$isInline. '">' .$field.$label. '</label>';
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// HELPERS //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Generate an unique ID for a field
   *
   * @param string $name The field's name
   * @return string A field number to use
   */
  protected function unique($name)
  {
    // Register the field with Laravel
    $labels = $this->app['form']->labels;
    $labels[] = $name;
    $this->app['form']->labels = $labels;

    // Count number of fields with the same ID
    $where = array_filter($this->app['form']->labels, function($label) use ($name) {
      return $label == $name;
    });
    $unique = sizeof($where);

    // In case the field doesn't need to be numbered
    if ($unique < 2 or empty($this->items)) return false;
    return $unique;
  }

  /**
   * Set something on the currently focused checkable
   *
   * @param string $attribute The key to set
   * @param string $value     Its value
   */
  protected function setOnFocused($attribute, $value)
  {
    if (is_null($this->focus)) return false;

    $this->items[$this->focus] = Arrays::set($this->items[$this->focus], $attribute, $value);

    return $this;
  }

  /**
   * Check if a checkable is checked
   *
   * @return boolean Checked or not
   */
  protected function isChecked($name = null, $value = null)
  {
    if(!$name) $name = $this->name;

    // If it's a checkbox, see if we marqued that one as checked in the array
    // Or if it's a single radio, simply see if we called check
    if($this->isCheckbox() or
      !$this->isCheckbox() and !$this->items)
        $checked = Arrays::get($this->checked, $name, false);

    // If there are multiple, search for the value
    // as the name are the same between radios
    else $checked = Arrays::get($this->checked, $value, false);

    // Check the values and POST array
    $post   = $this->app['former']->getPost($name);
    $static = $this->app['former']->getValue($name);

    if(!is_null($post) and $post !== $this->app['former']->getOption('unchecked_value')) $isChecked = ($post == $value);
    elseif(!is_null($static)) $isChecked = ($static == $value);
    else $isChecked = $checked;
    return $isChecked ? true : false;
  }

  /**
   * Check if the current element is a checkbox
   *
   * @return boolean Checkbox or radio
   */
  protected function isCheckbox()
  {
    return $this->checkable == 'checkbox';
  }

  /**
   * Check if the checkables are grouped or not
   *
   * @return boolean
   */
  protected function isGrouped()
  {
    return
      $this->grouped == true or
      strpos($this->name, '[]') !== FALSE;
  }
}
