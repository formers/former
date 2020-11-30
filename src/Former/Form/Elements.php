<?php
namespace Former\Form;

use Former\Helpers;
use HtmlObject\Element;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Htmlable;

/**
 * The different parts of a form that are neither fields nor groups
 * not buttons and stuff
 */
class Elements
{
	/**
	 * The Container
	 *
	 * @var Container
	 */
	protected $app;

	/**
	 * The Session instance
	 *
	 * @var Session
	 */
	protected $session;

	/**
	 * Build a new Element
	 *
	 * @param Container $app
	 */
	public function __construct(Container $app, $session)
	{
		$this->app     = $app;
		$this->session = $session;
	}

	/**
	 * Generate a hidden field containing the current CSRF token.
	 *
	 * @return string
	 */
	public function token()
	{
		$csrf = method_exists($this->session, 'getToken') ? $this->session->getToken() : $this->session->token();

		return (string) $this->app['former']->hidden('_token', $csrf);
	}

	/**
	 * Creates a label tag
	 *
	 * @param  string $label      The label content
	 * @param  string $for        The field the label's for
	 * @param  array  $attributes The label's attributes
	 *
	 * @return Element             A <label> tag
	 */
	public function label($label, $for = null, $attributes = array())
	{
		if (!$label instanceof Htmlable) {
			$oldLabel = (string) $label;
			$label    = Helpers::translate($oldLabel);

			// If there was no change to the label,
			// then a Laravel translation did not occur
			if (lcfirst($label) == $oldLabel) {
				$label = str_replace('_', ' ', $label);
			}
		} else {
			$label = (string) $label->toHtml();
		}

		$attributes['for']             = $for;
		$this->app['former']->labels[] = $for;

		return Element::create('label', $label, $attributes);
	}

	/**
	 * Creates a form legend
	 *
	 * @param  string $legend     The text
	 * @param  array  $attributes Its attributes
	 *
	 * @return Element             A <legend> tag
	 */
	public function legend($legend, $attributes = array())
	{
		$legend = Helpers::translate($legend);

		return Element::create('legend', $legend, $attributes);
	}

	/**
	 * Close a field group
	 *
	 * @return string
	 */
	public function closeGroup()
	{
		$closing = '';
		if (Group::$opened && isset(Group::$openGroup)) {
			$closing = Group::$openGroup->close();
		}

		// Close custom group
		Group::$opened = false;

		// Reset custom group reference
		Group::$openGroup = null;

		return $closing;
	}
}
