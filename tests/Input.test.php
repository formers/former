<?php
include 'start.php';

class InputTest extends FormerTests
{
  public function testText()
  {
    $input = Former::text('foo')->__toString();
    $matcher =
      '<div class="control-group">' .
        '<label for="foo" class="control-label">Foo</label>' .
        '<div class="controls">' .
          '<input type="text" name="foo">' .
        '</div>' .
      '</div>';

    $this->assertEquals($matcher, $input);
  }

  public function testTextLabel()
  {
    $static = Former::text('foo')->label('bar')->__toString();
    $input  = Former::text('foo', 'bar')->__toString();
    $matcher =
      '<div class="control-group">' .
        '<label for="foo" class="control-label">Bar</label>' .
        '<div class="controls">' .
          '<input type="text" name="foo">' .
        '</div>' .
      '</div>';

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testValue()
  {
    $static = Former::text('foo')->value('bar')->__toString();
    $input  = Former::text('foo', null, 'bar')->__toString();
    $matcher =
      '<div class="control-group">' .
        '<label for="foo" class="control-label">Foo</label>' .
        '<div class="controls">' .
          '<input type="text" name="foo" value="bar">' .
        '</div>' .
      '</div>';

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }
}