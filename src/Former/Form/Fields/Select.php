<?php
namespace Former\Form\Fields;

use Former\Helpers;
use Former\Traits\Field;
use HtmlObject\Element;
use Illuminate\Container\Container;

/**
 * Everything list-related (select, multiselect, ...)
 */
class Select extends Field
{

	/**
	 * The select's placeholder
	 *
	 * @var string
	 */
	private $placeholder = null;

	/**
	 * The Select's options
	 *
	 * @var array
	 */
	protected $options;

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
	 * @param Container $app        The Container instance
	 * @param string    $type       select
	 * @param string    $name       Field name
	 * @param string    $label      Its label
	 * @param array     $options    The select's options
	 * @param string    $selected   The selected option
	 * @param array     $attributes Attributes
	 */
	public function __construct(Container $app, $type, $name, $label, $options, $selected, $attributes)
	{
		if ($selected) {
			$this->value = $selected;
		}
		if ($options) {
			$this->options($options);
		}

		parent::__construct($app, $type, $name, $label, $selected, $attributes);

		// Nested models population
		if (str_contains($this->name, '.') and is_array($this->value) and !empty($this->value) and is_string($this->value[key($this->value)])) {
			$this->fromQuery($this->value);
			$this->value = $selected ?: null;
		}
	}

	/**
	 * Renders the select
	 *
	 * @return string A <select> tag
	 */
	public function render()
	{
		// Multiselects
		if ($this->isOfType('multiselect')) {
			if (!isset($this->attributes['id'])) {
				$this->setAttribute('id', $this->name);
			}

			$this->multiple();
			$this->name .= '[]';
		}

		if ( ! $this->value instanceOf \ArrayAccess) {
			$this->value = (array) $this->value;
		}

		// Mark selected values as selected
		if ($this->hasChildren() and !empty($this->value)) {
			foreach ($this->value as $value) {
				if (is_object($value) && method_exists($value, 'getKey')) {
					$value = $value->getKey();
				}
				$this->selectValue($value);
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
	 * Select a value in the field's children
	 *
	 * @param mixed   $value
	 * @param Element $parent
	 *
	 * @return void
	 */
	protected function selectValue($value, $parent = null)
	{
		// If no parent element defined, use direct children
		if (!$parent) {
			$parent = $this;
		}

		foreach ($parent->getChildren() as $child) {
			// Search by value

			if ($child->getAttribute('value') === $value || is_numeric($value) && $child->getAttribute('value') === (int)$value ) {
				$child->selected('selected');
			}

			// Else iterate over subchilds
			if ($child->hasChildren()) {
				$this->selectValue($value, $child);
			}
		}
	}

	/**
	 * Get the Select's placeholder
	 *
	 * @return Element
	 */
	protected function getPlaceholder()
	{
		if (!$this->placeholder) {
			return false;
		}

		$attributes = array('value' => '', 'disabled' => 'disabled');
		if (!$this->value) {
			$attributes['selected'] = 'selected';
		}

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
		$options = array();

		// If valuesAsKeys is true, use the values as keys
		if ($valuesAsKeys) {
			foreach ($_options as $v) {
				$options[$v] = $v;
			}
		} else {
			$options = $_options;
		}

		// Add the various options
		foreach ($options as $value => $text) {
			if (is_array($text) and isset($text['value'])) {
				$attributes = $text;
				$text       = $value;
				$value      = null;
			} else {
				$attributes = array();
			}
			$this->addOption($text, $value, $attributes);
		}

		// Set the selected value
		if (!is_null($selected)) {
			$this->select($selected);
		}

		return $this;
	}

	/**
	 * Creates a list of options from a range
	 *
	 * @param  integer $from
	 * @param  integer $to
	 * @param  integer $step
	 */
	public function range($from, $to, $step = 1)
	{
		$range = range($from, $to, $step);
		$this->options($range, null, true);

		return $this;
	}

	/**
	 * Add an option to the Select's options
	 *
	 * @param array|string $text       It's value or an array of values
	 * @param string       $value      It's text
	 * @param array        $attributes The option's attributes
	 */
	public function addOption($text = null, $value = null, $attributes = array())
	{
		// Get the option's value
		$childrenKey = !is_null($value) ? $value : sizeof($this->children);

		// If we passed an options group
		if (is_array($text)) {
			$this->children[$childrenKey] = Element::create('optgroup')->label($value);
			foreach ($text as $key => $value) {
				$option = Element::create('option', $value)->setAttribute('value', $key);
				$this->children[$childrenKey]->nest($option);
			}
			// Else if it's a simple option
		} else {
			if (!isset($attributes['value'])) {
				$attributes['value'] = $value;
			}

			$this->children[$attributes['value']] = Element::create('option', $text)->setAttributes($attributes);
		}

		return $this;
	}

	/**
	 * Use the results from a Fluent/Eloquent query as options
	 *
	 * @param  array           $results    An array of Eloquent models
	 * @param  string|function $text       The value to use as text
	 * @param  string|array    $attributes The data to use as attributes
	 * @param  string	   $selected   The default selected item
	 */
	public function fromQuery($results, $text = null, $attributes = null, $selected = null)
	{
		$this->options(Helpers::queryToArray($results, $text, $attributes), $selected);

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
