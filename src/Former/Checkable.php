<?php
/**
 * Checkable
 *
 * Abstract methods inherited by Checkbox and Radio
 */
namespace Former;

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

  ////////////////////////////////////////////////////////////////////
  ///////////////////////// PUBLIC INTERFACE /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set the checkables as inline
   */
  public function inline()
  {
    $this->inline = true;
  }

  /**
   * Set the checkables as stacked
   */
  public function stacked()
  {
    $this->inline = false;
  }

  /**
   * Set the checkables as grouped
   */
  public function grouped()
  {
    $this->grouped = true;
  }

  /**
   * Add text to a single checkable
   *
   * @param  string $text The checkable label
   */
  public function text($text)
  {
    // In case people try to pass Lang objects
    if(is_object($text)) $text = $text->get();

    $this->text = Helpers::translate($text);
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
        return $this->checked = $checked;
    }

    // Checking an item in particular
    if (is_string($checked) or is_int($checked)) {
      return $this->checked[$checked] = true;
    }

    // Only setting a single item
    $this->checked[$this->name] = (bool) $checked;

    return (bool) $checked;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// CORE FUNCTIONS //////////////////////////
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
        $name = array_get($attributes, 'name', $fallback);
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
    if(!isset($value) or $value === Config::get('unchecked_value')) $value = $fallbackValue;

    // If inline items, add class
    $isInline = $this->inline ? ' inline' : null;

    // Merge custom attributes with global attributes
    $attributes = array_merge($this->attributes, $attributes);
    if (!isset($attributes['id'])) $attributes['id'] = $name.$this->unique($name);

    // Create field
    $field = call_user_func('\Form::'.$this->checkable, $name, $value, $this->isChecked($name, $value), $attributes);

    // Add hidden checkbox if requested
    if (Config::get('push_checkboxes')) {
      $field = \Form::hidden($name, Config::get('unchecked_value')) . $field;
    }

    // If no label to wrap, return plain checkable
    if(!$label) return $field;

    return '<label for="' .$attributes['id']. '" class="' .$this->checkable.$isInline. '">' .$field.$label. '</label>';
  }

  /**
   * Generate an unique ID for a field
   *
   * @param string $name The field's name
   * @return string A field number to use
   */
  protected function unique($name)
  {
    // Register the field with Laravel
    \Form::$labels[] = $name;

    // Count number of fields with the same ID
    $where = array_filter(\Form::$labels, function($label) use ($name) {
      return $label == $name;
    });
    $unique = sizeof($where);

    // In case the field doesn't need to be numbered
    if ($unique < 2 or empty($this->items)) return false;

    return $unique;
  }

  /**
   * Prints out the currently stored checkables
   */
  public function __toString()
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
  ///////////////////////////// HELPERS //////////////////////////////
  ////////////////////////////////////////////////////////////////////

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
        $checked = array_get($this->checked, $name, false);

    // If there are multiple, search for the value
    // as the name are the same between radios
    else $checked = array_get($this->checked, $value, false);

    // Check the values and POST array
    $post   = Former::getPost($name);
    $static = Former::getValue($name);

    if(!is_null($post) and $post !== Config::get('unchecked_value')) $isChecked = ($post == $value);
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

  protected function isGrouped()
  {
    return $this->grouped == true or strpos($this->name, '[]') !== FALSE;
  }

}
