<?php
namespace Former\Traits;

use Former\Helpers;
use HtmlObject\Element;
use HtmlObject\Input;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;

/**
 * Abstract methods inherited by Checkbox and Radio
 */
abstract class Checkable extends Field
{
	/**
	 * Renders the checkables as inline
	 *
	 * @var boolean
	 */
	protected $inline = false;

	/**
	 * Add a text to a single element
	 *
	 * @var string
	 */
	protected $text = null;

	/**
	 * Renders the checkables as grouped
	 *
	 * @var boolean
	 */
	protected $grouped = false;

	/**
	 * The checkable items currently stored
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * The type of checkable item
	 *
	 * @var string
	 */
	protected $checkable = null;

	/**
	 * An array of checked items
	 *
	 * @var array
	 */
	protected $checked = array();

	/**
	 * The checkable currently being focused on
	 *
	 * @var integer
	 */
	protected $focus = null;

	/**
	 * Whether this particular checkable is to be pushed
	 *
	 * @var boolean
	 */
	protected $isPushed = null;

	////////////////////////////////////////////////////////////////////
	//////////////////////////// CORE METHODS //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Build a new checkable
	 *
	 * @param Container $app
	 * @param string    $type
	 * @param array     $name
	 * @param           $label
	 * @param           $value
	 * @param           $attributes
	 */
	public function __construct(Container $app, $type, $name, $label, $value, $attributes)
	{
		// Unify auto and chained methods of grouping checkboxes
		if (ends_with($name, '[]')) {
			$name = substr($name, 0, -2);
			$this->grouped();
		}
		parent::__construct($app, $type, $name, $label, $value, $attributes);

		if (is_array($this->value)) {
			$this->items($this->value);
		}
	}

	/**
	 * Apply methods to focused checkable
	 *
	 * @param string $method
	 * @param array  $parameters
	 *
	 * @return $this
	 */
	public function __call($method, $parameters)
	{
		$focused = $this->setOnFocused('attributes.'.$method, array_get($parameters, 0));
		if ($focused) {
			return $this;
		}

		return parent::__call($method, $parameters);
	}

	/**
	 * Prints out the currently stored checkables
	 */
	public function render()
	{
		$html = null;

		// Multiple items
		if ($this->items) {
			unset($this->app['former']->labels[array_search($this->name, $this->app['former']->labels)]);
			foreach ($this->items as $key => $item) {
				$value = $this->isCheckbox() && !$this->isGrouped() ? 1 : $key;
				$html .= $this->createCheckable($item, $value);
			}

			return $html;
		}

		// Single item
		return $this->createCheckable(array(
			'name'  => $this->name,
			'label' => $this->text,
			'value' => $this->value,
			'attributes' => $this->attributes,
		));
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////// FIELD METHODS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Focus on a particular checkable
	 *
	 * @param integer $on The checkable to focus on
	 *
	 * @return $this
	 */
	public function on($on)
	{
		if (!isset($this->items[$on])) {
			return $this;
		} else {
			$this->focus = $on;
		}

		return $this;
	}

	/**
	 * Set the checkables as inline
	 */
	public function inline()
	{
		$this->inline = true;

		return $this;
	}

	/**
	 * Set the checkables as stacked
	 */
	public function stacked()
	{
		$this->inline = false;

		return $this;
	}

	/**
	 * Set the checkables as grouped
	 */
	public function grouped()
	{
		$this->grouped = true;

		return $this;
	}

	/**
	 * Add text to a single checkable
	 *
	 * @param  string $text The checkable label
	 *
	 * @return $this
	 */
	public function text($text)
	{
		// Translate and format
		$text = Helpers::translate($text);

		// Apply on focused if any
		$focused = $this->setOnFocused('label', $text);
		if ($focused) {
			return $this;
		}

		$this->text = $text;

		return $this;
	}

	/**
	 * Push this particular checkbox
	 *
	 * @param boolean $pushed
	 *
	 * @return $this
	 */
	public function push($pushed = true)
	{
		$this->isPushed = $pushed;

		return $this;
	}

	/**
	 * Check a specific item
	 *
	 * @param bool|string $checked The checkable to check, or an array of checked items
	 *
	 * @return $this
	 */
	public function check($checked = true)
	{
		// If we're setting all the checked items at once
		if (is_array($checked)) {
			$this->checked = $checked;
			// Checking an item in particular
		} elseif (is_string($checked) or is_int($checked)) {
			$this->checked[$checked] = true;
			// Only setting a single item
		} else {
			$this->checked[$this->name] = (bool) $checked;
		}

		return $this;
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////// INTERNAL METHODS ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Creates a series of checkable items
	 *
	 * @param array $_items Items to create
	 */
	protected function items($_items)
	{
		// If passing an array
		if (sizeof($_items) == 1 and
			isset($_items[0]) and
			is_array($_items[0])
		) {
			$_items = $_items[0];
		}

		// Fetch models if that's what we were passed
		if (isset($_items[0]) and is_object($_items[0])) {
			$_items = Helpers::queryToArray($_items);
			$_items = array_flip($_items);
		}

		// Iterate through items, assign a name and a label to each
		$count = 0;
		foreach ($_items as $label => $name) {

			// Define a fallback name in case none is found
			$fallback = $this->isCheckbox()
				? $this->name.'_'.$count
				: $this->name;

			// Grouped fields
			if ($this->isGrouped()) {
				$attributes['id'] = str_replace('[]', null, $fallback);
				$fallback         = str_replace('[]', null, $this->name).'[]';
			}

			// If we haven't any name defined for the checkable, try to compute some
			if (!is_string($label) and !is_array($name)) {
				$label = $name;
				$name  = $fallback;
			}

			// If we gave custom information on the item, add them
			if (is_array($name)) {
				$attributes = $name;
				$name       = array_get($attributes, 'name', $fallback);
				unset($attributes['name']);
			}

			// Store all informations we have in an array
			$item = array(
				'name'  => $name,
				'label' => Helpers::translate($label),
				'count' => $count,
			);
			if (isset($attributes)) {
				$item['attributes'] = $attributes;
			}

			$this->items[] = $item;
			$count++;
		}
	}

	/**
	 * Renders a checkable
	 *
	 * @param string|array $item          A checkable item
	 * @param integer      $fallbackValue A fallback value if none is set
	 *
	 * @return string
	 */
	protected function createCheckable($item, $fallbackValue = 1)
	{
		// Extract informations
		extract($item);

		// Set default values
		if (!isset($attributes)) {
			$attributes = array();
		}
		if (isset($attributes['value'])) {
			$value = $attributes['value'];
		}
		if (!isset($value) or $value === $this->app['former']->getOption('unchecked_value')) {
			$value = $fallbackValue;
		}

		// If inline items, add class
		$isInline = $this->inline ? ' '.$this->app['former.framework']->getInlineLabelClass($this) : null;

		// In Bootsrap 3, don't append the the checkable type (radio/checkbox) as a class if
		// rendering inline.
		$class = $this->app['former']->framework() == 'TwitterBootstrap3' ? trim($isInline) : $this->checkable.$isInline;

		// Merge custom attributes with global attributes
		$attributes = array_merge($this->attributes, $attributes);
		if (!isset($attributes['id'])) {
			$attributes['id'] = $name.$this->unique($name);
		}

		// Create field
		$field = Input::create($this->checkable, $name, $value, $attributes);
		if ($this->isChecked($item, $value)) {
			$field->checked('checked');
		}

		// Add hidden checkbox if requested
		if ($this->isOfType('checkbox', 'checkboxes')) {
			if ($this->isPushed or ($this->app['former']->getOption('push_checkboxes') and $this->isPushed !== false)) {
				$field = $this->app['former']->hidden($name)->forceValue($this->app['former']->getOption('unchecked_value')).$field->render();

				// app['former.field'] was overwritten by Former::hidden() call in the line above, so here
				// we reset it to $this to enable $this->app['former']->getErrors() to retrieve the correct object
				$this->app->instance('former.field', $this);
			}
		}

		// If no label to wrap, return plain checkable
		if (!$label) {
			$element = (is_object($field)) ? $field->render() : $field;
		} else {
			$element = Element::create('label', $field.$label)->for($attributes['id'])->class($class)->render();
		}

		// If BS3, if checkables are stacked, wrap them in a div with the checkable type
		if (!$isInline && $this->app['former']->framework() == 'TwitterBootstrap3') {
			$wrapper = Element::create('div', $element)->class($this->checkable);
			if ($this->getAttribute('disabled')) {
				$wrapper->addClass('disabled');
			}
			$element = $wrapper->render();
		}

		// Return the field
		return $element;
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// HELPERS //////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Generate an unique ID for a field
	 *
	 * @param string $name The field's name
	 *
	 * @return string A field number to use
	 */
	protected function unique($name)
	{
		$this->app['former']->labels[] = $name;

		// Count number of fields with the same ID
		$where  = array_filter($this->app['former']->labels, function ($label) use ($name) {
			return $label == $name;
		});
		$unique = sizeof($where);

		// In case the field doesn't need to be numbered
		if ($unique < 2 or empty($this->items)) {
			return false;
		}

		return $unique;
	}

	/**
	 * Set something on the currently focused checkable
	 *
	 * @param string $attribute The key to set
	 * @param string $value     Its value
	 *
	 * @return $this|bool
	 */
	protected function setOnFocused($attribute, $value)
	{
		if (is_null($this->focus)) {
			return false;
		}

		$this->items[$this->focus] = array_set($this->items[$this->focus], $attribute, $value);

		return $this;
	}

	/**
	 * Check if a checkable is checked
	 *
	 * @return boolean Checked or not
	 */
	protected function isChecked($item = null, $value = null)
	{
		if (isset($item['name'])) {
			$name = $item['name'];
		}
		if (empty($name)) {
			$name = $this->name;
		}

		// If it's a checkbox, see if we marqued that one as checked in the array
		// Or if it's a single radio, simply see if we called check
		if ($this->isCheckbox() or
			!$this->isCheckbox() and !$this->items
		) {
			$checked = array_get($this->checked, $name, false);

			// If there are multiple, search for the value
			// as the name are the same between radios
		} else {
			$checked = array_get($this->checked, $value, false);
		}

		// Check the values and POST array
		if ($this->isGrouped()) {
			// The group index. (e.g. 'bar' if the item name is foo[bar], or the item index for foo[])
			$groupIndex = self::getGroupIndexFromItem($item);

			// Search using the bare name, not the individual item name
			$post   = $this->app['former']->getPost($this->name);
			$static = $this->app['former']->getValue($this->bind ?: $this->name);

			if (isset($post[$groupIndex])) {
				$post = $post[$groupIndex];
			}

			/**
			 * Support for Laravel Collection repopulating for grouped checkboxes. Note that the groupIndex must
			 * match the value in order for the checkbox to be considered checked, e.g.:
			 *
			 *  array(
			 *    'name' = 'roles[foo]',
			 *    'value' => 'foo',
			 *  )
			 */
			if ($static instanceof Collection) {
				// If the repopulate value is a collection, search for an item matching the $groupIndex
				foreach ($static as $staticItem) {
					$staticItemValue = method_exists($staticItem, 'getKey') ? $staticItem->getKey() : $staticItem;
					if ($staticItemValue == $groupIndex) {
						$static = $staticItemValue;
						break;
					}
				}
			} else if (isset($static[$groupIndex])) {
				$static = $static[$groupIndex];
			}
		} else {
			$post   = $this->app['former']->getPost($name);
			$static = $this->app['former']->getValue($this->bind ?: $name);
		}

		if (!is_null($post) and $post !== $this->app['former']->getOption('unchecked_value')) {
			$isChecked = ($post == $value);
		} elseif (!is_null($static)) {
			$isChecked = ($static == $value);
		} else {
			$isChecked = $checked;
		}

		return $isChecked ? true : false;
	}

	/**
	 * Check if the current element is a checkbox
	 *
	 * @return boolean Checkbox or radio
	 */
	protected function isCheckbox()
	{
		return $this->checkable == 'checkbox';
	}

	/**
	 * Check if the checkables are grouped or not
	 *
	 * @return boolean
	 */
	protected function isGrouped()
	{
		return
			$this->grouped == true or
			strpos($this->name, '[]') !== false;
	}

	/**
	 * @param array $item The item array, containing at least name and count keys.
	 *
	 * @return mixed The group index. (e.g. returns bar if the item name is foo[bar], or the item count for foo[])
	 */
	public static function getGroupIndexFromItem($item)
	{
		$groupIndex = preg_replace('/^.*?\[(.*)\]$/', '$1', $item['name']);
		if (empty($groupIndex) or $groupIndex == $item['name']) {
			return $item['count'];
		}

		return $groupIndex;
	}
}
