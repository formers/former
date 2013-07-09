<?php
namespace HtmlObject;

use HtmlObject\Traits\Helpers;
use HtmlObject\Traits\Tag;

/**
 * A classic HTML element
 */
class Element extends Tag
{

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// CORE METHODS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Creates a basic Element
   *
   * @param string $element
   * @param string $value
   * @param array  $attributes
   *
   * @return Element
   */
  public function __construct($element = null, $value = null, $attributes = array())
  {
    $this->setTag($element, $value, $attributes);
  }

  /**
   * Static alias for constructor
   *
   * @param string          $element
   * @param string|null|Tag $value
   * @param array           $attributes
   * @return                Element
   */
  public static function create($element = null, $value = null, $attributes = array())
  {
    return new static($element, $value, $attributes);
  }

  /**
   * Dynamically create an element
   *
   * @param string $method     The element
   * @param array  $parameters Value and attributes
   *
   * @return Element
   */
  public static function __callStatic($method, $parameters)
  {
    $value      = Helpers::arrayGet($parameters, 0);
    $attributes = Helpers::arrayGet($parameters, 1);

    return new static($method, $value, $attributes);
  }

}
