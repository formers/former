<?php
namespace Former\Form\Fields;

use Former\Helpers;
use Former\Traits\Field;
use Illuminate\Container\Container;

/**
 * Renders all basic input types
 */
class Input extends Field
{
	/**
	 * Current datalist stored
	 *
	 * @var array
	 */
	protected $datalist = array();

	/**
	 * Properties to be injected as attributes
	 *
	 * @var array
	 */
	protected $injectedProperties = array('type', 'name', 'value');

	////////////////////////////////////////////////////////////////////
	/////////////////////////// CORE METHODS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Build an input field
	 *
	 * @param Container $app        The Container
	 * @param string    $type       The input type
	 * @param string    $name       Field name
	 * @param string    $label      Its label
	 * @param string    $value      Its value
	 * @param array     $attributes Attributes
	 */
	public function __construct(Container $app, $type, $name, $label, $value, $attributes)
	{
		parent::__construct($app, $type, $name, $label, $value, $attributes);

		// Multiple models population
		if (is_array($this->value)) {
			$values = array();
			foreach ($this->value as $value) {
				$values[] = is_object($value) ? $value->__toString() : $value;
			}
			if (isset($values)) {
				$this->value = implode(', ', $values);
			}
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
		if ($this->isOfType('search')) {
			$this->asSearch();
		}

		$this->setId();

		// Render main input
		$input = parent::render();

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
	 * @param  array  $datalist An array to use a source
	 * @param  string $value    The field to use as value
	 * @param  string $key      The field to use as key
	 */
	public function useDatalist($datalist, $value = null, $key = null)
	{
		$datalist = Helpers::queryToArray($datalist, $value, $key);

		$list = $this->list ?: 'datalist_'.$this->name;

		// Create the link to the datalist
		$this->list     = $list;
		$this->datalist = $datalist;

		return $this;
	}

	/**
	 * Add range to the input
	 *
	 * @param  integer $min
	 * @param  integer $max
	 *
	 * @return self
	 */
	public function range($min, $max)
	{
		$this->min($min);
		$this->max($max);

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
		$this->addClass('search-query');

		return $this;
	}

	/**
	 * Renders a datalist
	 *
	 * @param string $id     The datalist's id attribute
	 * @param array  $values Its values
	 *
	 * @return string A <datalist> tag
	 */
	private function createDatalist($id, $values)
	{
		$datalist = '<datalist id="'.$id.'">';
		foreach ($values as $key => $value) {
			$datalist .= '<option value="'.$value.'">'.$key.'</option>';
		}
		$datalist .= '</datalist>';

		return $datalist;
	}
}
