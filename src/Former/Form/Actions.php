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
      return $this->createButtonOfType($method, $parameters[0]);
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
    $this->attributes = $this->app['former.framework']->addActionClasses($this->attributes);

    // Render block
    $actions  = '<div' .$this->app['former.helpers']->attributes($this->attributes). '>';
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
  private function createButtonOfType($type, $name)
  {
    $this->content[] = $this->app['former']->$type($name)->__toString();

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
    if (String::find($method, $buttons)) return true;

    return false;
  }
}

