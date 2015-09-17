<?php
namespace Former;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Populates the class with values, and fetches them
 * from various places
 */
class Populator extends Collection
{
	/**
	 * Create a new collection.
	 *
	 * @param  array|Model $items
	 *
	 * @return void
	 */
	public function __construct($items = array())
	{
		$this->items = $items;
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////// INDIVIDUAL VALUES ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the value of a field
	 *
	 * @param string $field The field's name
	 *
	 * @return mixed
	 */
	public function get($field, $fallback = null)
	{
		// Anonymous fields should not return any value
		if ($field == null) {
			return null;
		}

		// Plain array
		if (is_array($this->items) and !str_contains($field, '[')) {
			return parent::get($field, $fallback);
		}

		// Transform the name into an array
		$value = $this->items;
		$field = $this->parseFieldAsArray($field);

		// Dive into the model
		foreach ($field as $relationship) {

			// Get attribute from model
			if (!is_array($value)) {
				$value = $this->getAttributeFromModel($value, $relationship, $fallback);

				continue;
			}

			// Get attribute from model
			if (array_key_exists($relationship, $value)) {
				$value = $value[$relationship];
			} else {
				// Check array for submodels that may contain the relationship
				$inSubmodel = false;

				foreach ($value as $key => $submodel) {
					$value[$key] = $this->getAttributeFromModel($submodel, $relationship, $fallback);

					if ($value[$key] !== $fallback) {
						$inSubmodel = true;
					}
				}

				// If no submodels contained the relationship, return the fallback, not an array of fallbacks
				if (!$inSubmodel) {
					$value = $fallback;
					break;
				}
			}
		}

		return $value;
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// SWAPPERS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Replace the items
	 *
	 * @param  mixed $items
	 *
	 * @return void
	 */
	public function replace($items)
	{
		$this->items = $items;
	}

	/**
	 * Reset the current values array
	 *
	 * @return void
	 */
	public function reset()
	{
		$this->items = array();
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// HELPERS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Parses the name of a field to a tree of fields
	 *
	 * @param string $field The field's name
	 *
	 * @return array A tree of field
	 */
	protected function parseFieldAsArray($field)
	{
		if (Str::contains($field, '[]')) {
			return (array) $field;
		}

		// Transform array notation to dot notation
		if (Str::contains($field, '[')) {
			$field = preg_replace("/[\[\]]/", '.', $field);
			$field = str_replace('..', '.', $field);
			$field = trim($field, '.');
		}

		// Parse dot notation
		if (Str::contains($field, '.')) {
			$field = explode('.', $field);
		} else {
			$field = (array) $field;
		}

		return $field;
	}

	/**
	 * Get an attribute from a model
	 *
	 * @param object $model     The model
	 * @param string $attribute The attribute's name
	 * @param string $fallback  Fallback value
	 *
	 * @return mixed
	 */
	public function getAttributeFromModel($model, $attribute, $fallback)
	{
		if ($model instanceof Model) {
			// Return fallback if attribute is null
			$value = $model->getAttribute($attribute);
			return is_null($value) ? $fallback : $value;
		}

		if ($model instanceof Collection) {
			return $model->get($attribute, $fallback);
		}

		if (is_object($model) && method_exists($model, 'toArray')) {
			$model = $model->toArray();
		} else {
			$model = (array) $model;
		}
		if (array_key_exists($attribute, $model)) {
			return $model[$attribute];
		}

		return $fallback;
	}
}
