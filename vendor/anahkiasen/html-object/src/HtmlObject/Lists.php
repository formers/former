<?php
namespace HtmlObject;

/**
 * A list element (ul, ol, etc.)
 */
class Lists extends Element
{

  /**
   * Default element
   *
   * @var string
   */
  protected $element = 'ul';

  /**
   * Default element for nested children
   *
   * @var string
   */
  protected $defaultChild = 'li';

}
