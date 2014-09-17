<?php
namespace Former\Form\Fields;

use Former\Traits\Field;

/**
 * Renders Plain Text Control
 */
class Plaintext extends Field
{
	////////////////////////////////////////////////////////////////////
	/////////////////////////// CORE METHODS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Prints out the current tag
	 *
	 * @return string A plain text tag
	 */
	public function render()
	{
		$this->addClass($this->app['former.framework']->getPlainTextClasses());
		$this->setId();

		return $this->app['former.framework']->createPlainTextField($this);
	}
}
