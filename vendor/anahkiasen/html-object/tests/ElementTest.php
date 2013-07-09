<?php
include '_start.php';

use HtmlObject\Element;

class ElementTest extends HtmlObjectTests
{
  public function testCanDynamicallyCreateObjects()
  {
    $object = Element::p('foo')->class('bar');
    $matcher = $this->getMatcher();
    $matcher['attributes']['class'] = 'bar';

    $this->assertHTML($matcher, $object);
  }
}