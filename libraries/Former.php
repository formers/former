<?php
/**
 *
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
  private static $field;

  /**
   * Values populating the form
   * @var array
   */
  private static $values;

  /**
   * The form's errors
   * @var Message
   */
  private static $errors;

  /**
   * An array of rules to use
   * @var array
   */
  private static $rules = array();

  /**
   * An array of the rules usable in live validation
   * @var array
   */
  private static $supportedRules = array(
    'alpha', 'required', 'min', 'max', 'numeric', 'not_numeric');

  /**
   * The type of form we're displaying
   * @var string
   */
  private static $formType;

  // Former options ------------------------------------------------ /

  /**
   * A class to be added to required fields
   * @var string
   */
  public static $requiredClass = 'required';

  /**
   * Whether Former should use Bootstrap's syntax
   * @var boolean
   */
  public static $useBootstrap = true;

  /**
   * The main place where former will look for translations
   * @var string
   */
  public static $translateFrom = 'validation.attributes';

  // Field types --------------------------------------------------- /

  /**
   * The available input sizes
   * @var array
   */
  private static $FIELD_SIZES = array(
    'mini', 'small', 'medium', 'large', 'xlarge', 'xxlarge',
    'span1', 'span2', 'span3', 'span4', 'span5', 'span6', 'span7',
    'span8', 'span9', 'span10', 'span11', 'span12');

  /**
   * The available form types
   * @var array
   */
  private static $FORM_TYPES = array('horizontal', 'vertical', 'inline', 'search');

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
      return static::openForm($method, $parameters);
    }

    // Checking for any supplementary classes
    $classes = explode('_', $method);
    $method  = array_pop($classes);

    // Picking the right class
    switch ($method) {
      case 'select':
      case 'multiselect':
        $callClass = 'Select';
        break;
      case 'checkbox':
      case 'checkboxes':
        $callClass = 'Checkbox';
        break;
      case 'textarea':
        $callClass = 'Textarea';
        break;
      case 'radio':
      case 'radios':
        $callClass = 'Radio';
        break;
      case 'uneditable':
        $callClass = 'Uneditable';
        break;
      default:
        $callClass = 'Input';
        break;
    }

    // Listing parameters
    $class = '\Former\Fields\\'.$callClass;
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

    // Add any size we found
    $sizes = array_intersect(static::$FIELD_SIZES, $classes);
    if ($sizes) {
      $size = $sizes[key($sizes)];
      static::$field->addClass('input-'.$size);
    }

    return new Former;
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
    $object = method_exists($this->control(), $method)
      ? $this->control()
      : static::$field;

    // Call the function on the corresponding class
    call_user_func_array(array($object, $method), $parameters);

    return $this;
  }

  /**
   * Prints out Field wrapped in ControlGroup
   *
   * @return string A form field
   */
  public function __toString()
  {
    // Dry syntax (hidden fields, plain fields)
    if (static::$field->type == 'hidden' or
      static::$formType == 'search' or
      static::$formType == 'inline') {
        $html = static::$field->__toString();
    } elseif (static::$useBootstrap) {

      // Bootstrap syntax
      $controlGroup = $this->control();

      $html = $controlGroup->open();
        $html .= $controlGroup->getLabel(static::$field->name);
        $html .= '<div class="controls">';
          $html .= $controlGroup->prependAppend(static::$field);
          $html .= $controlGroup->getHelp();
        $html .= '</div>';
      $html .= $controlGroup->close();
    } else {

      // Classic syntax
      $html = \Form::label(static::$field->name, static::$field->label);
      $html .= static::$field;
    }

    // Destroy field instance
    static::$field = null;

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
   * Get a value from the object/array
   *
   * @param  string $name The key to retrieve
   * @return mixed        Its value
   */
  public static function getValue($name)
  {
    return is_object(static::$values)
      ? static::$values->{$name}
      : array_get(static::$values, $name);
  }

  /**
   * Set the errors to use for validations
   *
   * @param Message $validator The result from a validation
   */
  public static function withErrors($validator)
  {
    // If we're given a raw Validator, go fetch the errors in it
    if($validator instanceof Validator) $validator = $validator->errors;

    static::$errors = $validator;
  }

  /**
   * Add live validation rules
   *
   * @param  array $rules An array of Laravel rules
   */
  public static function withRules($rules)
  {
    // Parse the rules according to Laravel conventions
    foreach ($rules as $name => $fieldRules) {
      foreach (explode('|', $fieldRules) as $rule) {

        // If we have a rule with a value
        if (($colon = strpos($rule, ':')) !== false) {
          $parameters = str_getcsv(substr($rule, $colon + 1));
       }

       // Exclude unsupported rules
       $rule = is_numeric($colon) ? substr($rule, 0, $colon) : $rule;
       if(!in_array($rule, static::$supportedRules)) continue;

       // Store processed rule in Former's array
       if(!isset($parameters)) $parameters = array();
       static::$rules[$name][$rule] = $parameters;
      }
    }
  }

  /**
   * Set the useBootstrap option
   *
   * @param  boolean $boolean Whether we should use Bootstrap syntax or not
   */
  public function useBootstrap($boolean = true)
  {
    static::$useBootstrap = $boolean;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// BUILDERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Opens a form dynamically
   *
   * @param  string $static     The method called
   * @param  array  $parameters Its parameters
   * @return string             A form opening tag
   */
  private static function openForm($static, $parameters)
  {
    $method     = 'POST';
    $secure     = false;
    $type       = 'vertical';
    $action     = array_get($parameters, 0);
    $attributes = array_get($parameters, 1);

    // Look for HTTPS form
    if(str_contains($static, 'secure')) $scure = true;

    // Look for file form
    if(str_contains($static, 'for_files')) $attributes['enctype'] = 'multipart/form-data';

    // Look for a file type
    foreach (static::$FORM_TYPES as $class) {
      if (str_contains($static, $class)) {
        $type = $class;
        break;
      }
    }
    $attributes = Helpers::addClass($attributes, 'form-'.$class);

    // Store current form's type
    static::$formType = $class;

    // Open the form
    return \Form::open($action, $method, $attributes, $secure);
  }

  /**
   * Closes a form
   *
   * @return string A form closing tag
   */
  public static function close()
  {
    return '</close>';
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
    $actions .= is_array($buttons) ? implode(' ', $buttons) : $buttons;
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
  public static function getErrors()
  {
    if (static::$errors) {
      return static::$errors->first(static::$field->name);
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
