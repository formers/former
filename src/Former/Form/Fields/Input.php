<?php
/**
 * Input
 *
 * Renders all basic input types
 */
namespace Former\Form\Fields;

use \Former\Helpers;
use \Former\Traits\Field;

class Input extends Field
{
  /**
   * Current datalist stored
   * @var array
   */
  private $datalist = array();

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Build an input field
   */
  public function __construct($app, $type, $name, $label, $value, $attributes)
  {
    parent::__construct($app, $type, $name, $label, $value, $attributes);

    // Multiple models population
    if (is_array($this->value)) {
      foreach($this->value as $value) $values[] = is_object($value) ? $value->__toString() : $value;
      if (isset($values)) $this->value = implode(', ', $values);
    }
  }

  /**
   * Prints out the current tag
   *
   * @return string An input tag
   */
  public function render()
  {
    // Particular case of the search element
    if($this->isOfType('search')) $this->asSearch();

    // Render main input
    $input = $this->app['form']->input($this->type, $this->name, $this->value, $this->attributes);

    // If we have a datalist to append, print it out
    if ($this->datalist) {
      $input .= $this->createDatalist($this->list, $this->datalist);
    }

    return $input;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// FIELD METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

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

    return $this;
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

    return $this;
  }

  /**
   * Renders a datalist
   *
   * @return string A <datalist> tag
   */
  private function createDatalist($id, $values)
  {
    $datalist = '<datalist id="' .$id. '">';
      foreach ($values as $key => $value) {
        $datalist .= '<option value="' .$value. '">' .$key. '</option>';
      }
    $datalist .= '</datalist>';

    return $datalist;
  }
}
