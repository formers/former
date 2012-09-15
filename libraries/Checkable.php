<?php
namespace Former;

class Checkable extends Field
{
  protected $inline = false;

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