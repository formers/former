<?php
namespace Former;

use Former\Traits\Field;

/**
 * Handles the transformation of validation rules into actual
 * attributes and patterns for HTML5 live validation
 */
class LiveValidation
{
	/**
	 * The field being worked on
	 *
	 * @var Field
	 */
	public $field;

	/**
	 * Load a Field instance to apply rules to it
	 *
	 * @param Field $field The field
	 */
	public function __construct(Field &$field)
	{
		$this->field = $field;
	}

	/**
	 * Apply live validation rules to a field
	 *
	 * @param array $rules The rules to apply
	 */
	public function apply($rules)
	{
		// If no rules to apply, cancel
		if (!$rules) {
			return false;
		}

		foreach ($rules as $rule => $parameters) {

			// If the rule is unsupported yet, skip it
			if (!method_exists($this, $rule)) {
				continue;
			}

			$this->$rule($parameters);
		}
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// RULES /////////////////////////////
	////////////////////////////////////////////////////////////////////

	// Field types
	////////////////////////////////////////////////////////////////////

	/**
	 * Email field
	 */
	public function email()
	{
		$this->field->setType('email');
	}

	/**
	 * URL field
	 */
	public function url()
	{
		$this->field->setType('url');
	}

	/**
	 * Required field
	 */
	public function required()
	{
		$this->field->required();
	}

	// Patterns
	////////////////////////////////////////////////////////////////////

	/**
	 * Integer field
	 */
	public function integer()
	{
		$this->field->pattern('\d+');
	}

	/**
	 * Numeric field
	 */
	public function numeric()
	{
		if ($this->field->isOfType('number')) {
			$this->field->step('any');
		} else {
			$this->field->pattern('[+-]?\d*\.?\d+');
		}
	}

	/**
	 * Not numeric field
	 */
	public function not_numeric()
	{
		$this->field->pattern('\D+');
	}

	/**
	 * Only alphanumerical
	 */
	public function alpha()
	{
		$this->field->pattern('[a-zA-Z]+');
	}

	/**
	 * Only alphanumerical and numbers
	 */
	public function alpha_num()
	{
		$this->field->pattern('[a-zA-Z0-9]+');
	}

	/**
	 * Alphanumerical, numbers and dashes
	 */
	public function alpha_dash()
	{
		$this->field->pattern('[a-zA-Z0-9_\-]+');
	}

	/**
	 * In []
	 */
	public function in($possible)
	{
		// Create the corresponding regex
		$possible = (sizeof($possible) == 1) ? $possible[0] : '('.join('|', $possible).')';

		$this->field->pattern('^'.$possible.'$');
	}

	/**
	 * Not in []
	 */
	public function not_in($impossible)
	{
		$this->field->pattern('(?:(?!^'.join('$|^', $impossible).'$).)*');
	}

	/**
	 * Matches a pattern
	 */
	public function match($pattern)
	{
		// Remove delimiters from existing regex
		$pattern = substr($pattern[0], 1, -1);

		$this->field->pattern($pattern);
	}

	/**
	 * Alias for match
	 */
	public function regex($pattern)
	{
		return $this->match($pattern);
	}

	// Boundaries
	////////////////////////////////////////////////////////////////////

	/**
	 * Max value
	 */
	public function max($max)
	{
		if ($this->field->isOfType('file')) {
			$this->size($max);
		} else {
			$this->setMax($max[0]);
		}
	}

	/**
	 * Max size
	 */
	public function size($size)
	{
		$this->field->max($size[0]);
	}

	/**
	 * Min value
	 */
	public function min($min)
	{
		$this->setMin($min[0]);
	}

	/**
	 * Set boundaries
	 */
	public function between($between)
	{
		list($min, $max) = $between;

		$this->setBetween($min, $max);
	}

	/**
	 * Set accepted mime types
	 *
	 * @param string[] $mimes
	 */
	public function mimes($mimes)
	{
		// Only useful on file fields
		if (!$this->field->isOfType('file')) {
			return false;
		}

		$this->field->accept($this->setAccepted($mimes));
	}

	/**
	 * Set accept only images
	 */
	public function image()
	{
		$this->mimes(array('jpg', 'png', 'gif', 'bmp'));
	}

	// Dates
	////////////////////////////////////////////////////////////////////

	/**
	 * Before a date
	 */
	public function before($date)
	{
		list($format, $date) = $this->formatDate($date[0]);

		$this->field->max(date($format, $date));
	}

	/**
	 * After a date
	 */
	public function after($date)
	{
		list($format, $date) = $this->formatDate($date[0]);

		$this->field->min(date($format, $date));
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// HELPERS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Transform extensions and mime groups into a list of mime types
	 *
	 * @param  array $mimes An array of mimes
	 *
	 * @return string A concatenated list of mimes
	 */
	private function setAccepted($mimes)
	{
		// Transform extensions or mime groups into mime types
		$mimes = array_map(array('\Laravel\File', 'mime'), $mimes);

		return implode(',', $mimes);
	}

	/**
	 * Format a date to a pattern
	 *
	 * @param  string $date The date
	 *
	 * @return string The pattern
	 */
	private function formatDate($date)
	{
		$format = 'Y-m-d';

		// Add hour for datetime fields
		if ($this->field->isOfType('datetime', 'datetime-local')) {
			$format .= '\TH:i:s';
		}

		return array($format, strtotime($date));
	}

	/**
	 * Set a maximum value to a field
	 *
	 * @param integer $max
	 */
	private function setMax($max)
	{
		$attribute = $this->field->isOfType('number') ? 'max' : 'maxlength';

		$this->field->$attribute($max);
	}

	/**
	 * Set a minimum value to a field
	 *
	 * @param integer $min
	 */
	private function setMin($min)
	{
		if ($this->field->isOfType('number') == 'min') {
			$this->field->min($min);
		} else {
			$this->field->pattern(".{".$min.",}");
		}
	}

	/**
	 * Set a minimum and maximum value to a field
	 *
	 * @param $min
	 * @param $max
	 */
	public function setBetween($min, $max)
	{
		if ($this->field->isOfType('number') == 'min') {
			// min, max values for generation of the pattern
			$this->field->min($min);
			$this->field->max($max);
		} else {
			$this->field->pattern('.{'.$min.','.$max.'}');

			// still let the browser limit text input after reaching the max
			$this->field->maxlength($max);
		}
	}
}
