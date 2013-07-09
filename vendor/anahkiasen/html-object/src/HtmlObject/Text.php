<?php
namespace HtmlObject;

use HtmlObject\Traits\Tag;

/**
 * A TextNode
 */
class Text extends Tag
{

  /**
   * Create a TextNode
   *
   * @param string $value
   */
  public function __construct($value = null)
  {
    $this->value = $value;
  }

  /**
   * Static alias for constructor
   *
   * @param string $value The text value
   *
   * @return Text
   */
  public static function create($value = null)
  {
    return new static($value);
  }

  /**
   * Render a TextNode
   *
   * @return string
   */
  public function render()
  {
    return $this->value;
  }

}