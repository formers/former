<?php
/**
 * Field
 *
 * Abstracts general fields parameters (type, value, name) and
 * reforms a correct form field depending on what was asked
 */
namespace Former;

use \File;

abstract class Field
{
  /**
   * The field type
   * @var string
   */
  protected $type;

  /**
   * The field value
   * @var string
   */
  protected $value;

  /**
   * A label for the field (if not using Bootstrap)
   * @var string
   */
  protected $label;

  /**
   * The field attributes
   * @var array
   */
  protected $attributes = array();

  /**
   * The field's control group
   * @var ControlGroup
   */
  protected $controlGroup;

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// INTERFACE /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set up a Field instance
   *
   * @param string $type A field type
   */
  public function __construct($type, $name, $label, $value, $attributes)
  {
    // Set base parameters
    $this->attributes = (array) $attributes;
    $this->label      = $label;
    $this->name       = $name;
    $this->type       = $type;
    $this->value      = $value;

    // Set magic parameters (repopulated value, translated label, etc)
    if(Config::get('automatic_label')) $this->ponder($name, $label);
    if($type != 'password') $this->value = $this->repopulate();
    if(Config::get('live_validation')) $this->addRules();

    // Link Control group
    if (Framework::isnt(null)) {
      $this->controlGroup = new ControlGroup($this->label);
    }
  }

  /**
   * Dynamically set attributes
   *
   * @param  string $method     An attribute
   * @param  array  $parameters Its value(s)
   */
  public function __call($method, $parameters)
  {
    // Replace underscores
    $method = str_replace('_', '-', $method);

    // Get value and set it
    $value = array_get($parameters, 0, 'true');
    $this->setAttribute($method, $value);

    return $this;
  }

  /**
   * Get a Field variable or an attribute
   *
   * @param  string $attribute The desired attribute
   * @return string            Its value
   */
  public function __get($attribute)
  {
    if(isset($this->$attribute)) return $this->$attribute;
    else return array_get($this->attributes, $attribute);
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// FUNCTIONS //////////////////////////////
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

  ////////////////////////////////////////////////////////////////////
  //////////////////////// SETTERS AND GETTERS ///////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Returns this Field's control group
   *
   * @return ControlGroup
   */
  public function getControl()
  {
    return $this->controlGroup;
  }

  /**
   * Adds a label to the control group/field
   *
   * @param  string $text A label
   * @return Field        A field
   */
  public function label($text)
  {
    if($this->controlGroup) $this->controlGroup->setLabel($text);
    else $this->label = Helpers::translate($text);

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

    if(!$already) $this->value = $value;
  }

  /**
   * Set an attribute
   *
   * @param string $attribute An attribute
   * @param string $value     Its value
   */
  public function setAttribute($attribute, $value = null)
  {
    $this->attributes[$attribute] = $value;
  }

  /**
   * Set a bunch of parameters at once
   *
   * @param array   $attributes An associative array of attributes
   * @param boolean $merge      Whether they should be merged to the old ones
   */
  public function setAttributes($attributes, $merge = true)
  {
    $attributes = (array) $attributes;

    $this->attributes = $merge
      ? array_merge($this->attributes, $attributes)
      : $attributes;
  }

  /**
   * Add a class to the current field
   *
   * @param string $class The class to add
   */
  public function addClass($class)
  {
    $this->attributes = Helpers::addClass($this->attributes, $class);
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// HELPERS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Use values stored in Former to populate the current field
   */
  private function repopulate($fallback = null)
  {
    if(is_null($fallback)) $fallback = $this->value;

    // Get values from POST, populated, and manually set value
    $post     = Former::getPost($this->name);
    $populate = Former::getValue($this->name);

    // Assign a priority to each
    if(!is_null($post)) $value = $post;
    elseif(!is_null($populate)) $value = $populate;
    else $value = $fallback;

    return $value;
  }

  /**
   * Set a maximum value to a field
   *
   * @param integer $max
   */
  private function setMax($max)
  {
    $attribute = $this->type == 'number' ? 'max' : 'maxlength';
    $this->attributes[$attribute] = $max;
  }

  /**
   * Set a minimum value to a field
   *
   * @param integer $min
   */
  private function setMin($min)
  {
    $attribute = $this->type == 'number' ? 'min' : 'minlength';
    $this->attributes[$attribute] = $min;
  }

  /**
   * Ponders a label and a field name, and tries to get the best out of it
   *
   * @param  string $label A label
   * @param  string $name  A field name
   * @return array         A label and a field name
   */
  private function ponder($name, $label)
  {
    // Check for the two possibilities
    if($label and is_null($name)) $name = \Str::slug($label);
    elseif(is_null($label) and $name) $label = $name;

    // Attempt to translate the label
    $label = Helpers::translate($label);

    // Save values
    $this->name  = $name;
    $this->label = $label;
  }

  /**
   * Add the corresponding rules to the field's attributes
   */
  private function addRules()
  {
    // Get the different rules assigned to this field
    $rules = Former::getRules($this->name);
    if(!$rules) return false;

    // Iterate through them and add the attributes
    foreach ($rules as $rule => $parameters) {
      switch ($rule) {
        case 'email':
          $this->type = 'email';
          break;
        case 'url':
          $this->type = 'url';
          break;
        case 'required';
          $this->required();
          break;
        case 'after':
        case 'before':
          $format = 'Y-m-d';
          if ($this->type == 'datetime' or
              $this->type == 'datetime-local') {
                $format .= '\TH:i:s';
          }

          $date = strtotime(array_get($parameters, 0));
          $attribute = ($rule == 'before') ? 'max' : 'min';
          $this->attributes[$attribute] = date($format, $date);
          break;
        case 'max':
          $this->setMax(array_get($parameters, 0));
          break;
        case 'min':
          $this->setMin(array_get($parameters, 0));
          break;
        case 'integer':
          $this->attributes['pattern'] = '\d+';
          break;
        case 'mimes':
        case 'image':
          if ($this->type == 'file') {
            $ext = $rule == 'image' ? array('jpg', 'png', 'gif', 'bmp') : $parameters;
            $mimes = array_map('File::mime', $ext);
            $this->attributes['accept'] = implode(',', $mimes);
          }
          break;
        case 'numeric':
          if ($this->type == 'number') $this->attributes['step'] = 'any';
          else $this->attributes['pattern'] = '[+-]?\d*\.?\d+';
          break;
        case 'not_numeric':
          $this->attributes['pattern'] = '\D+';
          break;
        case 'alpha':
          $this->attributes['pattern'] = '[a-zA-Z]+';
          break;
        case 'alpha_num':
          $this->attributes['pattern'] = '[a-zA-Z0-9]+';
          break;
        case 'alpha_dash':
          $this->attributes['pattern'] = '[a-zA-Z0-9_\-]+';
          break;
        case 'between':
          list($min, $max) = $parameters;
          $this->setMin($min);
          $this->setMax($max);
          break;
        case 'in':
          $possible = (sizeof($parameters) == 1) ? $parameters[0] : '('.join('|', $parameters).')';
          $this->attributes['pattern'] = '^' .$possible. '$';
          break;
        case 'not_in':
          $this->attributes['pattern'] = '(?:(?!^' .join('$|^', $parameters). '$).)*';
          break;
        case 'match':
          $this->attributes['pattern'] = substr($parameters[0], 1, -1);
          break;
        default:
          continue;
          break;
      }
    }
  }
}
