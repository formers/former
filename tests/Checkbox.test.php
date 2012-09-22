<?php
use \Former\Former;

include 'start.php';

class CheckboxTest extends FormerTests
{
  private function cb($name = 'foo', $label = null, $value = 1)
  {
    return '<label class="checkbox"><input id="foo" type="checkbox" name="' .$name. '" value="' .$value. '">' .$label. '</label>';
  }

  private function cx($name = 'foo', $label = null, $value = 1, $inline = false)
  {
    $inline = $inline ? ' inline' : null;

    return '<label class="checkbox' .$inline. '"><input type="checkbox" name="' .$name. '" value="' .$value. '">' .$label. '</label>';
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

  public function testSingleWithValue()
  {
    $checkbox = Former::checkbox('foo')->text('bar')->value('foo')->__toString();
    $matcher = $this->cg($this->cb('foo', 'Bar', 'foo'));

    $this->assertEquals($matcher, $checkbox);
  }

  public function testMultiple()
  {
    $checkboxes = Former::checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cg($this->cx('foo_0', 'Foo').$this->cx('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testInline()
  {
    $checkboxes1 = Former::inline_checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $checkboxes2 = Former::checkboxes('foo')->inline()->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cg($this->cx('foo_0', 'Foo', 1, true).$this->cx('foo_1', 'Bar', 1, true));

    $this->assertEquals($matcher, $checkboxes1);
    $this->assertEquals($matcher, $checkboxes2);
  }

  public function testStacked()
  {
    $checkboxes1 = Former::stacked_checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $checkboxes2 = Former::checkboxes('foo')->stacked()->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cg($this->cx('foo_0', 'Foo', 1).$this->cx('foo_1', 'Bar', 1));

    $this->assertEquals($matcher, $checkboxes1);
    $this->assertEquals($matcher, $checkboxes2);
  }

  public function testMultipleArray()
  {
    $checkboxes = Former::checkboxes('foo')->checkboxes(array('Foo' => 'foo', 'Bar' => 'bar'))->__toString();
    $matcher = $this->cg($this->cb('foo', 'Foo').$this->cx('bar', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testMultipleCustom()
  {
    $radios = Former::checkboxes('foo')->checkboxes($this->checkables)->__toString();
    $matcher = $this->cg(
    '<label class="checkbox">'.
      '<input data-foo="bar" value="bar" id="foo" type="checkbox" name="foo">'.
      'Foo'.
    '</label>'.
    '<label class="checkbox">'.
      '<input data-foo="bar" value="bar" id="bar" type="checkbox" name="foo">'.
      'Bar'.
    '</label>');

    $this->assertEquals($matcher, $radios);
  }

  public function testMultipleCustomNoName()
  {
    $checkables = $this->checkables;
    unset($checkables['Foo']['name']);
    unset($checkables['Bar']['name']);

    $radios = Former::checkboxes('foo')->checkboxes($checkables)->__toString();
    $matcher = $this->cg(
    '<label class="checkbox">'.
      '<input data-foo="bar" value="bar" type="checkbox" name="foo_0">'.
      'Foo'.
    '</label>'.
    '<label class="checkbox">'.
      '<input data-foo="bar" value="bar" id="bar" type="checkbox" name="foo_1">'.
      'Bar'.
    '</label>');

    $this->assertEquals($matcher, $radios);
  }
}