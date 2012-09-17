<?php
include 'start.php';

class InputTest extends PHPUnit_Framework_TestCase
{
  public function testInput()
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
}