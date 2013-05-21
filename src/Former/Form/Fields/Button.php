<?php
namespace Former\Form\Fields;

use Former\Former;
use Former\Helpers;
use Former\Traits\Field;

/**
 * Button fields
 */
class Button extends Field
{

  /**
   * The Illuminate Container
   *
   * @var Container
   */
  protected $former;

  /**
   * The Button default element
   *
   * @var string
   */
  protected $element = 'input';

  /**
   * Default value for self-closing
   *
   * @var boolean
   */
  protected $isSelfClosing = true;

  /**
   * A list of class properties to be added to attributes
   *
   * @var array
   */
  protected $injectedProperties = array(
    'name', 'type', 'value',
  );

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Easier arguments order for button fields
   *
   * @param Container $app        The Illuminate Container
   * @param string    $type       button/submit/reset/etc
   * @param string    $value      The text of the button
   * @param string    $link       Its link
   * @param array     $attributes Its attributes
   */
  public function __construct(Former $former, $type, $value, $link, $attributes)
  {
    $this->former        = $former;

    $this->attributes = (array) $attributes;
    $this->type       = $type;
    $this->value($value);

    // Set correct element for the various button patterns
    switch ($type) {
      case 'button':
        $this->element = 'button';
        $this->isSelfClosing = false;
        break;
      case 'link':
        $this->element = 'a';
        $this->attributes['href'] = $link;
        $this->isSelfClosing = false;
        break;
    }
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
    $this->value = Helpers::translate($value);

    return $this;
  }

}
