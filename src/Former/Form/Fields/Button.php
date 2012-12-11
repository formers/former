<?php
/**
 * Button
 *
 * Button fields
 */
namespace Former\Form\Fields;

use \Form;

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
  public function __construct($app, $type, $value, $attributes)
  {
    $this->app = $app;
    $this->attributes = (array) $attributes;
    $this->type = $type;
    $this->value($value);
  }

  /**
   * Renders the button
   *
   * @return string A form button
   */
  public function render()
  {
    $type = $this->type;

    return $this->app['former.laravel.form']->$type($this->value, $this->attributes);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// FIELD METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Hijack Former's Object model value method
   *
   * @param  string $value The new button text
   */
  public function value($value)
  {
    $value = $this->app['former.helpers']->translate($value);

    $this->value = $value;

    return $this;
  }
}
