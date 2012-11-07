<?php
/**
 * Button
 *
 * Button fields
 */
namespace Former\Fields;

use \Form;

class Button extends \Former\Field
{
  /**
   * Easier arguments order for button fields
   *
   * @param string $type       button/submit
   * @param string $value      Its value
   * @param array  $attributes Attributes
   */
  public function __construct($type, $value, $attributes)
  {
    $this->attributes = (array) $attributes;
    $this->type = $type;
    $this->value($value);
  }

  /**
   * Hijack Former's Object model value method
   *
   * @param  string $value The new button text
   */
  public function value($value)
  {
    $value = \Former\Helpers::translate($value);

    $this->value = $value;

    return $this;
  }

  /**
   * Renders the button
   *
   * @return string A form button
   */
  public function __toString()
  {
    $type = $this->type;

    return \Laravel\Form::$type($this->value, $this->attributes);
  }
}
