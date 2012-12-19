<?php
/**
 * Actions
 *
 * Handles the actions part of a form
 * Submit buttons, and such
 */
namespace Former\Form;

use \Former\Traits\FormerObject;

class Actions extends FormerObject
{
  private $app;

  /**
   * Constructs a new Actions block
   *
   * @param Container $app
   * @param array     $content The block content
   */
  public function __construct($app, $content)
  {
    $this->app = $app;
    $this->content = $content;
  }

  /**
   * Render the actions block
   *
   * @return string
   */
  public function __toString()
  {
    $actions  = '<div class="form-actions">';
      $actions .= implode(' ', (array) $this->content);
    $actions .= '</div>';

    return $actions;
  }
}