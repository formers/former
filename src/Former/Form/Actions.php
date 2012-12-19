<?php
/**
 * Actions
 *
 * Handles the actions part of a form
 * Submit buttons, and such
 */
namespace Former\Form;

use \Former\Traits\FormerObject;
use \Underscore\String;

class Actions extends FormerObject
{
  protected $app;

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
      $button = $this->app['former']->$method($parameters[0])->__toString();
      $this->content[] = $button;

      return $this;
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
    $this->attributes = $this->app['former.framework']->addActionClasses($this->attributes);

    $actions  = '<div' .$this->app['former.helpers']->attributes($this->attributes). '>';
      $actions .= implode(' ', (array) $this->content);
    $actions .= '</div>';

    return $actions;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

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