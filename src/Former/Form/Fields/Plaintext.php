<?php
namespace Former\Form\Fields;

use Former\Traits\Field;
use Illuminate\Support\HtmlString;

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
		if ($this->app['former']->getOption('escape_plaintext_value', true)) {
			$this->escapeValue();
		}

		return $this->app['former.framework']->createPlainTextField($this);
	}

	protected function escapeValue()
	{
		$valueToEscape = $this->getValue();
		$value = is_string($valueToEscape) || $valueToEscape instanceof HtmlString ? e($valueToEscape) : $valueToEscape;

		return $this->forceValue($value);
	}
}
