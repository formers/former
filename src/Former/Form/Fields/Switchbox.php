<?php
namespace Former\Form\Fields;

class Switchbox extends Checkbox
{
	////////////////////////////////////////////////////////////////////
	////////////////////////// FIELD METHODS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Create a serie of switches
	 */
	public function switches()
	{
		if ($this->isGrouped()) {
			// Remove any possible items added by the Populator.
			$this->items = array();
		}
		$this->items(func_get_args());

		return $this;
	}
}
