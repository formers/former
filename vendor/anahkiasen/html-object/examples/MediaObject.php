<?php
use HtmlObject\Element;
use HtmlObject\Image;
use HtmlObject\Traits\Tag;

/**
 * MediaObject::create('image.jpg', 'John Doe', 'My name is John Doe')
 *
 * <article class="media">
 *   <figure class="media-object">
 *     <img src="image.jpg">
 *   </figure>
 *   <div class="media-body">
 *     <h2 class="media-heading">John Doe</h2>
 *     My Name is John Doe
 *   </div>
 * </article>
 */

/**
 * A Twitter Bootstrap media object
 */
class MediaObject extends Tag
{
  protected $element = 'article';

  /**
   * Build a new Media Object
   *
   * @param string $image   Image URL
   * @param string $title   Title
   * @param string $content Content
   */
  public function __construct($image, $title, $content)
  {
    $this->addClass('media');

    $image   = Image::create($image);
    $figure  = Element::figure($image)->class('media-object');

    $body    = Element::div()->class('media-body');
    $title   = Element::h2($title)->class('media-heading');

    $this->nest(array(
      'figure' => $figure,
      'body'   => $body->nest(array(
        'title'   => $title,
        'content' => $content,
      )),
    ));
  }

  /**
   * Static alias for constructor
   */
  public static function create($image, $title, $content)
  {
    return new static($image, $title, $content);
  }
}
