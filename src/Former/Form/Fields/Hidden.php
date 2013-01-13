<?php
/**
 * Hidden
 *
 * Class for hidden fields
 */
namespace Former\Form\Fields;

use \Former\Traits\Field;

class Hidden extends Field
{

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Easier arguments order for hidden fields
   *
   * @param string $type       hidden
   * @param string $name       Field names
   * @param string $value      Its value
   * @param array  $attributes Attributes
   */
  public function __construct($app, $type, $name, $value, $attributes)
  {
    parent::__construct($app, $type, $name, '', $value, $attributes);
  }

  /**
   * Outputs a hidden field
   *
   * @return string An <input type="hidden" />
   */
  public function render()
  {
    return $this->app['form']->hidden($this->name, $this->value, $this->attributes);
  }
}
