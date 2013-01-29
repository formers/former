<?php
/**
 * Button
 *
 * Button fields
 */
namespace Former\Form\Fields;

use \Form;
use \Former\Helpers;

class Button extends \Former\Traits\Field
{
  protected $app;

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Easier arguments order for button fields
   *
   * @param string $type       button/submit
   * @param string $value      Its value
   * @param array  $attributes Attributes
   */
  public function __construct($app, $type, $value, $link, $attributes)
  {
    $this->app        = $app;
    $this->attributes = (array) $attributes;
    $this->type       = $type;
    $this->value($value);

    // Add href to attributes if link
    if ($this->type == 'link') {
      $this->link = $link;
    }
  }

  /**
   * Renders the button
   *
   * @return string A form button
   */
  public function render()
  {
    $type = $this->type;

    // Link buttons
    if ($type == 'link') {
      return $this->app['html']->to($this->link, $this->value, $this->attributes);
    }

    return $this->app['form']->$type($this->value, $this->attributes);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// FIELD METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Check if the field is a button
   *
   * @return boolean
   */
  public function isButton()
  {
    return true;
  }

  /**
   * Hijack Former's Object model value method
   *
   * @param  string $value The new button text
   */
  public function value($value)
  {
    $value = Helpers::translate($value);

    $this->value = $value;

    return $this;
  }
}
