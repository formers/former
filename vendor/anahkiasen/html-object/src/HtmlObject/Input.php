<?php
namespace HtmlObject;

use HtmlObject\Traits\Helpers;
use HtmlObject\Traits\Tag;

/**
 * An input
 */
class Input extends Tag
{

  /**
   * The tag element
   *
   * @var string
   */
  protected $element = 'input';

  /**
   * Whether the element is self closing
   *
   * @var boolean
   */
  protected $isSelfClosing = true;

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// CORE METHODS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Create a new Input
   *
   * @param string $type       Its type
   * @param string $name       Its name
   * @param string $value      Its value
   * @param array  $attributes
   *
   * @return Input
   */
  public function __construct($type, $name = null, $value = null, $attributes = array())
  {
    $attributes['type'] = $type;
    $attributes['name'] = $name;

    $this->setTag('input', $value, $attributes);
  }

  /**
   * Create a new Input
   *
   * @param string $type       Its type
   * @param string $name       Its name
   * @param string $value      Its value
   * @param array  $attributes
   *
   * @return Input
   */
  public static function create($type, $name = null, $value = null, $attributes = array())
  {
    return new static($type, $name, $value, $attributes);
  }

  /**
   * Dynamically create an input type
   *
   * @param string $method     The input type
   * @param array  $parameters
   *
   * @return Input
   */
  public static function __callStatic($method, $parameters)
  {
    $name       = Helpers::arrayGet($parameters, 0);
    $value      = Helpers::arrayGet($parameters, 1);
    $attributes = Helpers::arrayGet($parameters, 2);

    return new static($method, $name, $value, $attributes);
  }

}
