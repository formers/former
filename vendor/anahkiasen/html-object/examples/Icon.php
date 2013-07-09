<?php
use HtmlObject\Traits\Tag;

/**
 * new Icon('bookmark')
 * <i class="icon-bookmark"></i>
 *
 * Icon::bookmark()->white()
 * <i class="icon-bookmark icon-white"></i>
 */

/**
 * A classic Icon pattern
 */
class Icon extends Tag
{
  /**
   * The Icon's tag
   *
   * @var string
   */
  protected $element = 'i';

  /**
   * Create a new icon
   *
   * @param string $icon The icon
   */
  public function __construct($icon)
  {
    $this->class('icon-'.$icon);
  }

  /**
   * Static alias for constructor
   */
  public static function __callStatic($method, $parameters)
  {
    return new static($method);
  }

  /**
   * Make the Icon white
   */
  public function white()
  {
    $this->addClass('icon-white');
  }
}