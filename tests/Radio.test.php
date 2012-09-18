<?php
class RadioTest extends FormerTests
{
  private function r($name = 'foo', $label = null, $value = 1)
  {
    return '<label class="radio"><input id="foo" type="radio" name="' .$name. '" value="' .$value. '">' .$label. '</label>';
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

  public function testMultiple()
  {
    $radios = Former::radios('foo')->radios('foo', 'bar')->__toString();
    $matcher = $this->cg($this->r('foo', 'Foo', 0).$this->r('foo', 'Bar'));

    $this->assertEquals($matcher, $radios);
  }

  public function testMultipleCustom()
  {
    $radios = Former::radios('foo')->radios(array('foo' => 'Foo', 'bar' => 'Bar'))->__toString();
    $matcher = $this->cg($this->r('foo', 'Foo', 0).$this->rx('bar', 'Bar'));

    $this->assertEquals($matcher, $radios);
  }
}