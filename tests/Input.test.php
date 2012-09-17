<?php
include 'start.php';

class InputTest extends FormerTests
{
  private function cg($label, $input)
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  public function testText()
  {
    $input = Former::text('foo')->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="control-label">Foo</label>',
      '<input type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
  }

  public function testTextLabel()
  {
    $static = Former::text('foo')->label('bar')->__toString();
    $input  = Former::text('foo', 'bar')->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="control-label">Bar</label>',
      '<input type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testValue()
  {
    $static = Former::text('foo')->value('bar')->__toString();
    $input  = Former::text('foo', null, 'bar')->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="control-label">Foo</label>',
      '<input type="text" name="foo" value="bar" id="foo">');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testMagicAttribute()
  {
    $static = Former::text('foo')->class('foo')->bar('bar')->__toString();
    $input  = Former::text('foo', null, null, array('class' => 'foo', 'bar' => 'bar'))->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="control-label">Foo</label>',
      '<input class="foo" bar="bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testAddClass()
  {
    $static = Former::text('foo')->class('foo')->addClass('bar')->__toString();
    $input  = Former::text('foo', null, null, array('class' => 'foo'))->addClass('bar')->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="control-label">Foo</label>',
      '<input class="foo bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }
}