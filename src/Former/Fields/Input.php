<?php
/**
 * Input
 *
 * Renders all basic input types
 */
namespace Former\Fields;

use \Form;
use \Former\Helpers;

class Input extends \Former\Field
{
  /**
   * Current datalist stored
   * @var array
   */
  private $datalist = array();

  public function __construct($type, $name, $label, $value, $attributes)
  {
    parent::__construct($type, $name, $label, $value, $attributes);

    // Multiple models population
    if (is_array($this->value)) {
      foreach($this->value as $v) $_value[] = is_object($v) ? $v->__toString() : $v;
      $this->value = implode(', ', $_value);
    }
  }

  /**
   * Adds a datalist to the current field
   *
   * @param  array $datalist An array to use a source
   */
  public function useDatalist($datalist, $value = null, $key = null)
  {
    $datalist = Helpers::queryToArray($datalist, $value, $key);

    $list = $this->list ?: 'datalist_'.$this->name;

    // Create the link to the datalist
    $this->list($list);
    $this->datalist = $datalist;
  }

  /**
   * Prints out the current tag
   *
   * @return string An input tag
   */
  public function __toString()
  {
    // Particular case of the search element
    if($this->type == 'search') $this->asSearch();

    // Render main input
    $input = Form::input($this->type, $this->name, $this->value, $this->attributes);

    // If we have a datalist to append, print it out
    if ($this->datalist) {
      $input .= self::renderDatalist();
    }

    return $input;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////////// HELPERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Render a text element as a search element
   */
  private function asSearch()
  {
    $this->type = 'text';
    $this->attributes = Helpers::addClass($this->attributes, 'search-query');
  }

  /**
   * Renders a datalist
   *
   * @return string       A <datalist> tag
   */
  private function renderDatalist()
  {
    $datalist = '<datalist id="' .$this->list. '">';
      foreach ($this->datalist as $key => $value) {
        $datalist .= '<option value="' .$value. '">' .$key. '</option>';
      }
    $datalist .= '</datalist>';

    return $datalist;
  }
}
