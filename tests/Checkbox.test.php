<?php
include 'start.php';

class CheckboxTest extends FormerTests
{
  private function cb($name = 'foo', $label = null, $value = 1)
  {
    return '<label class="checkbox"><input id="foo" type="checkbox" name="' .$name. '" value="' .$value. '">' .$label. '</label>';
  }

  private function cx($name = 'foo', $label = null, $value = 1)
  {
    return '<label class="checkbox"><input type="checkbox" name="' .$name. '" value="' .$value. '">' .$label. '</label>';
  }

  public function testSingle()
  {
    $checkbox = Former::checkbox('foo')->__toString();
    $matcher = $this->cg($this->cb());

    $this->assertEquals($matcher, $checkbox);
  }

  public function testSingleWithLabel()
  {
    $checkbox = Former::checkbox('foo')->text('bar')->__toString();
    $matcher = $this->cg($this->cb('foo', 'Bar'));

    $this->assertEquals($matcher, $checkbox);
  }

  public function testMultiple()
  {
    $checkboxes = Former::checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cg($this->cx('foo_0', 'Foo').$this->cx('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testMultipleCustom()
  {
    $checkboxes = Former::checkboxes('foo')->checkboxes(array('foo' => 'Foo', 'bar' => 'Bar'))->__toString();
    $matcher = $this->cg($this->cb('foo', 'Foo').$this->cx('bar', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }
}