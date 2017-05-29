<?php
namespace Former;

use Closure;
use Former\Exceptions\InvalidFrameworkException;
use Former\Traits\Field;
use Illuminate\Container\Container;
use Illuminate\Support\MessageBag;
use Illuminate\Contracts\Validation\Validator;

/**
 * Helps the user interact with the various Former components
 */
class Former
{
	// Instances
	////////////////////////////////////////////////////////////////////


	/**
	 * The current environment
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app;

	/**
	 * The Method Dispatcher
	 *
	 * @var MethodDispatcher
	 */
	protected $dispatch;

	// Informations
	////////////////////////////////////////////////////////////////////

	/**
	 * The form's errors
	 *
	 * @var Message
	 */
	protected $errors;

	/**
	 * An array of rules to use
	 *
	 * @var array
	 */
	protected $rules = array();

	/**
	 * An array of field macros
	 *
	 * @var array
	 */
	protected $macros = array();

	/**
	 * The labels created so far
	 *
	 * @var array
	 */
	public $labels = array();

	/**
	 * The IDs created so far
	 *
	 * @var array
	 */
	public $ids = array();

	/**
	 * A lookup table where the key is the input name,
	 * and the value is number of times seen. This is
	 * used to calculate unique ids.
	 *
	 * @var array
	 */
	public $names = array();

	// Namespaces
	////////////////////////////////////////////////////////////////////

	/**
	 * The namespace of Form elements
	 */
	const FORMSPACE = 'Former\Form\\';

	/**
	 * The namespace of fields
	 */
	const FIELDSPACE = 'Former\Form\Fields\\';

	/**
	 * Build a new Former instance
	 *
	 * @param Container        $app
	 * @param MethodDispatcher $dispatcher
	 */
	public function __construct(Container $app, MethodDispatcher $dispatcher)
	{
		$this->app      = $app;
		$this->dispatch = $dispatcher;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// INTERFACE /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Acts as a router that redirects methods to all of Former classes
	 *
	 * @param  string $method     The method called
	 * @param  array  $parameters An array of parameters
	 *
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		// Dispatch to Form\Elements
		// Explicitly check false since closeGroup() may return an empty string
		if (($element = $this->dispatch->toElements($method, $parameters)) !== false) {
			return $element;
		}

		// Dispatch to Form\Form
		if ($form = $this->dispatch->toForm($method, $parameters)) {
			$this->app->instance('former.form', $form);

			return $this->app['former.form'];
		}

		// Dispatch to Form\Group
		if ($group = $this->dispatch->toGroup($method, $parameters)) {
			return $group;
		}

		// Dispatch to Form\Actions
		if ($actions = $this->dispatch->toActions($method, $parameters)) {
			return $actions;
		}

		// Dispatch to macros
		if ($macro = $this->dispatch->toMacros($method, $parameters)) {
			return $macro;
		}

		// Checking for any supplementary classes
		$classes = explode('_', $method);
		$method  = array_pop($classes);

		// Dispatch to the different Form\Fields
		$framework = isset($this->app['former.form.framework']) ? $this->app['former.form.framework'] : $this->app['former.framework'];
		$field     = $this->dispatch->toFields($method, $parameters);

		if ($field instanceof Field) {
			$field = $framework->getFieldClasses($field, $classes);
		}

		// Else bind field
		$this->app->instance('former.field', $field);

		return $this->app['former.field'];
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// MACROS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Register a macro with Former
	 *
	 * @param  string   $name  The name of the macro
	 * @param  Callable $macro The macro itself
	 *
	 * @return mixed
	 */
	public function macro($name, $macro)
	{
		$this->macros[$name] = $macro;
	}

	/**
	 * Check if a macro exists
	 *
	 * @param  string $name
	 *
	 * @return boolean
	 */
	public function hasMacro($name)
	{
		return isset($this->macros[$name]);
	}

	/**
	 * Get a registered macro
	 *
	 * @param  string $name
	 *
	 * @return Closure
	 */
	public function getMacro($name)
	{
		return $this->macros[$name];
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// POPULATOR ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Add values to populate the array
	 *
	 * @param mixed $values Can be an Eloquent object or an array
	 */
	public function populate($values)
	{
		$this->app['former.populator']->replace($values);
	}

	/**
	 * Set the value of a particular field
	 *
	 * @param string $field The field's name
	 * @param mixed  $value Its new value
	 */
	public function populateField($field, $value)
	{
		$this->app['former.populator']->put($field, $value);
	}

	/**
	 * Get the value of a field
	 *
	 * @param string $field The field's name
	 * @param null   $fallback
	 *
	 * @return mixed
	 */
	public function getValue($field, $fallback = null)
	{
		return $this->app['former.populator']->get($field, $fallback);
	}

	/**
	 * Fetch a field value from both the new and old POST array
	 *
	 * @param  string $name     A field name
	 * @param  string $fallback A fallback if nothing was found
	 *
	 * @return string           The results
	 */
	public function getPost($name, $fallback = null)
	{
		$name     = str_replace(array('[', ']'), array('.', ''), $name);
		$name     = trim($name, '.');
		$oldValue = $this->app['request']->old($name, $fallback);

		return $this->app['request']->input($name, $oldValue, true);
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// TOOLKIT /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Set the errors to use for validations
	 *
	 * @param Message $validator The result from a validation
	 *
	 * @return  void
	 */
	public function withErrors($validator = null)
	{
		// Try to get the errors form the session
		if ($this->app['session']->has('errors')) {
			$this->errors = $this->app['session']->get('errors');
		}

		// If we're given a raw Validator, go fetch the errors in it
		if ($validator instanceof Validator) {
			$this->errors = $validator->getMessageBag();
		} else {
			if ($validator instanceof MessageBag) {
				$this->errors = $validator;
			}
		}

		return $this->errors;
	}

	/**
	 * Add live validation rules
	 *
	 * @param  array *$rules An array of Laravel rules
	 *
	 * @return  void
	 */
	public function withRules()
	{
		$rules = call_user_func_array('array_merge', func_get_args());

		// Parse the rules according to Laravel conventions
		foreach ($rules as $name => $fieldRules) {
			$expFieldRules = $fieldRules;
			if (!is_array($expFieldRules)) {
				$expFieldRules = explode('|', $expFieldRules);
				$expFieldRules = array_map('trim', $expFieldRules);
			}

			foreach ($expFieldRules as $rule) {

				$parameters = null;

				if (($colon = strpos($rule, ':')) !== false) {
					$rulename = substr($rule, 0, $colon);

					/**
					 * Regular expressions may contain commas and should not be divided by str_getcsv.
					 * For regular expressions we are just using the complete expression as a parameter.
					 */
					if ($rulename !== 'regex') {
						$parameters = str_getcsv(substr($rule, $colon + 1));
					} else {
						$parameters = [substr($rule, $colon + 1)];
					}
				}

				// Exclude unsupported rules
				$rule = is_numeric($colon) ? substr($rule, 0, $colon) : $rule;

				// Store processed rule in Former's array
				if (!isset($parameters)) {
					$parameters = array();
				}

				$this->rules[$name][$rule] = $parameters;
			}
		}
	}

	/**
	 * Switch the framework used by Former
	 *
	 * @param string $framework The name of the framework to use
	 */
	public function framework($framework = null)
	{
		if (!$framework) {
			return $this->app['former.framework']->current();
		}

		$this->setOption('framework', $framework);

		$framework = $this->getFrameworkInstance($framework);
		$this->app->bind('former.framework', function ($app) use ($framework) {
			return $framework;
		});
	}

	/**
	 * Get a new framework instance
	 *
	 * @param string $framework
	 *
	 * @throws Exceptions\InvalidFrameworkException
	 * @return \Former\Interfaces\FrameworkInterface
	 */
	public function getFrameworkInstance($framework)
	{
		$formerClass = __NAMESPACE__.'\Framework\\'.$framework;

		//get interfaces of the given framework
		$interfaces = class_exists($framework) ? class_implements($framework) : array();

		if(class_exists($formerClass)) {
			$returnClass = $formerClass;
		} elseif(class_exists($framework) && isset($interfaces['Former\Interfaces\FrameworkInterface'])) {
			// We have some outside class, lets return it.
			$returnClass = $framework;
		} else {
			throw (new InvalidFrameworkException())->setFramework($framework);
		}

		return new $returnClass($this->app);
	}

	/**
	 * Get an option from the config
	 *
	 * @param string $option  The option
	 * @param mixed  $default Optional fallback
	 *
	 * @return mixed
	 */
	public function getOption($option, $default = null)
	{
		return $this->app['config']->get('former.'.$option, $default);
	}

	/**
	 * Set an option on the config
	 *
	 * @param string $option
	 * @param string $value
	 */
	public function setOption($option, $value)
	{
		return $this->app['config']->set('former.'.$option, $value);
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// BUILDERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Closes a form
	 *
	 * @return string A form closing tag
	 */
	public function close()
	{
		if ($this->app->bound('former.form')) {
			$closing = $this->app['former.form']->close();
		}

		// Destroy instances
		$instances = array('former.form', 'former.form.framework');
		foreach ($instances as $instance) {
			$this->app[$instance] = null;
			unset($this->app[$instance]);
		}

		// Reset populator
		$this->app['former.populator']->reset();

		// Reset all values
		$this->errors = null;
		$this->rules  = array();

		return isset($closing) ? $closing : null;
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// HELPERS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the errors for the current field
	 *
	 * @param  string $name A field name
	 *
	 * @return string       An error message
	 */
	public function getErrors($name = null)
	{
		// Get name and translate array notation
		if (!$name and $this->app['former.field']) {
			$name = $this->app['former.field']->getName();

			// Always return empty string for anonymous fields (i.e. fields with no name/id)
			if (!$name) {
				return '';
			}
		}

		if ($this->errors and $name) {
			$name = str_replace(array('[', ']'), array('.', ''), $name);
			$name = trim($name, '.');

			return $this->errors->first($name);
		}

		return $this->errors;
	}

	/**
	 * Get a rule from the Rules array
	 *
	 * @param  string $name The field to fetch
	 *
	 * @return array        An array of rules
	 */
	public function getRules($name)
	{
		// Check the rules for the name as given
		$ruleset = array_get($this->rules, $name);

		// If no rules found, convert to dot notation and try again
		if (is_null($ruleset)) {
			$name = str_replace(array('[', ']'), array('.', ''), $name);
			$name = trim($name, '.');
			$ruleset = array_get($this->rules, $name);
		}

		return $ruleset;
	}
}
