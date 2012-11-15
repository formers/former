<?php
/**
 * Form
 *
 * Construct and manages the form wrapping all fields
 */
namespace Former;

class Form extends Traits\FormerObject
{
  /**
   * The Form type
   * @var string
   */
  public $type = null;

  /**
   * The available form types
   * @var array
   */
  private $availableTypes = array('horizontal', 'vertical', 'inline', 'search');

  /**
   * The destination of the current form
   * @var string
   */
  private $action;

  /**
   * The form method
   * @var string
   */
  private $method;

  /**
   * Whether the form should be secured or not
   * @var boolean
   */
  private $secure;

  /**
   * Whether the current form is opened or not
   * @var boolean
   */
  private $opened = false;

  /**
   * Opens up magically a form
   *
   * @param  string $typeAsked  The form type asked
   * @param  array  $parameters Parameters passed
   * @return string             A form opening tag
   */
  public function open($typeAsked, $parameters)
  {
    $action     = array_get($parameters, 0);
    $method     = array_get($parameters, 1, 'POST');
    $attributes = array_get($parameters, 2, array());
    $secure     = array_get($parameters, 3, false);

    // If classic form
    if($typeAsked == 'open') $type = Config::get('default_form_type');
    else {
      // Look for HTTPS form
      if (str_contains($typeAsked, 'secure')) {
        $typeAsked = str_replace('secure', null, $typeAsked);
        $secure = true;
      }

      // Look for file form
      if (str_contains($typeAsked, 'for_files')) {
        $typeAsked = str_replace('for_files', null, $typeAsked);
        $attributes['enctype'] = 'multipart/form-data';
      }

      // Calculate form type
      $type = trim(str_replace('open', null, $typeAsked), '_');
      if(!in_array($type, $this->availableTypes)) $type = Config::get('default_form_type');
    }

    // Add the final form type
    $attributes = Helpers::addClass($attributes, 'form-'.$type);

    // Store it
    $this->type = $type;

    // Fetch errors if asked for
    if (Config::get('fetch_errors')) {
      Former::withErrors();
    }

    // Open the form
    $this->action     = $action;
    $this->method     = $method;
    $this->attributes = $attributes;
    $this->secure     = $secure;

    return $this;
  }

  /**
   * Closes a Form
   *
   * @return string A closing <form> tag
   */
  public function close()
  {
    return '</form>';
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////////// HELPERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function isOpened()
  {
    return $this->opened;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// CHAINED METHODS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Change the form's action
   *
   * @param  string $action The new action
   */
  public function action($action)
  {
    $this->action = $action;
  }

  /**
   * Change the form's method
   *
   * @param  string $method The method to use
   */
  public function method($method)
  {
    $this->method = $method;
  }

  /**
   * Whether the form should be secure
   *
   * @param  boolean $secure Secure or not
   */
  public function secure($secure = true)
  {
    $this->secure = $secure;
  }

  /**
   * Alias for Former::withRules
   *
   * @param array $rules Rules
   */
  public function rules()
  {
    return call_user_func_array('\Former\Former::withRules', func_get_args());
  }

  /**
   * Outputs the current form opened
   *
   * @return string A <form> opening tag
   */
  public function __toString()
  {
    // Mark the form as opened
    $this->opened = true;

    return \Form::open($this->action, $this->method, $this->attributes, $this->secure);
  }
}
