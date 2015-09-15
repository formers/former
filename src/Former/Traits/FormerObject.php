<?php
namespace Former\Traits;

use Former\Helpers;
use HtmlObject\Element;

/**
 * Base Former object that allows chained attributes setting, adding
 * classes to the existing ones, and provide types helpers
 */
abstract class FormerObject extends Element
{
	/**
	 * The field's name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The field type
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * A list of class properties to be added to attributes
	 *
	 * @var array
	 */
	protected $injectedProperties = array('name');

	////////////////////////////////////////////////////////////////////
	/////////////////////////// ID AND LABELS //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Create an unique ID and return it
	 *
	 * @return string
	 */
	public function getCreatedId()
	{
		$this->setId();

		return $this->attributes['id'];
	}

	/**
	 * Set the matching ID on a field if possible
	 */
	protected function setId()
	{
		if (!array_key_exists('id', $this->attributes) and
			in_array($this->name, $this->app['former']->labels)
		) {
			// Set and save the field's ID
			$id                         = $this->getUniqueId($this->name);
			$this->attributes['id']     = $id;
			$this->app['former']->ids[] = $id;
		}
	}

	/**
	 * Get an unique ID for a field from its name
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	protected function getUniqueId($name)
	{
		$names = &$this->app['former']->names;

		if (array_key_exists($name, $names)) {
			$count = $names[$name] + 1;
			$names[$name] = $count;
			return $name . '-' . $count;
		}

		$names[$name] = 1;
		return $name;
	}

	/**
	 * Render the FormerObject and set its id
	 *
	 * @return string
	 */
	public function render()
	{
		// Set the proper ID according to the label
		$this->setId();

		// Encode HTML value
		$isButton = ($this instanceof Field) ? $this->isButton() : false;
		if (!$isButton and is_string($this->value)) {
			$this->value = Helpers::encode($this->value);
		}

		return parent::render();
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// GETTERS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the object's name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// OBJECT TYPE ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the object's type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Change a object's type
	 *
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Check if an object is of a certain type
	 *
	 * @return boolean
	 */
	public function isOfType()
	{
		$types = func_get_args();

		return in_array($this->type, $types);
	}
}
