<?php
/**
 *
 * Former
 *
 * Superset of Field ; helps the user interact with it and its classes
 * Various form helpers for repopulation, rules, etc.
 */
namespace Former;

use Cerberus\Toolkit\Laravel,
    \Input;

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

  // Field types --------------------------------------------------- /

  /**
   * The available input sizes
   * @var array
   */
  private static $FIELD_SIZES = array('mini', 'small', 'medium', 'large', 'xlarge', 'xxlarge');

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
    if(str_contains($method, 'open')) {
      return static::openForm($method, $parameters);
    }

    // Checking for any supplementary classes
    $classes = explode('_', $method);
    $method  = array_pop($classes);

    // Picking the right class
    switch($method) {
      case 'select':
      case 'multiselect':
        $class = 'Select';
        break;
      case 'checkbox':
      case 'checkboxes':
        $class = 'Checkbox';
        break;
      case 'textarea':
        $class = 'Textarea';
        break;
      default:
        $class = 'Input';
        break;
    }

    // Listing parameters
    $name       = array_get($parameters, 0);
    $label      = array_get($parameters, 1);
    $value      = array_get($parameters, 2);
    $attributes = array_get($parameters, 3, array());

    // Process data
    $value = static::getValue($name, $value);
    list($label, $name) = static::getLabelName($label, $name);

    // Add any size we found
    $sizes = array_intersect(static::$FIELD_SIZES, $classes);
    if($sizes) {
      $size = $sizes[key($sizes)];
      $attributes = Helpers::addClass($attributes, 'input-'.$size);
    }

    // Calling the selected class
    $class = '\Former\\'.$class;
    static::$field = new $class($method, $name, $label, $value, $attributes);

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
    // Hidden fields don't need no control group
    if(static::$field->type == 'hidden' or
      static::$formType == 'search' or
      static::$formType == 'inline') {
      return static::$field->__toString();
    }

    $controlGroup = $this->control();

    $html = $controlGroup->open();
     $html .= $controlGroup->getLabel(static::$field->name);
      $html .= '<div class="controls">';
        $html .= $controlGroup->prependAppend(static::$field);
        $html .= $controlGroup->getHelp();
      $html .= '</div>';
    $html .= $controlGroup->close();

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
  public static function setValues($values)
  {
    static::$values = $values;
  }

  public static function setErrors($errors)
  {
    static::$errors = $errors;
  }

  public static function getValue($key, $fallback = null)
  {
    // Get value from either object or array
    $value = is_object(static::$values)
      ? static::$values->{$key}
      : array_get(static::$values, $key);

    // If nothing found, replace by fallback
    if(!$value) $value = $fallback;

    // Overwrite value by POST if present
    $value = Input::get($key, Input::old($key, $value));

    return $value;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// BUILDERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

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
    foreach(static::$FORM_TYPES as $class) {
      if(str_contains($static, $class)) {
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
    if(static::$errors) {
      return static::$errors->first(static::$field->name);
    }
  }

  /**
   * Ponders a label and a field name, and tries to gets the best out of it
   *
   * @param  string $label     A label
   * @param  string $fieldname A field name
   * @return array             A label and a field name
   */
  private static function getLabelName($label, $fieldname = null)
  {
    // Check for the two possibilities
    if($label and !$fieldname) $fieldname = \Str::slug($label);
    elseif(!$label and $fieldname) $label = $fieldname;

    // Attempt to translate the label
    $label = Laravel::translate($label);
    $label = ucfirst($label);

    return array($label, $fieldname);
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

  public static function field()
  {
    if(!static::$field) return false;

    return self::$field;
  }
}
