<?php
namespace Former\Form\Fields;

use Former\Traits\Field;

/**
 * Textarea fields
 */
class Textarea extends Field
{
	/**
	 * The textarea's element
	 *
	 * @var string
	 */
	protected $element = 'textarea';

	/**
	 * The textarea's self-closing state
	 *
	 * @var boolean
	 */
	protected $isSelfClosing = false;
}
