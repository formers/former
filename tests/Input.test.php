<?php
use \Former\Former;

class InputTest extends FormerTests
{
  public function tearDown()
  {
    Former::useBootstrap(true);
  }

  public function testText()
  {
    $input = Former::text('foo')->__toString();
    $matcher = $this->cg('<input type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
  }

  public function testTextWithoutBootstrap()
  {
    Former::useBootstrap(false);

    $input = Former::text('foo')->data('foo')->class('bar')->__toString();
    $matcher = '<label for="foo">Foo</label><input data="foo" class="bar" type="text" name="foo" id="foo">';

    $this->assertEquals($matcher, $input);
  }

  public function testTextWithoutFormInstance()
  {
    Former::close();

    $input = Former::text('foo')->data('foo')->class('bar')->__toString();
    $matcher = '<label for="foo">Foo</label><input data="foo" class="bar" type="text" name="foo" id="foo">';

    $this->assertEquals($matcher, $input);

    Former::horizontal_open();
  }

  public function testHiddenField()
  {
    $input = Former::hidden('foo')->value('bar')->__toString();
    $matcher = '<input type="hidden" name="foo" value="bar" id="foo">';

    $this->assertEquals($matcher, $input);
  }

  public function testTextLabel()
  {
    $static = Former::text('foo')->label('bar')->__toString();
    $input  = Former::text('foo', 'bar')->__toString();
    $matcher = $this->cg(
      '<input type="text" name="foo" id="foo">',
      '<label for="foo" class="control-label">Bar</label>');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testTextLabelWithoutBootstrap()
  {
    Former::useBootstrap(false);

    $static = Former::text('foo')->label('bar')->__toString();
    $input  = Former::text('foo', 'bar')->__toString();
    $matcher = '<label for="foo">Bar</label><input type="text" name="foo" id="foo">';

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testValue()
  {
    $static = Former::text('foo')->value('bar')->__toString();
    $input  = Former::text('foo', null, 'bar')->__toString();
    $matcher = $this->cg('<input type="text" name="foo" value="bar" id="foo">');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testMagicAttribute()
  {
    $static = Former::text('foo')->class('foo')->data_bar('bar')->__toString();
    $input  = Former::text('foo', null, null, array('class' => 'foo', 'data-bar' => 'bar'))->__toString();
    $matcher = $this->cg('<input class="foo" data-bar="bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testMagicAttributeUnvalue()
  {
    $static = Former::text('foo')->require()->__toString();
    $matcher = $this->cg('<input require="true" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $static);
  }

  public function testSetAttributes()
  {
    $attributes = array('class' => 'foo', 'data-foo' => 'bar');

    $static = Former::text('foo')->require()->setAttributes($attributes)->__toString();
    $matcher = $this->cg('<input require="true" class="foo" data-foo="bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $static);
  }

  public function testSetAttributesOverwrite()
  {
    $attributes = array('class' => 'foo', 'data-foo' => 'bar');

    $static = Former::text('foo')->require()->setAttributes($attributes, false)->__toString();
    $matcher = $this->cg('<input class="foo" data-foo="bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $static);
  }

  public function testGetAttribute()
  {
    $former = Former::span1_text('name')->foo('bar');

    $this->assertEquals('span1', $former->class);
    $this->assertEquals('bar', $former->foo);
  }

  public function testAddClass()
  {
    $static = Former::text('foo')->class('foo')->addClass('bar')->__toString();
    $input  = Former::text('foo', null, null, array('class' => 'foo'))->addClass('bar')->__toString();
    $matcher = $this->cg('<input class="foo bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testMagicMethods()
  {
    foreach(array('mini', 'small', 'medium', 'large', 'xlarge', 'xxlarge') as $size) {
      $method = $size.'_text';
      $static = Former::$method('foo')->addClass('bar')->__toString();
      $matcher = $this->cg('<input class="input-' .$size. ' bar" type="text" name="foo" id="foo">');

      $this->assertEquals($matcher, $static);
    }
  }
}