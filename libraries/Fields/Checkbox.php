<?php
/**
 * Checkbox
 *
 * My name is Bawksy you see
 */
namespace Former\Fields;

use Form;

class Checkbox extends \Former\Checkable
{
  /**
   * The currently stored checkboxes
   * @var array
   */
  private $checkboxes = array();

  /**
   * Create a serie of checkboxes
   */
  public function checkboxes()
  {
    $_checkboxes = func_get_args();
    if(sizeof($_checkboxes) == 1)
      $_checkboxes = $_checkboxes[0];

    $count = 0;
    foreach($_checkboxes as $name => $label) {
      if(!is_string($name)) $name = $this->name.'_'.$count;
      $checkboxes[$name] = $label;
      $count++;
    }

    $this->checkboxes = $checkboxes;
  }

  /**
   * Prints out the currently stored checkboxes
   */
  public function __toString()
  {
    if($this->checkboxes) {
      $html  = null;
      foreach($this->checkboxes as $name => $label) {
        $html .= $this->createCheckbox($name, $label);
      }
      return $html;
    }

    return $this->createCheckbox($this->name, $this->text);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////


  /**
   * Renders a checkbox
   *
   * @param  string $name  Its name
   * @param  string $label Its value
   * @return string        A checkbox
   */
  private function createCheckbox($name, $label)
  {
    $isInline = $this->inline ? ' inline' : null;

    return '<label class="checkbox' .$isInline. '">'.
      Form::checkbox($name, 'true', $this->isChecked($name), $this->attributes).
      ' '.$label.
    '</label>';
  }
}