<?php
/**
 * Form
 *
 * Construct and manages the form wrapping all fields
 */
namespace Former\Form;

use \Former\Traits\FormerObject;
use \Underscore\Types\Arrays;
use \Underscore\Types\String;

class Form extends FormerObject
{
  /**
   * The current environment
   * @var Illuminate\Container
   */
  protected $app;

  /**
   * The Form type
   * @var string
   */
  private $type = null;

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
   * Whether a form is opened or not
   * @var boolean
   */
  private static $opened = false;

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function __construct($app)
  {
    $this->app = $app;
  }

  /**
   * Opens up magically a form
   *
   * @param  string $type       The form type asked
   * @param  array  $parameters Parameters passed
   * @return string             A form opening tag
   */
  public function open($type, $parameters)
  {
    $action     = Arrays::get($parameters, 0);
    $method     = Arrays::get($parameters, 1, 'POST');
    $attributes = Arrays::get($parameters, 2, array());
    $secure     = Arrays::get($parameters, 3, false);

    // Fetch errors if asked for
    if ($this->app['former']->getOption('fetch_errors')) {
      $this->app['former']->withErrors();
    }

    // Open the form
    $this->action     = $action;
    $this->attributes = $attributes;
    $this->method     = $method;
    $this->secure     = $secure;

    // Add any effect of the form type
    $this->type = $this->applyType($type);

    // Add supplementary classes
    $this->attributes = $this->app['former']->getFramework()->addFormClasses($this->attributes, $this->type);

    return $this;
  }

  /**
   * Closes a Form
   *
   * @return string A closing <form> tag
   */
  public function close()
  {
    static::$opened = false;

    return '</form>';
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// STATIC HELPERS ////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Whether a form is currently opened or not
   *
   * @return boolean
   */
  public static function isOpened()
  {
    return static::$opened;
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

    return $this;
  }

  /**
   * Change the form's method
   *
   * @param  string $method The method to use
   */
  public function method($method)
  {
    $this->method = $method;

    return $this;
  }

  /**
   * Whether the form should be secure
   *
   * @param  boolean $secure Secure or not
   */
  public function secure($secure = true)
  {
    $this->secure = $secure;

    return $this;
  }

  /**
   * Alias for $this->app['former']->withRules
   *
   * @param array $rules Rules
   */
  public function rules()
  {
    call_user_func_array(array($this->app['former'], 'withRules'), func_get_args());

    return $this;
  }

  /**
   * Outputs the current form opened
   *
   * @return string A <form> opening tag
   */
  public function __toString()
  {
    // Mark the form as opened
    static::$opened = true;

    return $this->app['form']->open($this->action, $this->method, $this->attributes, $this->secure);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Apply various parameters according to form type
   *
   * @param string $type The original form type provided
   * @return string The final form type
   */
  private function applyType($type)
  {
    // If classic form
    if ($type == 'open') {
      return $this->app['former']->getOption('default_form_type');
    }

    // Look for HTTPS form
    if (String::contains($type, 'secure')) {
      $type = String::remove($type, 'secure');
      $this->secure = true;
    }

    // Look for file form
    if (String::contains($type, 'for_files')) {
      $type = String::remove($type, 'for_files');
      $this->attributes['enctype'] = 'multipart/form-data';
    }

    // Calculate form type
    $type = String::remove($type, 'open');
    $type = trim($type, '_');

    // Use default form type if the one provided is invalid
    if (!in_array($type, $this->availableTypes)) {
      $type = $this->app['former']->getOption('default_form_type');
    }

    return $type;
  }
}
