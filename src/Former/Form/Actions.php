<?php
namespace Former\Form;

use Former\Former;
use Former\Traits\FormerObject;
use Underscore\Methods\ArraysMethods as Arrays;
use Underscore\Methods\StringMethods as String;

/**
 * Handles the actions part of a form
 * Submit buttons, and such
 */
class Actions extends FormerObject
{

  /**
   * The current environment
   *
   * @var Illuminate\Container
   */
  protected $former;

  /**
   * The Actions element
   *
   * @var string
   */
  protected $element = 'div';

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Constructs a new Actions block
   *
   * @param Container $app
   * @param array     $value The block content
   */
  public function __construct(Former $former, $value)
  {
    $this->former = $former;
    $this->value  = $value;

    // Add specific actions classes to the actions block
    $this->addClass($this->former->getFramework()->getActionClasses());
  }

  /**
   * Get the content of the Actions block
   *
   * @return string
   */
  public function getContent()
  {
    $content = Arrays::each($this->value, function($content) {
      return method_exists($content, '__toString') ? (string) $content : $content;
    });

    return implode(' ', $content);
  }

  /**
   * Dynamically append actions to the block
   *
   * @param string $method     The method
   * @param array  $parameters Its parameters
   *
   * @return Actions
   */
  public function __call($method, $parameters)
  {
    // Dynamically add buttons to an actions block
    if ($this->isButtonMethod($method)) {
      $text       = Arrays::get($parameters, 0);
      $link       = Arrays::get($parameters, 1);
      $attributes = Arrays::get($parameters, 2);
      if (!$attributes and is_array($link)) $attributes = $link;
      return $this->createButtonOfType($method, $text, $link, $attributes);
    }

    return parent::__call($method, $parameters);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Create a new Button and add it to the actions
   *
   * @param string $type       The button type
   * @param string $name       Its name
   * @param string $link       A link to point to
   * @param array  $attributes Its attributes
   *
   * @return Actions
   */
  private function createButtonOfType($type, $name, $link, $attributes)
  {
    $this->value[] = $this->former->$type($name, $link, $attributes)->__toString();

    return $this;
  }

  /**
   * Check if a given method calls a button or not
   *
   * @param string $method The method to check
   *
   * @return boolean
   */
  private function isButtonMethod($method)
  {
    $buttons = array('button', 'submit', 'link', 'reset');

    return (bool) String::find($method, $buttons);
  }

}
