<?php
namespace Former;

use \Laravel\File;

class LiveValidation
{
  /**
   * The field being worked on
   * @var Field
   */
  private $field;

  /**
   * Apply live validation to a field
   *
   * @param Field $field The field
   * @param array $rules The rules to apply
   */
  public function __construct(&$field, $rules)
  {
    // If no rules to apply, cancel
    if (!$rules) return false;

    // Store field
    $this->field = $field;

    // Apply the rules
    foreach ($rules as $rule => $parameters) {
      if (!method_exists($this, $rule)) continue;

      $this->$rule($parameters);
    }
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// RULES /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  // Field types --------------------------------------------------- /

  /**
   * Email field
   */
  public function email()
  {
    $this->field->setType('email');
  }

  /**
   * URL field
   */
  public function url()
  {
    $this->field->setType('url');
  }

  /**
   * Required field
   */
  public function required()
  {
    $this->field->required();
  }

  // Patterns ------------------------------------------------------ /

  /**
   * Integer field
   */
  public function integer()
  {
    $this->field->pattern('\d+');
  }

  /**
   * Numeric field
   */
  public function numeric()
  {
    if ($this->field->getType() == 'number') $this->field->step('any');
    else $this->field->pattern('[+-]?\d*\.?\d+');
  }

  /**
   * Not numeric field
   */
  public function not_numeric()
  {
    $this->field->pattern('\D+');
  }

  /**
   * Only alphanumerical

   */
  public function alpha()
  {
    $this->field->pattern('[a-zA-Z]+');
  }

  /**
   * Only alphanumerical and numbers
   */
  public function alpha_num()
  {
    $this->field->pattern('[a-zA-Z0-9]+');
  }

  /**
   * Alphanumerical, numbers and dashes
   */
  public function alpha_dash()
  {
    $this->field->pattern('[a-zA-Z0-9_\-]+');
  }

  /**
   * In []
   */
  public function in($possible)
  {
    $possible = (sizeof($possible) == 1) ? $possible[0] : '('.join('|', $possible).')';
    $this->field->pattern('^' .$possible. '$');
  }

  /**
   * Not in []
   */
  public function not_in($impossible)
  {
    $this->field->pattern('(?:(?!^' .join('$|^', $impossible). '$).)*');
  }

  /**
   * Matches a pattern
   */
  public function match($pattern)
  {
    $this->field->pattern(substr($pattern[0], 1, -1));
  }

  // Boundaries ---------------------------------------------------- /

  /**
   * Max value
   */
  public function max($max)
  {
    $this->setMax($max[0]);
  }

  /**
   * Min value
   */
  public function min($min)
  {
    $this->setMin($min[0]);
  }

  /**
   * Set boundaries
   */
  public function between($between)
  {
    list($min, $max) = $between;

    $this->setMin($min);
    $this->setMax($max);
  }

  /**
   * Set accepted mime types
   */
  public function mimes($mimes)
  {
    if ($this->field->type != 'file') return false;

    $this->field->accept($this->setAccepted($mimes));
  }

  /**
   * Set accept only images
   */
  public function image()
  {
    $this->mimes(array('jpg', 'png', 'gif', 'bmp'));
  }

  // Dates --------------------------------------------------------- /

  /**
   * Before a date
   */
  public function before($date)
  {
    list($format, $date) = $this->formatDate($date[0]);
    $this->field->max(date($format, $date));
  }

  /**
   * After a date
   */
  public function after($date)
  {
    list($format, $date) = $this->formatDate($date[0]);
    $this->field->min(date($format, $date));
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set the accepted MIMEs
   *
   * @param array $mimes An array of mimes
   */
  private function setAccepted($mimes)
  {
    $mimes = array_map(array('\Laravel\File', 'mime'), $mimes);

    return implode(',', $mimes);
  }

  /**
   * Format a date to a pattern
   *
   * @param  string $date The date
   * @return string The pattern
   */
  private function formatDate($date)
  {
    $format = 'Y-m-d';

    // Datetime fields
    if ($this->field->getType() == 'datetime' or
      $this->field->getType() == 'datetime-local') {
        $format .= '\TH:i:s';
    }

    return array($format, strtotime($date));
  }

  /**
   * Set a maximum value to a field
   *
   * @param integer $max
   */
  private function setMax($max)
  {
    $attribute = $this->field->getType() == 'number' ? 'max' : 'maxlength';
    $this->field->$attribute($max);
  }

  /**
   * Set a minimum value to a field
   *
   * @param integer $min
   */
  private function setMin($min)
  {
    $attribute = $this->field->getType() == 'number' ? 'min' : 'minlength';
    $this->field->$attribute($min);
  }
}
