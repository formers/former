<?php
use \Former\Former;

class ZurbTest extends FormerTests
{
  public function setUp()
  {
    parent::setUp();
    Former::framework('zurb');
  }

  public function testMagicMethods()
  {
    $text = Former::three_text('foo')->__toString();
    $matcher = '<div><label for="foo">Foo</label><input class="three" type="text" name="foo" id="foo"></div>';

    $this->assertEquals($matcher, $text);
  }

  public function testErrorState()
  {
    $text = Former::text('foo')->state('error')->__toString();
    $matcher = '<div class="error"><label for="foo">Foo</label><input type="text" name="foo" id="foo"></div>';

    $this->assertEquals($matcher, $text);
  }

  public function testHelp()
  {
    $text1 = Former::text('foo')->inlineHelp('bar')->__toString();
    $text2 = Former::text('foo')->blockHelp('bar')->__toString();
    $matcher = '<div><label for="foo">Foo</label><input type="text" name="foo" id="foo"><small>Bar</small></div>';

    $this->assertEquals($matcher, $text1);
    $this->assertEquals($matcher, $text2);
  }
}
