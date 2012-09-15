<?php
/**
 * Input
 *
 * Renders all basic input types
 */
namespace Former;

use Form;

class Input extends Field
{
  /**
   * Current datalist stored
   * @var array
   */
  private $datalist = array();

  /**
   * Adds a datalist to the current field
   *
   * @param  array $datalist An array to use a source
   */
  public function useDatalist($datalist)
  {
    $this->list('datalist_'.$this->name);
    $this->datalist = $datalist;
  }

  /**
   * Prints out the current tag
   *
   * @return string An input tag
   */
  public function __toString()
  {
    // Render main input
    $input = Form::input($this->type, $this->name, $this->value, $this->attributes);

    // If we have a datalist to append, print it out
    if($this->datalist) {
      $input .= self::renderDatalist('datalist_'.$this->name, $this->datalist);
    }

    return $input;
  }

  /**
   * Renders a datalist
   *
   * @param  string $name The datalist name
   * @param  array  $list Its values
   * @return string       A <datalist> tag
   */
  private function renderDatalist($name, $list)
  {
    $datalist = '<datalist id="' .$name. '">';
      foreach($list as $key => $value) {
        $datalist .= '<option value="' .$value. '">' .$key. '</option>';
      }
    $datalist .= '</datalist>';

    return $datalist;
  }
}