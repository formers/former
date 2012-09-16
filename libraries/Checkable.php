<?php
namespace Former;

class Checkable extends Field
{
  /**
   * Renders the checkables as inline
   * @var boolean
   */
  protected $inline = false;

  /**
   * Add a text to a single checkbox
   * @var string
   */
  protected $text = null;

  /**
   * Set the checkboxes as inline
   */
  public function inline()
  {
    $this->inline = true;
  }

  /**
   * Set the checkboxes as stacked
   */
  public function stacked()
  {
    $this->inline = false;
  }

  /**
   * Adds text to a single checkable
   *
   * @param  string $text The checkable label
   */
  public function text($text)
  {
    $this->text = $text;
  }

  /**
   * Check if a checkbox is checked
   *
   * @return boolean Checked or not
   */
  protected function isChecked($name = null)
  {
    if(!$name) $name = $this->name;
    $value = \Input::get($name, \Input::old($name));

    return $value ? true : false;
  }
}