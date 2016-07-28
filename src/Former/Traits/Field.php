<?php
namespace Former\Traits;

use Former\Form\Form;
use Former\Form\Group;
use Former\Former;
use Former\Helpers;
use Former\Interfaces\FieldInterface;
use Former\LiveValidation;
use Illuminate\Container\Container;
use Illuminate\Support\Str;

/**
 * Abstracts general fields parameters (type, value, name) and
 * reforms a correct form field depending on what was asked
 */
abstract class Field extends FormerObject implements FieldInterface
{
	/**
	 * The IoC Container
	 *
	 * @var Container
	 */
	protected $app;

	/**
	 * The Form instance
	 *
	 * @var Former\Form
	 */
	protected $form;

	/**
	 * A label for the field (if not using Bootstrap)
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * The field's group
	 *
	 * @var Group
	 */
	protected $group;

	/**
	 * The field's default element
	 *
	 * @var string
	 */
	protected $element = 'input';

	/**
	 * Whether the Field is self-closing or not
	 *
	 * @var boolean
	 */
	protected $isSelfClosing = true;

	/**
	 * The field's bind destination
	 *
	 * @var string
	 */
	protected $bind;

	/**
	 * Get the current framework instance
	 *
	 * @return Framework
	 */
	protected function currentFramework()
	{
		if ($this->app->bound('former.form.framework')) {
			return $this->app['former.form.framework'];
		}

		return $this->app['former.framework'];
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// INTERFACE ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Set up a Field instance
	 *
	 * @param string $type A field type
	 */
	public function __construct(Container $app, $type, $name, $label, $value, $attributes)
	{
		// Set base parameters
		$this->app   = $app;
		$this->type  = $type;
		$this->value = $value;
		$this->setAttributes($attributes);
		$this->form = $this->app->bound('former.form') ? $this->app['former.form'] : null;

		// Compute and translate label
		$this->automaticLabels($name, $label);

		// Repopulate field
		if ($type != 'password' && $name !== '_token') {
			$this->value = $this->repopulate();
		}

		// Apply Live validation rules
		if ($this->app['former']->getOption('live_validation')) {
			$rules = new LiveValidation($this);
			$rules->apply($this->getRules());
		}

		// Bind the Group class
		$groupClass = $this->isCheckable() ? 'CheckableGroup' : 'Group';
		$groupClass = Former::FORMSPACE.$groupClass;

		$this->group = new $groupClass($this->app, $this->label);
	}

	/**
	 * Redirect calls to the group if necessary
	 *
	 * @param string $method
	 */
	public function __call($method, $parameters)
	{
		// Translate attributes
		$translatable = $this->app['former']->getOption('translatable', array());
		if (in_array($method, $translatable) and isset($parameters[0])) {
			$parameters[0] = Helpers::translate($parameters[0]);
		}

		// Redirect calls to the Control Group
		if (method_exists($this->group, $method) or Str::startsWith($method, 'onGroup')) {
			$method = str_replace('onGroup', '', $method);
			$method = lcfirst($method);

			call_user_func_array(array($this->group, $method), $parameters);

			return $this;
		}

		return parent::__call($method, $parameters);
	}

	/**
	 * Prints out the field, wrapped in its group
	 *
	 * @return string
	 */
	public function wrapAndRender()
	{
		// Dry syntax (hidden fields, plain fields)
		if ($this->isUnwrappable()) {
			$html = $this->render();
			// Control group syntax
		} elseif (Form::hasInstanceOpened()) {
			$html = $this->group->wrapField($this);
			// Classic syntax
		} else {
			$html = $this->currentFramework()->createLabelOf($this);
			$html .= $this->render();
		}

		return $html;
	}

	/**
	 * Prints out the field
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->wrapAndRender();
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////// PUBLIC INTERFACE ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Whether the current field is required or not
	 *
	 * @return boolean
	 */
	public function isRequired()
	{
		return isset($this->attributes['required']);
	}

	/**
	 * Check if a field is unwrappable (no label)
	 *
	 * @return boolean
	 */
	public function isUnwrappable()
	{
		return
			($this->form and $this->currentFramework()->is('Nude')) or
			($this->form and $this->isOfType('inline')) or
			$this->isButton() or
			$this->isOfType('hidden') or
			\Former\Form\Group::$opened or
			$this->group and $this->group->isRaw();
	}

	/**
	 * Check if field is a checkbox or a radio
	 *
	 * @return boolean
	 */
	public function isCheckable()
	{
		return $this->isOfType('checkbox', 'checkboxes', 'radio', 'radios');
	}

	/**
	 * Check if the field is a button
	 *
	 * @return boolean
	 */
	public function isButton()
	{
		return false;
	}

	/**
	 * Get the rules applied to the current field
	 *
	 * @return array An array of rules
	 */
	public function getRules()
	{
		return $this->app['former']->getRules($this->name);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////// SETTERS AND GETTERS ///////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Apply a Live Validation rule by chaining
	 *
	 * @param string $rule The rule
	 */
	public function rule($rule)
	{
		$parameters = func_get_args();
		array_shift($parameters);

		$live = new LiveValidation($this);
		$live->apply(array(
			$rule => $parameters,
		));

		return $this;
	}

    /**
     * Apply multiple rules passed as a string.
     *
     * @param $rules
     * @return $this
     */
    public function rules($rules)
    {
        foreach (explode('|', $rules) as $rule) {
            $parameters = null;

            // If we have a rule with a value
            if (($colon = strpos($rule, ':')) !== false) {
                $parameters = str_getcsv(substr($rule, $colon + 1));
            }

            // Exclude unsupported rules
            $rule = is_numeric($colon) ? substr($rule, 0, $colon) : $rule;

            // Store processed rule in Former's array
            if (!isset($parameters)) {
                $parameters = array();
            }

            call_user_func_array([$this, 'rule'], array_merge([$rule], $parameters));
        }

        return $this;
    }

	/**
	 * Adds a label to the group/field
	 *
	 * @param  string $text       A label
	 * @param  array  $attributes The label's attributes
	 *
	 * @return Field              A field
	 */
	public function label($text, $attributes = array())
	{
		// Create the Label element
		$for   = $this->id ?: $this->name;
		$label = $this->app['former']->label($text, $for, $attributes);

		// Set label
		$this->label = $label;
		if ($this->group) {
			$this->group->setLabel($label);
		}

		return $this;
	}

	/**
	 * Set the Field value no matter what
	 *
	 * @param string $value A new value
	 */
	public function forceValue($value)
	{
		$this->value = $value;

		return $this;
	}

	/**
	 * Classic setting of attribute, won't overwrite any populate() attempt
	 *
	 * @param  string $value A new value
	 */
	public function value($value)
	{
		// Check if we already have a value stored for this field or in POST data
		$already = $this->repopulate();

		if (!$already) {
			$this->value = $value;
		}

		return $this;
	}

	/**
	 * Change the field's name
	 *
	 * @param  string $name The new name
	 */
	public function name($name)
	{
		$this->name = $name;

		// Also relink the label to the new name
		$this->label($name);

		return $this;
	}

	/**
	 * Get the field's labels
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Change the field's bind destination
	 *
	 * @param $destination
	 */
	public function bind($destination) {
		$this->bind = $destination;
		if ($this->type != 'password') {
			$this->value = $this->repopulate();
		}

		return $this;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// HELPERS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Use values stored in Former to populate the current field
	 */
	private function repopulate($fallback = null)
	{
		// Get values from POST, populated, and manually set value
		$post      = $this->app['former']->getPost($this->name);
		$populator = $this->form ? $this->form->getPopulator() : $this->app['former.populator'];
		$populate  = $populator->get($this->bind ?: $this->name);

		// Assign a priority to each
		if (!is_null($post)) {
			return $post;
		}
		if (!is_null($populate)) {
			return $populate;
		}

		return $fallback ?: $this->value;
	}

	/**
	 * Ponders a label and a field name, and tries to get the best out of it
	 *
	 * @param  string $label A label
	 * @param  string $name  A field name
	 *
	 * @return false|null         A label and a field name
	 */
	private function automaticLabels($name, $label)
	{
		// Disabled automatic labels
		if (!$this->app['former']->getOption('automatic_label')) {
			$this->name = $name;
			$this->label($label);

			return false;
		}

		// Check for the two possibilities
		if ($label and is_null($name)) {
			$name = Str::slug($label);
		} elseif (is_null($label) and $name) {
			$label = preg_replace('/\[\]$/', '', $name);
		}

		// Save values
		$this->name = $name;
		$this->label($label);
	}
}
