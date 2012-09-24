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
        $name = $fallbackName;
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

    return
      '<label for="' .$name. '" class="' .$this->checkable.$isInline. '">' .
      call_user_func('\Form::'.$this->checkable, $name, $value, $this->isChecked($name), $attributes).
      $label.'</label>';
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
  protected function isChecked($name = null)
  {
    if(!$name) $name = $this->name;
    $value = Former::getPost($name);

    return $value ? true : false;
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
