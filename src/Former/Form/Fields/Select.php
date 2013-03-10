<?php
namespace Former\Form\Fields;

use Former\Helpers;
use Former\Traits\Field;
use HtmlObject\Element;

/**
 * Everything list-related (select, multiselect, ...)
 */
class Select extends Field
{
  /**
   * The select's placeholder
   * @var string
   */
  private $placeholder = null;

  /**
   * The select's element
   *
   * @var string
   */
  protected $element = 'select';

  /**
   * The select's self-closing state
   *
   * @var boolean
   */
  protected $isSelfClosing = false;

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Easier arguments order for selects
   *
   * @param Container $app        The Illuminate Container
   * @param string    $type       file
   * @param string    $name       Field name
   * @param string    $label      Its label
   * @param array     $options    The select's options
   * @param string    $selected   The selected option
   * @param array     $attributes Attributes
   */
  public function __construct(\Former\Former $former, $type, $name, $label, $options, $selected, $attributes)
  {
    if($selected) $this->value = $selected;
    if($options)  $this->options($options);

    parent::__construct($former, $type, $name, $label, $selected, $attributes);

    // Multiple models population
    if (is_array($this->value)) {
      $this->fromQuery($this->value);
      $this->value = $selected ?: null;
    }
  }

  public function render()
  {
    // Multiselects
    if ($this->isOfType('multiselect')) {
      if (!isset($this->attributes['id'])) {
        $this->setAttribute('id', $this->name);
      }

      $this->multiple();
      $this->name .= '[]';

      if ($this->hasChildren() and is_array($this->value)) {
        foreach($this->value as $value) {
          $this->getChild($value)->selected('selected');
        }
      }
    }
    else {
       if($this->hasChildren() and $this->value) {
          $this->getChild($this->value)->selected('selected');
       }
    }

    // Add placeholder text if any
    if ($placeholder = $this->getPlaceholder()) {
      array_unshift($this->children, $placeholder);
    }

    $this->value = null;

    return parent::render();
  }

  /**
   * Get the Select's placeholder
   *
   * @return Element
   */
  protected function getPlaceholder()
  {
    if (!$this->placeholder) return false;

    $attributes = array('value' => '', 'disabled' => '');
    if(!$this->value) $attributes['selected'] = '';

    return Element::create('option', $this->placeholder, $attributes);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// FIELD METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set the select options
   *
   * @param  array   $_options     The options as an array
   * @param  mixed   $selected     Facultative selected entry
   * @param  boolean $valuesAsKeys Whether the array's values should be used as
   *                               the option's values instead of the array's keys
   */
  public function options($_options, $selected = null, $valuesAsKeys = false)
  {
    // Automatically fetch Lang objects for people who store translated options lists
    if ($_options instanceof \Laravel\Lang) {
      $_options = $_options->get();
    }

    // If valuesAsKeys is true, use the values as keys
    if ($valuesAsKeys) {
      foreach($_options as $v) $options[$v] = $v;
    } else $options = $_options;

    foreach ($options as $key => $option) {
      $options[$key] = Element::create('option', $option)->setAttribute('value', $key);
    }

    $this->children = $options;

    if($selected) $this->value = $selected;

    return $this;
  }

  /**
   * Use the results from a Fluent/Eloquent query as options
   *
   * @param  array  $results  An array of Eloquent models
   * @param  string $value    The attribute to use as text
   * @param  string $key      The attribute to use as value
   */
  public function fromQuery($results, $value = null, $key = null)
  {
    $this->options(Helpers::queryToArray($results, $value, $key));

    return $this;
  }

  /**
   * Select a particular list item
   *
   * @param  mixed $selected Selected item
   */
  public function select($selected)
  {
    $this->value = $selected;

    return $this;
  }

  /**
   * Add a placeholder to the current select
   *
   * @param  string $placeholder The placeholder text
   */
  public function placeholder($placeholder)
  {
    $this->placeholder = Helpers::translate($placeholder);

    return $this;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Returns the current options in memory for manipulations
   *
   * @return array The current options array
   */
  public function getOptions()
  {
    return $this->children;
  }
}
