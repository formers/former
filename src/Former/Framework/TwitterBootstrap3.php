<?php
namespace Former\Framework;

use Former\Interfaces\FrameworkInterface;
use Former\Traits\Field;
use Former\Traits\Framework;
use HtmlObject\Element;
use Illuminate\Container\Container;
use Illuminate\Support\Str;

/**
 * The Twitter Bootstrap form framework
 */
class TwitterBootstrap3 extends Framework implements FrameworkInterface
{
	/**
	 * Form types that trigger special styling for this Framework
	 *
	 * @var array
	 */
	protected $availableTypes = array('horizontal', 'vertical', 'inline');

	/**
	 * The button types available
	 *
	 * @var array
	 */
	private $buttons = array(
		'lg',
		'sm',
		'xs',
		'block',
		'link',
		'default',
		'primary',
		'warning',
		'danger',
		'success',
		'info',
	);

	/**
	 * The field sizes available
	 *
	 * @var array
	 */
	private $fields = array(
		'lg',
		'sm',
		// 'col-xs-1', 'col-xs-2', 'col-xs-3', 'col-xs-4', 'col-xs-5', 'col-xs-6',
		// 'col-xs-7', 'col-xs-8', 'col-xs-9', 'col-xs-10', 'col-xs-11', 'col-xs-12',
		// 'col-sm-1', 'col-sm-2', 'col-sm-3', 'col-sm-4', 'col-sm-5', 'col-sm-6',
		// 'col-sm-7', 'col-sm-8', 'col-sm-9', 'col-sm-10', 'col-sm-11', 'col-sm-12',
		// 'col-md-1', 'col-md-2', 'col-md-3', 'col-md-4', 'col-md-5', 'col-md-6',
		// 'col-md-7', 'col-md-8', 'col-md-9', 'col-md-10', 'col-md-11', 'col-md-12',
		// 'col-lg-1', 'col-lg-2', 'col-lg-3', 'col-lg-4', 'col-lg-5', 'col-lg-6',
		// 'col-lg-7', 'col-lg-8', 'col-lg-9', 'col-lg-10', 'col-lg-11', 'col-lg-12',
	);

	/**
	 * The field states available
	 *
	 * @var array
	 */
	protected $states = array(
		'has-warning',
		'has-error',
		'has-success',
	);

	/**
	 * The default HTML tag used for icons
	 *
	 * @var string
	 */
	protected $iconTag = 'span';

	/**
	 * The default set for icon fonts
	 * By default Bootstrap 3 offers only 'glyphicon'
	 * See Former docs to use 'social' and 'filetypes' sets for specific icons.
	 *
	 * @var string
	 */
	protected $iconSet = 'glyphicon';

	/**
	 * The default prefix icon names
	 * "icon" works for Bootstrap 2 and Font-awesome
	 *
	 * @var string
	 */
	protected $iconPrefix = 'glyphicon';

	/**
	 * Create a new TwitterBootstrap instance
	 *
	 * @param \Illuminate\Container\Container $app
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
		$this->setFrameworkDefaults();
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////// FILTER ARRAYS //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Filter buttons classes
	 *
	 * @param  array $classes An array of classes
	 *
	 * @return string[] A filtered array
	 */
	public function filterButtonClasses($classes)
	{
		// Filter classes
		// $classes = array_intersect($classes, $this->buttons);

		// Prepend button type
		$classes   = $this->prependWith($classes, 'btn-');
		$classes[] = 'btn';

		return $classes;
	}

	/**
	 * Filter field classes
	 *
	 * @param  array $classes An array of classes
	 *
	 * @return array A filtered array
	 */
	public function filterFieldClasses($classes)
	{
		// Filter classes
		$classes = array_intersect($classes, $this->fields);

		// Prepend field type
		$classes = array_map(function ($class) {
			return Str::startsWith($class, 'col') ? $class : 'input-'.$class;
		}, $classes);

		return $classes;
	}

	////////////////////////////////////////////////////////////////////
	///////////////////// EXPOSE FRAMEWORK SPECIFICS ///////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Framework error state
	 *
	 * @return string
	 */
	public function errorState()
	{
		return 'has-error';
	}

	/**
	 * Returns corresponding inline class of a field
	 *
	 * @param Field $field
	 *
	 * @return string
	 */
	public function getInlineLabelClass($field)
	{
		$inlineClass = parent::getInlineLabelClass($field);
		if ($field->isOfType('checkbox', 'checkboxes')) {
			$inlineClass = 'checkbox-'.$inlineClass;
		} elseif ($field->isOfType('radio', 'radios')) {
			$inlineClass = 'radio-'.$inlineClass;
		}

		return $inlineClass;
	}

	/**
	 * Set the fields width from a label width
	 *
	 * @param array $labelWidths
	 */
	protected function setFieldWidths($labelWidths)
	{
		$labelWidthClass = $fieldWidthClass = $fieldOffsetClass = '';

		$viewports = $this->getFrameworkOption('viewports');
		foreach ($labelWidths as $viewport => $columns) {
			if ($viewport) {
				$labelWidthClass .= " col-$viewports[$viewport]-$columns";
				$fieldWidthClass .= " col-$viewports[$viewport]-".(12 - $columns);
				$fieldOffsetClass .= " col-$viewports[$viewport]-offset-$columns";
			}
		}

		$this->labelWidth  = ltrim($labelWidthClass);
		$this->fieldWidth  = ltrim($fieldWidthClass);
		$this->fieldOffset = ltrim($fieldOffsetClass);
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// ADD CLASSES //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Add classes to a field
	 *
	 * @param Field $field
	 * @param array $classes The possible classes to add
	 *
	 * @return Field
	 */
	public function getFieldClasses(Field $field, $classes)
	{
		// Add inline class for checkables
		if ($field->isCheckable() and in_array('inline', $classes)) {
			$field->inline();
		}

		// Filter classes according to field type
		if ($field->isButton()) {
			$classes = $this->filterButtonClasses($classes);
		} else {
			$classes = $this->filterFieldClasses($classes);
		}

		// Add form-control class for text-type, textarea and select fields
		// As text-type is open-ended we instead exclude those that shouldn't receive the class
		if (!$field->isCheckable() and !$field->isButton() and !in_array($field->getType(), array(
					'file',
					'plaintext',
				)) and !in_array('form-control', $classes)
		) {
			$classes[] = 'form-control';
		}

		return $this->addClassesToField($field, $classes);
	}

	/**
	 * Add group classes
	 *
	 * @return string A list of group classes
	 */
	public function getGroupClasses()
	{
		return 'form-group';
	}

	/**
	 * Add label classes
	 *
	 * @return string[] An array of attributes with the label class
	 */
	public function getLabelClasses()
	{
		if ($this->app['former.form']->isOfType('horizontal')) {
			return array('control-label', $this->labelWidth);
		} elseif ($this->app['former.form']->isOfType('inline')) {
			return array('sr-only');
		} else {
			return array('control-label');
		}
	}

	/**
	 * Add uneditable field classes
	 *
	 * @return string An array of attributes with the uneditable class
	 */
	public function getUneditableClasses()
	{
		return '';
	}

	/**
	 * Add plain text field classes
	 *
	 * @return string An array of attributes with the plain text class
	 */
	public function getPlainTextClasses()
	{
		return 'form-control-static';
	}

	/**
	 * Add form class
	 *
	 * @param  string $type The type of form to add
	 *
	 * @return string|null
	 */
	public function getFormClasses($type)
	{
		return $type ? 'form-'.$type : null;
	}

	/**
	 * Add actions block class
	 *
	 * @return string|null
	 */
	public function getActionClasses()
	{
		if ($this->app['former.form']->isOfType('horizontal') || $this->app['former.form']->isOfType('inline')) {
			return 'form-group';
		}

		return null;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// RENDER BLOCKS /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Render an help text
	 *
	 * @param string $text
	 * @param array  $attributes
	 *
	 * @return Element
	 */
	public function createHelp($text, $attributes = array())
	{
		return Element::create('span', $text, $attributes)->addClass('help-block');
	}

	/**
	 * Render an help text
	 *
	 * @param string $text
	 * @param array  $attributes
	 *
	 * @return Element
	 */
	public function createBlockHelp($text, $attributes = array())
	{
		return Element::create('p', $text, $attributes)->addClass('help-block');
	}

	/**
	 * Render a disabled field
	 *
	 * @param Field $field
	 *
	 * @return Element
	 */
	public function createDisabledField(Field $field)
	{
		return Element::create('span', $field->getValue(), $field->getAttributes());
	}

	/**
	 * Render a plain text field
	 *
	 * @param Field $field
	 *
	 * @return Element
	 */
	public function createPlainTextField(Field $field)
	{
		$label = $field->getLabel();
		if ($label) {
			$label->for('');
		}

		return Element::create('div', $field->getValue(), $field->getAttributes());
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// WRAP BLOCKS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Wrap an item to be prepended or appended to the current field
	 *
	 * @return Element A wrapped item
	 */
	public function placeAround($item)
	{
		// Render object
		if (is_object($item) and method_exists($item, '__toString')) {
			$item = $item->__toString();
		}

		// Get class to use
		$class = (strpos($item, '<button') !== false) ? 'btn' : 'addon';

		return Element::create('span', $item)->addClass('input-group-'.$class);
	}

	/**
	 * Wrap a field with prepended and appended items
	 *
	 * @param  Field $field
	 * @param  array $prepend
	 * @param  array $append
	 *
	 * @return string A field concatented with prepended and/or appended items
	 */
	public function prependAppend($field, $prepend, $append)
	{
		$return = '<div class="input-group">';
		$return .= join(null, $prepend);
		$return .= $field->render();
		$return .= join(null, $append);
		$return .= '</div>';

		return $return;
	}

	/**
	 * Wrap a field with potential additional tags
	 *
	 * @param  Field $field
	 *
	 * @return Element A wrapped field
	 */
	public function wrapField($field)
	{
		if ($this->app['former.form']->isOfType('horizontal')) {
			return Element::create('div', $field)->addClass($this->fieldWidth);
		}

		return $field;
	}

	/**
	 * Wrap actions block with potential additional tags
	 *
	 * @param  Actions $actions
	 *
	 * @return string A wrapped actions block
	 */
	public function wrapActions($actions)
	{
		// For horizontal forms, we wrap the actions in a div
		if ($this->app['former.form']->isOfType('horizontal')) {
			return Element::create('div', $actions)->addClass(array($this->fieldOffset, $this->fieldWidth));
		}

		return $actions;
	}
}
