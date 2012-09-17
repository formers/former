<?php
namespace Former;

use \Form;

class Checkable extends Field
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
  //////////////////////////////// HELPERS ///////////////////////////
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
    foreach($_items as $name => $label) {
      if(!is_string($name)) $name = $this->name.'_'.$count;
      $this->items[$name] = Helpers::translate($label);
      $count++;
    }
  }

  /**
   * Check if a checkable is checked
   *
   * @return boolean Checked or not
   */
  protected function isChecked($name = null)
  {
    if(!$name) $name = $this->name;
    $value = \Input::get($name, \Input::old($name));

    return $value ? true : false;
  }

  /**
   * Renders a checkable
   *
   * @param  string $name  Its name
   * @param  string $label Its value
   * @return string        A checkable item
   */
  protected function createCheckable($name, $label)
  {
    // If inline items, add class
    $isInline = $this->inline ? ' inline' : null;

    return
      '<label class="' .$this->checkable.$isInline. '">' .
        call_user_func('\Form::'.$this->checkable, $name, 'true', $this->isChecked($name), $this->attributes).
      $label.'</label>';
  }

  /**
   * Prints out the currently stored checkables
   */
  public function __toString()
  {
    if($this->items) {
      $html  = null;
      foreach($this->items as $name => $label) {
        $html .= $this->createCheckable($name, $label);
      }
      return $html;
    }

    return $this->createCheckable($this->name, $this->text);
  }

}