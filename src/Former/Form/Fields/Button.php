<?php
namespace Former\Form\Fields;

use Former\Former;
use Former\Helpers;
use Former\Traits\Field;
use Illuminate\Container\Container;

/**
 * Button fields
 */
class Button extends Field
{
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
   * @param Container $app        The Container
   * @param string    $type       button/submit/reset/etc
   * @param string    $value      The text of the button
   * @param string    $link       Its link
   * @param array     $attributes Its attributes
   */
  public function __construct(Container $app, $type, $value, $link, $attributes)
  {
    $this->app        = $app;
    $this->attributes = (array) $attributes;
    $this->type       = $type;
    $this->value($value);

    // Set correct element for the various button patterns
    switch ($type) {
      case 'button':
        $this->element       = 'button';
        $this->isSelfClosing = false;
        break;
      case 'link':
        $this->type               = null;
        $this->element            = 'a';
        $this->attributes['href'] = $link;
        $this->isSelfClosing      = false;
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
   * Prepend an icon to the button
   *
   * @param  string $icon
   * @param  array  $attributes
   *
   * @return self
   */
  public function icon($icon, $attributes = array())
  {
    $icon = $this->app['former.framework']->createIcon($icon, $attributes);
    $this->value = $icon. ' ' .$this->value;

    return $this;
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
