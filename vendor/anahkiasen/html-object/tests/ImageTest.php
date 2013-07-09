<?php
use HtmlObject\Image;

class ImageTest extends HtmlObjectTests
{
  public function testCanCreateList()
  {
    $image = Image::create('foo.jpg', 'foo');

    $matcher = $this->getMatcher('img', null, array(
      'src' => 'foo.jpg',
      'alt' => 'foo',
    ));

    $this->assertHTML($matcher, $image);
  }
}