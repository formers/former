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
   * @param  string $name The checkable to check
   */
  public function check($name = null)
  {
    if(!$name) $name = $this->name;

    if($this->isCheckbox()) $this->checked[] = $name;
    else $this->checked = array($name);
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// CORE FUNCTIONS ///////////////////////
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
      $fallbackName = $this->isCheckbox() ? $this->name.'_'.$count : $this->name;

      // If we haven't any name defined for the checkable, try to compute some
      if (!is_string($label) and !is_array($name)) {
        $label = $name;
        $name  = $fallbackName;
      }

      // If we gave custom information on the item, add them
      if (is_array($name)) {
        $attributes = $name;
        $name = array_get($attributes, 'name', $fallbackName);
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
    if(!isset($value)) $value = $fallbackValue;

    // If inline items, add class
    $isInline = $this->inline ? ' inline' : null;

    // Merge custom attributes with global attributes
    $attributes = array_merge($this->attributes, $attributes);

    // Register the field with Laravel
    \Form::$labels[] = $name;

    // Create field
    $field = call_user_func('\Form::'.$this->checkable, $name, $value, $this->isChecked($name, $value), $attributes);

    // If no label to wrap, return plain checkable
    if(!$label) return $field;

    return '<label for="' .$name. '" class="' .$this->checkable.$isInline. '">' .$field.$label. '</label>';
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
        $fallbackValue = $this->isCheckbox() ? 1 : $key;
        $html .= $this->createCheckable($item, $fallbackValue);
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
        $checked = in_array($name, $this->checked);

    // If there are multiple, search for the value
    // as the name are the same between radios
    else $checked = in_array($value, $this->checked);

    // Check the values and POST array
    $post   = Former::getPost($name);
    $static = Former::getValue($name);
    $manual = $checked;

    if(!is_null($post)) $isChecked = ($post == $value);
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

}
