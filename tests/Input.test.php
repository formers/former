<?php
include 'start.php';

class InputTest extends FormerTests
{
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
    $static = Former::text('foo')->class('foo')->data_bar('bar')->__toString();
    $input  = Former::text('foo', null, null, array('class' => 'foo', 'data-bar' => 'bar'))->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="control-label">Foo</label>',
      '<input class="foo" data-bar="bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testMagicAttributeUnvalue()
  {
    $static = Former::text('foo')->require()->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="control-label">Foo</label>',
      '<input require="true" type="text" name="foo" id="foo">');

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

  public function testMagicMethods()
  {
    foreach(array('mini', 'small', 'medium', 'large', 'xlarge', 'xxlarge') as $size) {
      $method = $size.'_text';
      $static = Former::$method('foo')->addClass('bar')->__toString();
      $matcher = $this->cg(
        '<label for="foo" class="control-label">Foo</label>',
        '<input class="input-' .$size. ' bar" type="text" name="foo" id="foo">');

      $this->assertEquals($matcher, $static);
    }
  }
}