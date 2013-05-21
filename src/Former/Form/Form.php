<?php
namespace Former\Form;

use Former\Former;
use Former\Populator;
use Former\Traits\FormerObject;
use Underscore\Methods\ArraysMethods as Arrays;
use Underscore\Methods\StringMethods as String;

/**
 * Construct and manages the form wrapping all fields
 */
class Form extends FormerObject
{

  /**
   * The Former instance
   *
   * @var Former
   */
  protected $former;

  /**
   * The Framework Interface
   *
   * @var FrameworkInterface
   */
  protected $framework;

  /**
   * The URL generator
   *
   * @var UrlGenerator
   */
  protected $url;

  /**
   * The Populator
   *
   * @var Populator
   */
  protected $populator;

  /**
   * The Form type
   *
   * @var string
   */
  protected $type = null;

  /**
   * The available form types
   *
   * @var array
   */
  protected $availableTypes = array('horizontal', 'vertical', 'inline', 'search');

  /**
   * The destination of the current form
   *
   * @var string
   */
  protected $action;

  /**
   * The form method
   *
   * @var string
   */
  protected $method;

  /**
   * Whether the form should be secured or not
   *
   * @var boolean
   */
  protected $secure;

  /**
   * The form element
   *
   * @var string
   */
  protected $element = 'form';

  /**
   * A list of injected properties
   *
   * @var array
   */
  protected $injectedProperties = array('method', 'action');

  /**
   * Whether a form is opened or not
   *
   * @var boolean
   */
  protected static $opened = false;

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Build a new Form instance
   *
   * @param Former       $former
   * @param UrlGenerator $url
   */
  public function __construct(Former $former, $url, Populator $populator)
  {
    $this->former    = $former;
    $this->framework = $former->getFramework();
    $this->url       = $url;
    $this->populator = $populator;
  }

  /**
   * Opens up magically a form
   *
   * @param  string $type       The form type asked
   * @param  array  $parameters Parameters passed
   * @return string             A form opening tag
   */
  public function openForm($type, $parameters)
  {
    $action     = Arrays::get($parameters, 0);
    $method     = Arrays::get($parameters, 1, 'POST');
    $attributes = Arrays::get($parameters, 2, array());
    $secure     = Arrays::get($parameters, 3, false);

    // Fetch errors if asked for
    if ($this->former->getOption('fetch_errors')) {
      $this->former->withErrors();
    }

    // Open the form
    $this->action($action);
    $this->attributes = $attributes;
    $this->method     = strtoupper($method);
    $this->secure     = $secure;

    // Add any effect of the form type
    $this->type = $this->applyType($type);

    // Add enctype
    if (!array_key_exists('accept-charset', $attributes )) {
      $this->attributes['accept-charset'] = 'utf-8';
    }

    // Add supplementary classes
    $this->addClass($this->former->getFramework()->getFormClasses($this->type));

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
  public static function hasInstanceOpened()
  {
    return static::$opened;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////////// SETTER /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Change the form's action
   *
   * @param  string $action The new action
   */
  public function action($action)
  {
    $this->action = $action ? $this->url->to($action) : null;

    return $this;
  }

  /**
   * Change the form's method
   *
   * @param  string $method The method to use
   */
  public function method($method)
  {
    $this->method = strtoupper($method);

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
   * Outputs the current form opened
   *
   * @return string A <form> opening tag
   */
  public function __toString()
  {
    // Mark the form as opened
    static::$opened = true;

    // Add name to attributes
    $this->attributes['name'] = $this->name;

    // Add spoof method
    if ($this->method == 'PUT' or $this->method == 'DELETE') {
      $spoof = $this->former->hidden('_method', $this->method);
      $this->method = 'POST';
    } else $spoof = null;

    return $this->open().$spoof;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// PUBLIC HELPERS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Alias for $this->former->withRules
   */
  public function rules()
  {
    call_user_func_array(array($this->former, 'withRules'), func_get_args());

    return $this;
  }

  /**
   * Populate a form with specific values
   *
   * @param array|object $values
   */
  public function populate($values)
  {
    $this->populator->setValues($values);

    return $this;
  }

  /**
   * Get the Populator binded to the Form
   *
   * @return Populator
   */
  public function getPopulator()
  {
    return $this->populator;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Apply various parameters according to form type
   *
   * @param  string $type The original form type provided
   * @return string The final form type
   */
  private function applyType($type)
  {
    // If classic form
    if ($type == 'open') {
      return $this->former->getOption('default_form_type');
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
      $type = $this->former->getOption('default_form_type');
    }

    return $type;
  }

}
