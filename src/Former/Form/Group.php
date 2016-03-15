<?php
namespace Former\Form;

use BadMethodCallException;
use Former\Helpers;
use HtmlObject\Element;
use HtmlObject\Traits\Tag;
use Illuminate\Container\Container;

/**
 * Helper class to build groups
 */
class Group extends Tag
{
	/**
	 * The Container
	 *
	 * @var Container
	 */
	protected $app;

	/**
	 * The current state of the group
	 *
	 * @var string
	 */
	protected $state = null;

	/**
	 * Whether the field should be displayed raw or not
	 *
	 * @var boolean
	 */
	protected $raw = false;

	/**
	 * The group label
	 *
	 * @var Element
	 */
	protected $label;

	/**
	 * The group help
	 *
	 * @var array
	 */
	protected $help = array();

	/**
	 * An array of elements to preprend the field
	 *
	 * @var array
	 */
	protected $prepend = array();

	/**
	 * An array of elements to append the field
	 *
	 * @var array
	 */
	protected $append = array();

	/**
	 * The field validations to be checked for errors
	 *
	 * @var array
	 */
	protected $validations = array();

	/**
	 * The group's element
	 *
	 * @var string
	 */
	protected $element = 'div';

	/**
	 * Whether a custom group is opened or not
	 *
	 * @var boolean
	 */
	public static $opened = false;

	/**
	 * The custom group that is open
	 *
	 * @var Former\Form\Group
	 */
	public static $openGroup = null;

	////////////////////////////////////////////////////////////////////
	/////////////////////////// CORE METHODS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Creates a group
	 *
	 * @param string $label Its label
	 */
	public function __construct(Container $app, $label, $validations = null)
	{
		// Get special classes
		$this->app = $app;
		$this->addClass($this->app['former.framework']->getGroupClasses());

		// Invisible if Nude
		if ($this->app['former.framework']->is('Nude')) {
			$this->element = '';
		}

		// Set group label
		if ($label) {
			$this->setLabel($label);
		}

		// Set validations used to override groups own conclusions
		$this->validations = (array) $validations;
	}

	/**
	 * Prints out the opening of the Control Group
	 *
	 * @return string A control group opening tag
	 */
	public function __toString()
	{
		return $this->open().$this->getFormattedLabel();
	}

	/**
	 * Opens a group
	 *
	 * @return string Opening tag
	 */
	public function open()
	{
		if ($this->getErrors()) {
			$this->state($this->app['former.framework']->errorState());
		}

		// Retrieve state and append it to classes
		if ($this->state) {
			$this->addClass($this->state);
		}

		// Required state
		if ($this->app->bound('former.field') and $this->app['former.field']->isRequired()) {
			$this->addClass($this->app['former']->getOption('required_class'));
		}

		return parent::open();
	}

	/**
	 * Set the contents of the current group
	 *
	 * @param string $contents The group contents
	 *
	 * @return string A group
	 */
	public function contents($contents)
	{
		return $this->wrap($contents, $this->getFormattedLabel());
	}

	/**
	 * Wrap a Field with the current group
	 *
	 * @param  \Former\Traits\Field $field A Field instance
	 *
	 * @return string        A group
	 */
	public function wrapField($field)
	{
		$label = $this->getLabel($field);
		$field = $this->prependAppend($field);
		$field .= $this->getHelp();

		return $this->wrap($field, $label);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// FIELD METHODS /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Set the state of the group
	 *
	 * @param  string $state A Bootstrap state class
	 */
	public function state($state)
	{
		// Filter state
		$state = $this->app['former.framework']->filterState($state);

		$this->state = $state;
	}

	/**
	 * Set a class on the Group
	 *
	 * @param string $class The class to add
	 */
	public function addGroupClass($class)
	{
		$this->addClass($class);
	}

	/**
	 * Adds a label to the group
	 *
	 * @param  string $label A label
	 */
	public function setLabel($label)
	{
		if (!$label instanceof Element) {
			$label = Helpers::translate($label);
			$label = Element::create('label', $label)->for($label);
		}

		$this->label = $label;
	}

	/**
	 * Get the formatted group label
	 *
	 * @return string|null
	 */
	public function getFormattedLabel()
	{
		if (!$this->label) {
			return false;
		}

		return $this->label->addClass($this->app['former.framework']->getLabelClasses());
	}

	/**
	 * Disables the control group for the current field
	 */
	public function raw()
	{
		$this->raw = true;
	}

	/**
	 * Check if the current group is to be displayed or not
	 *
	 * @return boolean
	 */
	public function isRaw()
	{
		return (bool) $this->raw;
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// HELP BLOCKS //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Alias for inlineHelp
	 *
	 * @param  string $help       The help text
	 * @param  array  $attributes Facultative attributes
	 */
	public function help($help, $attributes = array())
	{
		return $this->inlineHelp($help, $attributes);
	}

	/**
	 * Add an inline help
	 *
	 * @param  string $help       The help text
	 * @param  array  $attributes Facultative attributes
	 */
	public function inlineHelp($help, $attributes = array())
	{
		// If no help text, do nothing
		if (!$help) {
			return false;
		}

		$this->help['inline'] = $this->app['former.framework']->createHelp($help, $attributes);
	}

	/**
	 * Add an block help
	 *
	 * @param  string $help       The help text
	 * @param  array  $attributes Facultative attributes
	 */
	public function blockHelp($help, $attributes = array())
	{
		// Reserved method
		if ($this->app['former.framework']->isnt('TwitterBootstrap') && $this->app['former.framework']->isnt('TwitterBootstrap3')) {
			throw new BadMethodCallException('This method is only available on the Bootstrap framework');
		}

		// If no help text, do nothing
		if (!$help) {
			return false;
		}

		$this->help['block'] = $this->app['former.framework']->createBlockHelp($help, $attributes);
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////// PREPEND/APPEND METHODS ///////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Prepend elements to the field
	 */
	public function prepend()
	{
		$this->placeAround(func_get_args(), 'prepend');
	}

	/**
	 * Append elements to the field
	 */
	public function append()
	{
		$this->placeAround(func_get_args(), 'append');
	}

	/**
	 * Prepends an icon to a field
	 *
	 * @param string $icon       The icon to prepend
	 * @param array  $attributes Its attributes
	 */
	public function prependIcon($icon, $attributes = array(), $iconSettings = array())
	{
		$icon = $this->app['former.framework']->createIcon($icon, $attributes, $iconSettings);

		$this->prepend($icon);
	}

	/**
	 * Append an icon to a field
	 *
	 * @param string $icon       The icon to prepend
	 * @param array  $attributes Its attributes
	 */
	public function appendIcon($icon, $attributes = array(), $iconSettings = array())
	{
		$icon = $this->app['former.framework']->createIcon($icon, $attributes, $iconSettings);

		$this->append($icon);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// HELPERS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the errors for the group
	 *
	 * @return string
	 */
	public function getErrors()
	{
		$errors = '';

		if (!self::$opened) {

			// for non-custom groups, normal error handling applies
			$errors = $this->app['former']->getErrors();
		} elseif (!empty($this->validations)) {

			// error handling only when validations specified for custom groups
			foreach ($this->validations as $validation) {
				$errors .= $this->app['former']->getErrors($validation);
			}
		}

		return $errors;
	}

	/**
	 * Wraps content in a group
	 *
	 * @param string $contents The content
	 * @param string $label    The label to add
	 *
	 * @return string A group
	 */
	public function wrap($contents, $label = null)
	{
		$group = $this->open();
		$group .= $label;
		$group .= $this->app['former.framework']->wrapField($contents);
		$group .= $this->close();

		return $group;
	}

	/**
	 * Prints out the current label
	 *
	 * @param  string $field The field to create a label for
	 *
	 * @return string        A <label> tag
	 */
	protected function getLabel($field = null)
	{
		// Don't create a label if none exist
		if (!$field or !$this->label) {
			return null;
		}

		// Wrap label in framework classes
		$this->label->addClass($this->app['former.framework']->getLabelClasses());
		$this->label = $this->app['former.framework']->createLabelOf($field, $this->label);
		$this->label = $this->app['former.framework']->wrapLabel($this->label);

		return $this->label;
	}

	/**
	 * Prints out the current help
	 *
	 * @return string A .help-block or .help-inline
	 */
	protected function getHelp()
	{
		$inline = array_get($this->help, 'inline');
		$block  = array_get($this->help, 'block');

		// Replace help text with error if any found
		$errors = $this->app['former']->getErrors();
		if ($errors and $this->app['former']->getOption('error_messages')) {
			$inline = $this->app['former.framework']->createHelp($errors);
		}

		return join(null, array($inline, $block));
	}

	/**
	 * Format the field with prepended/appended elements
	 *
	 * @param  Field $field The field to format
	 *
	 * @return string        Field plus supplementary elements
	 */
	protected function prependAppend($field)
	{
		if (!$this->prepend and !$this->append) {
			return $field->render();
		}

		return $this->app['former.framework']->prependAppend($field, $this->prepend, $this->append);
	}

	/**
	 * Place elements around the field
	 *
	 * @param  array  $items An array of items to place
	 * @param  string $place Where they should end up (prepend|append)
	 */
	protected function placeAround($items, $place)
	{
		// Iterate over the items and place them where they should
		foreach ((array) $items as $item) {
			$item             = $this->app['former.framework']->placeAround($item);
			$this->{$place}[] = $item;
		}
	}
}
