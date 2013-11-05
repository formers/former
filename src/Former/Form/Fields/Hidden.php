<?php
namespace Former\Form\Fields;

use Former\Former;
use Former\Traits\Field;
use HtmlObject\Input as HtmlInput;
use Illuminate\Container\Container;

/**
 * Class for hidden fields
 */
class Hidden extends Field
{

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Easier arguments order for hidden fields
   *
   * @param Container $app        The Container
   * @param string    $type       hidden
   * @param string    $name       Field names
   * @param string    $value      Its value
   * @param array     $attributes Attributes
   */
  public function __construct(Container $app, $type, $name, $value, $attributes)
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
    return HtmlInput::create('hidden', $this->name, $this->value, $this->attributes)->render();
  }

}
