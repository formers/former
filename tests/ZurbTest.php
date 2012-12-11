<?php
use \Former\Former;

class ZurbTest extends FormerTests
{
  public function setUp()
  {
    parent::setUp();
    $this->former->framework('ZurbFoundation');
  }

  public function testMagicMethods()
  {
    $text = $this->former->three_text('foo')->__toString();
    $matcher = '<div><label for="foo">Foo</label><input class="three" type="text" name="foo" id="foo"></div>';

    $this->assertEquals($matcher, $text);
  }

  public function testErrorState()
  {
    $text = $this->former->text('foo')->state('error')->__toString();
    $matcher = '<div class="error"><label for="foo">Foo</label><input type="text" name="foo" id="foo"></div>';

    $this->assertEquals($matcher, $text);
  }

  public function testHelp()
  {
    $text = $this->former->text('foo')->inlineHelp('bar')->__toString();
    $matcher = '<div><label for="foo">Foo</label><input type="text" name="foo" id="foo"><small>Bar</small></div>';

    $this->assertEquals($matcher, $text);
  }

  public function testCantUseBlockHelp()
  {
    $this->setExpectedException('BadMethodCallException');

    $this->former->text('foo')->blockHelp('bar')->__toString();
  }
}
