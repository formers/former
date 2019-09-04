<?php
namespace Former\Form\Fields;

use Former\Helpers;
use Former\Traits\Field;
use HtmlObject\Element;
use HtmlObject\Input;
use Illuminate\Container\Container;
use Illuminate\Support\Str;

/**
 * Everything list-related (select, multiselect, ...)
 */
class Choice extends Field
{
	/**
	 * Renders the checkables as inline
	 *
	 * @var boolean
	 */
	protected $inline = false;

	/**
	 * The choice's placeholder
	 *
	 * @var string
	 */
	private $placeholder = null;

	/**
	 * The choice's options
	 *
	 * @var array
	 */
	protected $options = [
        'isMultiple' => false,
        'isExpanded' => false,
    ];

	/**
	 * The choice's choices
	 *
	 * @var array
	 */
	protected $choices = [];

	/**
	 * The choice's element
	 *
	 * @var string
	 */
	protected $element = 'choice';

	/**
	 * The choice's self-closing state
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
	 * @param array     $choices    The choice's choices
	 * @param string    $selected   The selected choice(s)
	 * @param array     $attributes Attributes
	 */
	public function __construct(Container $app, $type, $name, $label, $choices, $selected, $attributes)
	{
		if ($selected) {
			$this->value = $selected;
		}
		if ($choices) {
			$this->choices($choices);
		}

		parent::__construct($app, $type, $name, $label, $selected, $attributes);

		$this->setChoiceType();

		// Nested models population
		if (Str::contains($this->name, '.') and is_array($this->value) and !empty($this->value) and is_string($this->value[key($this->value)])) {
			$this->fromQuery($this->value);
			$this->value = $selected ?: null;
		}
	}

	/**
	 * Renders the choice
	 *
	 * @return string A <select> tag
	 */
	public function render()
	{
		$choiceType = $this->getType();
		$this->setFieldClasses();

		if (!isset($this->attributes['id'])) {
			$this->setAttribute('id', $this->name);
		}

		switch ($choiceType) {
			case 'select':
				$field = $this->getSelect();
				break;
			case 'checkbox':
			case 'radio':
				$field = $this->getCheckables($choiceType);
				break;
		}
		$this->value = null;
		$content = $field->render();
		return $content;
	}

	public function getSelect()
	{
		$field = Element::create('select', null, $this->attributes);

		$name = $this->name;
		if ($this->options['isMultiple']) {
			$field->multiple();
			$name .= '[]';
		}

		$field->setAttribute('name', $name);

		$field->nest($this->getOptions());

		return $field;
	}

	public function getOptions()
	{
		$children = [];

		// Add placeholder text if any
		if ($placeholder = $this->getPlaceholder()) {
			$children[] = $placeholder;
		}

		foreach ($this->choices as $key => $value) {
			if (is_array($value) and !isset($value['value'])) {
				$children[] = $this->getOptGroup($key, $value);
			} else {
				$children[] = $this->getOption($key, $value);
			}
		}
		return $children;
	}

	public function getOptGroup($label, $options)
	{
		$element = Element::create('optgroup')->label($label);
		$children = [];
		foreach ($options as $key => $value) {
			$option = $this->getOption($key, $value);
			$children[] = $option;
		}
		$element->nest($children);

		return $element;
	}

	public function getOption($key, $value)
	{
		if (is_array($value) and isset($value['value'])) {
			$attributes = $value;
			$text = $key;
			$key = null;
		} else {
			$attributes = array('value' => $key);
			$text = $value;
		}

		$element = Element::create('option', $text, $attributes);
		if ($this->hasValue($attributes['value'])) {
			$element->selected('selected');
		}

		return $element;
	}

	public function getCheckables($choiceType)
	{
		if (!(is_array($this->value) || $this->value instanceof \ArrayAccess)) {
			$this->value = explode(',', $this->value);
		}

		$disabled = isset($this->attributes['disabled']);
		unset($this->attributes['disabled']);

		$field = Element::create('div', null, $this->attributes);

		$children = [];
		foreach ($this->choices as $key => $value) {
			$attributes = [];

			if (is_array($value) and isset($value['value'])) {
				$attributes = $value;
				$label = $key;
				$inputValue = $value['value'];
			} else {
				$attributes = [];
				$label = $value;
				$inputValue = $key;
			}

			if ($disabled) {
				$attributes['disabled'] = true;
			}

			if(isset($attributes['name'])) {
				$name = $attributes['name'];
				unset($attributes['name']);
			} else {
				$name = $this->name;
			}
			if ($this->options['isMultiple']) {
				$name .= '[]';
			}

			if(!isset($attributes['id'])) {
				$attributes['id'] = $this->id.'_'.count($children);
			}

			// If inline items, add class
			$isInline = $this->inline ? ' '.$this->app['former.framework']->getInlineLabelClass($this) : null;

			// In Bootsrap 3, don't append the the checkable type (radio/checkbox) as a class if
			// rendering inline.
			$class = $this->app['former']->framework() == 'TwitterBootstrap3' ? trim($isInline) : $choiceType.$isInline;

			$element = Input::create($choiceType, $name, $inputValue, $attributes);

			// $element->setAttribute('name', $name);

			if ($this->hasValue($inputValue)) {
				$element->checked('checked');
			}
			// $attributes['value'] = $key;
			if (!$label) {
				$element = (is_object($field)) ? $field->render() : $field;
			} else {
				$rendered = $element->render();
				$labelElement = Element::create('label', $rendered.$label);
				$element = $labelElement->for($attributes['id'])->class($class);
			}

			// If BS3, if checkables are stacked, wrap them in a div with the checkable type
			if (!$isInline && $this->app['former']->framework() == 'TwitterBootstrap3') {
				$wrapper = Element::create('div', $element->render())->class($choiceType);
				if ($disabled) {
					$wrapper->addClass('disabled');
				}
				$element = $wrapper;
			}

			$children[] = $element;
		}
		$field->nest($children);

		return $field;
	}

	/**
	 * Get the choice's placeholder
	 *
	 * @return Element
	 */
	protected function getPlaceholder()
	{
		if (!$this->placeholder) {
			return false;
		}

		$attributes = array('value' => '', 'disabled' => 'disabled');
		if (!(array)$this->value) {
			$attributes['selected'] = 'selected';
		}

		return Element::create('option', $this->placeholder, $attributes);
	}

	/**
	 * Sets the element's type based on options
	 *
	 * @return this
	 */
	protected function setChoiceType()
	{
		if ($this->options['isExpanded'] && !$this->options['isMultiple']) {
			$this->setType('radio');
		} elseif ($this->options['isExpanded'] && $this->options['isMultiple']) {
			$this->setType('checkbox');
		} else {
			$this->setType('select');
		}
		return $this;
	}

	/**
	 * Select a value in the field's children
	 *
	 * @param mixed   $value
	 *
	 * @return bool
	 */
	protected function hasValue($choiceValue)
	{
		foreach ((array)$this->value as $key => $value) {
			if (is_object($value) && method_exists($value, 'getKey')) {
				$value = $value->getKey();
			}
			if ($choiceValue === $value || is_numeric($value) && $choiceValue === (int)$value) {
				return true;
			}
		}
		return false;
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////// FIELD METHODS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Set the choices
	 *
	 * @param  array   $_choices     The choices as an array
	 * @param  mixed   $selected     Facultative selected entry
	 * @param  boolean $valuesAsKeys Whether the array's values should be used as
	 *                               the option's values instead of the array's keys
	 */
	public function addChoice($value, $key = null)
	{
		$this->choices[$key ?? $value] = $value;

		return $this;
	}

	/**
	 * Set the choices
	 *
	 * @param  array   $_choices     The choices as an array
	 * @param  mixed   $selected     Facultative selected entry
	 * @param  boolean $valuesAsKeys Whether the array's values should be used as
	 *                               the option's values instead of the array's keys
	 */
	public function choices($_choices, $valuesAsKeys = false)
	{
		$choices = (array) $_choices;

		// If valuesAsKeys is true, use the values as keys
		if ($valuesAsKeys) {
			foreach ($choices as $value) {
				$this->addChoice($value, $value);
			}
		} else {
			foreach ($choices as $key => $value) {
				$this->addChoice($value, $key);
			}
		}

		return $this;
	}

	/**
	 * Creates a list of choices from a range
	 *
	 * @param  integer $from
	 * @param  integer $to
	 * @param  integer $step
	 */
	public function range($from, $to, $step = 1)
	{
		$range = range($from, $to, $step);
		$this->choices($range, true);

		return $this;
	}

	/**
	 * Use the results from a Fluent/Eloquent query as choices
	 *
	 * @param  array           $results    An array of Eloquent models
	 * @param  string|function $text       The value to use as text
	 * @param  string|array    $attributes The data to use as attributes
	 * @param  string	       $selected   The default selected item
	 */
	public function fromQuery($results, $text = null, $attributes = null, $selected = null)
	{
		$this->choices(Helpers::queryToArray($results, $text, $attributes))->value($selected);

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

	/**
	 * Set isMultiple
	 *
	 * @param boolean $isMultiple
	 * @return $this
	 */
	public function multiple($isMultiple = true)
	{
		$this->options['isMultiple'] = $isMultiple;
		$this->setChoiceType();

		return $this;
	}

	/**
	 * Set isExpanded
	 *
	 * @param boolean $isExpanded
	 * @return $this
	 */
	public function expanded($isExpanded = true)
	{
		$this->options['isExpanded'] = $isExpanded;
		$this->setChoiceType();

		return $this;
	}

	/**
	 * Set the choices as inline (for expanded items)
	 */
	public function inline($isInline = true)
	{
		$this->inline = $isInline;

		return $this;
	}

	/**
	 * Set the choices as stacked (for expanded items)
	 */
	public function stacked($isStacked = true)
	{
		$this->inline = !$isStacked;

		return $this;
	}

	/**
	 * Check if field is a checkbox or a radio
	 *
	 * @return boolean
	 */
	public function isCheckable()
	{
		return $this->options['isExpanded'];
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// HELPERS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Returns the current choices in memory for manipulations
	 *
	 * @return array The current choices array
	 */
	public function getChoices()
	{
		return $this->choices;
	}

}
