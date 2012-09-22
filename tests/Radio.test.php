<?php
use \Former\Former;

class RadioTest extends FormerTests
{
  private function r($name = 'foo', $label = null, $value = 1, $inline = false)
  {
    $inline = $inline ? ' inline' : null;

    return '<label class="radio' .$inline. '"><input id="foo" type="radio" name="' .$name. '" value="' .$value. '">' .$label. '</label>';
  }

  private function rx($name = 'foo', $label = null, $value = 1)
  {
    return '<label class="radio"><input type="radio" name="' .$name. '" value="' .$value. '">' .$label. '</label>';
  }

  public function testSingle()
  {
    $radio = Former::radio('foo')->__toString();
    $matcher = $this->cg($this->r());

    $this->assertEquals($matcher, $radio);
  }

  public function testSingleWithLabel()
  {
    $radio = Former::radio('foo')->text('bar')->__toString();
    $matcher = $this->cg($this->r('foo', 'Bar'));

    $this->assertEquals($matcher, $radio);
  }

  public function testSingleWithValue()
  {
    $radio = Former::radio('foo')->text('bar')->value('foo')->__toString();
    $matcher = $this->cg($this->r('foo', 'Bar', 'foo'));

    $this->assertEquals($matcher, $radio);
  }

  public function testMultiple()
  {
    $radios = Former::radios('foo')->radios('foo', 'bar')->__toString();
    $matcher = $this->cg($this->r('foo', 'Foo', 0).$this->r('foo', 'Bar'));

    $this->assertEquals($matcher, $radios);
  }

  public function testInline()
  {
    $radios1 = Former::inline_radios('foo')->radios('foo', 'bar')->__toString();
    $radios2 = Former::radios('foo')->inline()->radios('foo', 'bar')->__toString();
    $matcher = $this->cg($this->r('foo', 'Foo', 0, true).$this->r('foo', 'Bar', 1, true));

    $this->assertEquals($matcher, $radios1);
    $this->assertEquals($matcher, $radios2);
  }

  public function testStacked()
  {
    $radios1 = Former::stacked_radios('foo')->radios('foo', 'bar')->__toString();
    $radios2 = Former::radios('foo')->stacked()->radios('foo', 'bar')->__toString();
    $matcher = $this->cg($this->r('foo', 'Foo', 0).$this->r('foo', 'Bar', 1));

    $this->assertEquals($matcher, $radios1);
    $this->assertEquals($matcher, $radios2);
  }

  public function testMultipleArray()
  {
    $radios = Former::radios('foo')->radios(array('Foo' => 'foo', 'Bar' => 'bar'))->__toString();
    $matcher = $this->cg($this->r('foo', 'Foo', 0).$this->rx('bar', 'Bar'));

    $this->assertEquals($matcher, $radios);
  }

  public function testMultipleCustom()
  {
    $radios = Former::radios('foo')->radios($this->checkables)->__toString();
    $matcher = $this->cg(
    '<label class="radio">'.
      '<input data-foo="bar" value="bar" id="foo" type="radio" name="foo">'.
      'Foo'.
    '</label>'.
    '<label class="radio">'.
      '<input data-foo="bar" value="bar" id="bar" type="radio" name="foo">'.
      'Bar'.
    '</label>');

    $this->assertEquals($matcher, $radios);
  }
}