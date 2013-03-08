<?php
/**
 * Actions
 *
 * Handles the actions part of a form
 * Submit buttons, and such
 */
namespace Former\Form;

use Former\Traits\FormerObject;
use Underscore\Types\Arrays;
use Underscore\Types\String;

class Actions extends FormerObject
{
  /**
   * The current environment
   *
   * @var Illuminate\Container
   */
  protected $app;

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
  public function __construct($app, $value)
  {
    $this->app   = $app;
    $this->value = $value;

    // Add specific actions classes to the actions block
    $this->addClass($this->app['former']->getFramework()->getActionClasses());
  }

  /**
   * Get the content of the Actions block
   *
   * @return string
   */
  public function getContent()
  {
    return Arrays::from($this->value)->each(function($content) {
      return method_exists($content, '__toString') ? (string) $content : $content;
    })->implode(' ');
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
    if ($this->isButton($method)) {
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
    $this->value[] = $this->app['former']->$type($name, $link, $attributes)->__toString();

    return $this;
  }

  /**
   * Check if a given method calls a button or not
   *
   * @param string $method The method to check
   *
   * @return boolean
   */
  private function isButton($method)
  {
    $buttons = array('button', 'submit', 'link', 'reset');

    return (bool) String::find($method, $buttons);
  }
}
