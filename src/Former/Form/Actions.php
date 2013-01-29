<?php
/**
 * Actions
 *
 * Handles the actions part of a form
 * Submit buttons, and such
 */
namespace Former\Form;

use \Former\Traits\FormerObject;
use \Underscore\Types\String;
use \Underscore\Types\Arrays;

class Actions extends FormerObject
{
  /**
   * The current environment
   * @var Illuminate\Container
   */
  protected $app;

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Constructs a new Actions block
   *
   * @param Container $app
   * @param array     $content The block content
   */
  public function __construct($app, $content)
  {
    $this->app     = $app;
    $this->content = $content;
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

  /**
   * Render the actions block
   *
   * @return string
   */
  public function __toString()
  {
    // Add specific actions classes to the actions block
    $this->attributes = $this->app['former']->getFramework()->addActionClasses($this->attributes);

    // Render passed objects
    $this->content = Arrays::each($this->content, function($content) {
      if (method_exists($content, '__toString')) return $content->__toString();
      else return $content;
    });

    // Render block
    $actions  = '<div' .$this->app['html']->attributes($this->attributes). '>';
      $actions .= implode(' ', (array) $this->content);
    $actions .= '</div>';

    return $actions;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Create a new Button and add it to the actions
   *
   * @param string $type The button type
   * @param string $name Its name and label
   *
   * @return Actions
   */
  private function createButtonOfType($type, $name, $link, $attributes)
  {
    $this->content[] = $this->app['former']->$type($name, $link, $attributes)->__toString();

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

    return String::find($method, $buttons) ? true : false;
  }
}
