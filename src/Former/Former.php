<?php
/**
 * Former
 *
 * Superset of Field ; helps the user interact with it and its classes
 * Various form helpers for repopulation, rules, etc.
 */
namespace Former;

class Former
{
  /**
   * The current field being worked on
   * @var Field
   */
  protected static $field;

  /**
   * The current form being worked on
   * @var Form
   */
  protected static $form;

  /**
   * Values populating the form
   * @var array
   */
  protected static $values;

  /**
   * The form's errors
   * @var Message
   */
  protected static $errors;

  /**
   * An array of rules to use
   * @var array
   */
  protected static $rules = array();

  /**
   * The namespace of fields
   */
  const FIELDSPACE = 'Former\Fields\\';

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// INTERFACE /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Creates a field instance
   *
   * @param  string $method     The field type
   * @param  array  $parameters An array of parameters
   * @return Former
   */
  public static function __callStatic($method, $parameters)
  {
    // Form opener
    if (str_contains($method, 'open')) {
      static::$form = new Form;
      static::form()->open($method, $parameters);

      return new static;
    }

    // Avoid conflict with chained label method
    if ($method == 'label') {
      return call_user_func_array('static::_label', $parameters);
    }

    // Checking for any supplementary classes
    $classes = explode('_', $method);
    $method  = array_pop($classes);

    // Destroy previous field instance
    static::$field = null;

    // Picking the right class
    if (class_exists(static::FIELDSPACE.ucfirst($method))) {
      $callClass = ucfirst($method);
    } else {
      switch ($method) {
        case 'submit':
        case 'reset':
          $callClass = 'Button';
          break;
        case 'multiselect':
          $callClass = 'Select';
          break;
        case 'checkboxes':
          $callClass = 'Checkbox';
          break;
        case 'radios':
          $callClass = 'Radio';
          break;
        case 'files':
          $callClass = 'File';
          break;
        default:
          $callClass = 'Input';
          break;
      }
    }

    // Check for potential errors
    if (!class_exists(static::FIELDSPACE.$callClass)) {
      throw new \Exception('The class "' .static::FIELDSPACE.$callClass. '" called by field "' .$method. '" doesn\'t exist');
    }

    // Listing parameters
    $class = static::FIELDSPACE.$callClass;
    static::$field = new $class(
      $method,
      array_get($parameters, 0),
      array_get($parameters, 1),
      array_get($parameters, 2),
      array_get($parameters, 3),
      array_get($parameters, 4),
      array_get($parameters, 5)
    );

    // Inline checkboxes
    if(in_array($callClass, array('Checkbox', 'Radio')) and
      in_array('inline', $classes)) {
      static::$field->inline();
    }

    // Filter classes according to field type
    $classes = $callClass == 'Button'
      ? Framework::getButtonTypes($classes)
      : Framework::getFieldSizes($classes);

    // Add any supplementary classes we found
    if($classes) static::$field->addClass($classes);

    // As Buttons are more of a helper class, we return them directly
    if($callClass == 'Button') return static::$field;

    return new static;
  }

  /**
   * Pass a chained method to the Field
   *
   * @param  string $method     The method to call
   * @param  array  $parameters Its parameters
   * @return Former
   */
  public function __call($method, $parameters)
  {
    if (!static::form()->isOpened() and static::$form) {
      $object = static::$form;
    } else {
      $object = method_exists($this->control(), $method)
        ? $this->control()
        : static::$field;
    }

    // Call the function on the corresponding class
    call_user_func_array(array($object, $method), $parameters);

    return $this;
  }

  /**
   * Get an attribute/value from the Field instance
   *
   * @param  string $attribute The requested attribute
   * @return string            Its value
   */
  public function __get($attribute)
  {
    return $this->field()->$attribute;
  }

  /**
   * Prints out Field wrapped in ControlGroup
   *
   * @return string A form field
   */
  public function __toString()
  {
    if (static::$form and !static::$form->isOpened()) {
      return static::form()->__toString();
    }

    // Dry syntax (hidden fields, plain fields)
    if (static::$field->isUnwrappable()) {
      $html = static::$field->__toString();
    }

    // Bootstrap syntax
    elseif (Framework::isnt(null) and static::$form) {
      $html = $this->control()->wrapField(static::$field);
    }

    // Classic syntax
    else {
      $html  = Framework::label(static::$field);
      $html .= static::$field;
    }

    return $html;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// TOOLKIT ///////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Add values to populate the array
   *
   * @param mixed $values Can be an Eloquent object or an array
   */
  public static function populate($values)
  {
    static::$values = $values;
  }

  /**
   * Set a specific value in the population array
   *
   * @param string $key   The key to change
   * @param string $value The new value
   */
  public static function populateField($key, $value)
  {
    if (is_object(static::$values)) {
      static::$values->$key = $value;
    } else {
      static::$values[$key] = $value;
    }
  }

  /**
   * Get a value from the object/array
   *
   * @param  string $name     The key to retrieve
   * @param  string $fallback Fallback value if nothing found
   * @return mixed            Its value
   */
  public static function getValue($name, $fallback = null)
  {
    // Object values
    if (is_object(static::$values)) {

      // Transform the name into an array
      $value = static::$values;
      $name  = str_contains($name, '.') ? explode('.', $name) : (array) $name;

      // Dive into the model
      foreach ($name as $k => $r) {

        // Multiple results relation
        if (is_array($value)) {
          foreach ($value as $subkey => $submodel) {
            $value[$subkey] = isset($submodel->$r) ? $submodel->$r : $fallback;
          }
          continue;
        }

        // Single model relation
        if(isset($value->$r) or method_exists($value, 'get_'.$r)) $value = $value->$r;
        else {
          $value = $fallback;
          break;
        }
      }

      return $value;
    }

    // Plain array
    return array_get(static::$values, $name, $fallback);
  }

  /**
   * Fetch a field value from both the new and old POST array
   *
   * @param  string $name     A field name
   * @param  string $fallback A fallback if nothing was found
   * @return string           The results
   */
  public static function getPost($name, $fallback = null)
  {
    return \Input::get($name, \Input::old($name, $fallback));
  }

  /**
   * Set the errors to use for validations
   *
   * @param Message $validator The result from a validation
   */
  public static function withErrors($validator = null)
  {
    // Try to get the errors form the session
    if(\Session::has('errors')) $errors = \Session::get('errors');

    // If we're given a raw Validator, go fetch the errors in it
    if($validator instanceof \Laravel\Validator) $errors = $validator->errors;

    // If we found errors, bind them to the form
    if(isset($errors)) static::$errors = $errors;
  }

  /**
   * Add live validation rules
   *
   * @param  array *$rules An array of Laravel rules
   */
  public static function withRules()
  {
    $rules = call_user_func_array('array_merge', func_get_args());

    // Parse the rules according to Laravel conventions
    foreach ($rules as $name => $fieldRules) {
      foreach (explode('|', $fieldRules) as $rule) {

        // If we have a rule with a value
        if (($colon = strpos($rule, ':')) !== false) {
          $parameters = str_getcsv(substr($rule, $colon + 1));
       }

       // Exclude unsupported rules
       $rule = is_numeric($colon) ? substr($rule, 0, $colon) : $rule;

       // Store processed rule in Former's array
       if(!isset($parameters)) $parameters = array();
       static::$rules[$name][$rule] = $parameters;
      }
    }
  }

  /**
   * Alter Former's settings
   *
   * @param  string $key   The option to change
   * @param  string $value Its new value
   */
  public static function config($key, $value)
  {
    if($key == 'framework') return Framework::useFramework($value);

    return Config::set($key, $value);
  }

  /**
   * Set the useBootstrap option
   *
   * @param  boolean $boolean Whether we should use Bootstrap syntax or not
   */
  public static function framework($framework)
  {
    return Framework::useFramework($framework) == $framework;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// BUILDERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Closes a form
   *
   * @return string A form closing tag
   */
  public static function close()
  {
    $close = static::form()->close();

    // Destroy Form instance
    static::$form = null;

    // Reset all values
    static::$values = null;
    static::$errors = null;
    static::$rules  = null;

    return $close;
  }

  /**
   * Generate a hidden field containing the current CSRF token.
   *
   * @return string
   */
  public static function token()
  {
    return static::hidden(\Session::csrf_token, \Session::token())->__toString();
  }

  /**
   * Creates a label tag
   *
   * @param  string $label      The label content
   * @param  string $name       The field the label's for
   * @param  array  $attributes The label's attributes
   * @return string             A <label> tag
   */
  public static function _label($label, $name = null, $attributes = array())
  {
    $label = Helpers::translate($label);

    return \Form::label($name, $label, $attributes);
  }

  /**
   * Creates a form legend
   *
   * @param  string $legend     The text
   * @param  array  $attributes Its attributes
   * @return string             A legend tag
   */
  public static function legend($legend, $attributes = array())
  {
    $legend = Helpers::translate($legend);

    return '<legend'.\HTML::attributes($attributes).'>' .$legend. '</legend>';
  }

  /**
   * Writes the form actions
   *
   * @return string A .form-actions block
   */
  public static function actions()
  {
    $buttons = func_get_args();

    $actions  = '<div class="form-actions">';
      $actions .= implode(' ', (array) $buttons);
    $actions .= '</div>';

    return $actions;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// HELPERS ///////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the errors for the current field
   *
   * @param  string $name A field name
   * @return string       An error message
   */
  public static function getErrors($name = null)
  {
    // Get name and translate array notation
    if(!$name) $name = static::$field->name;
    $name = preg_replace('/\[([a-z]+)\]/', '.$1', $name);

    if (static::$errors) {
      return static::$errors->first($name);
    }
  }

  /**
   * Get a rule from the Rules array
   *
   * @param  string $name The field to fetch
   * @return array        An array of rules
   */
  public static function getRules($name)
  {
    return array_get(static::$rules, $name);
  }

  /**
   * Returns the current ControlGroup
   *
   * @return ControlGroup
   */
  public static function control()
  {
    if(!static::$field) return false;

    return static::$field->getControl();
  }

  /**
   * Returns the current Form
   *
   * @return Form
   */
  public static function form()
  {
    if (!static::$form) return new Form;
    return static::$form;
  }

  /**
   * Get the current field instance
   *
   * @return Field
   */
  public static function field()
  {
    if(!static::$field) return false;

    return self::$field;
  }
}
