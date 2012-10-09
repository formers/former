<?php
use \Former\Former;

include 'start.php';

class CheckboxTest extends FormerTests
{
  private function cb($name = 'foo', $label = null, $value = 1, $inline = false)
  {
    $inline = $inline ? ' inline' : null;
    $checkbox = '<input id="' .$name. '" type="checkbox" name="' .$name. '" value="' .$value. '">';

    return $label ? '<label for="' .$name. '" class="checkbox' .$inline. '">' .$checkbox.$label. '</label>' : $checkbox;
  }

  private function cbc($name = 'foo', $label = null, $value = 1, $inline = false)
  {
    $inline = $inline ? ' inline' : null;
    $checkbox = '<input checked="checked" id="' .$name. '" type="checkbox" name="' .$name. '" value="' .$value. '">';

    return $label ? '<label for="' .$name. '" class="checkbox' .$inline. '">' .$checkbox.$label. '</label>' : $checkbox;
  }

  public function testSingle()
  {
    $checkbox = Former::checkbox('foo')->__toString();
    $matcher = $this->cg($this->cb('foo'));

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
    $matcher = $this->cgm($this->cb('foo_0', 'Foo').$this->cb('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testInline()
  {
    $checkboxes1 = Former::inline_checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $checkboxes2 = Former::checkboxes('foo')->inline()->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cgm($this->cb('foo_0', 'Foo', 1, true).$this->cb('foo_1', 'Bar', 1, true));

    $this->assertEquals($matcher, $checkboxes1);
    $this->assertEquals($matcher, $checkboxes2);
  }

  public function testStacked()
  {
    $checkboxes1 = Former::stacked_checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $checkboxes2 = Former::checkboxes('foo')->stacked()->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cgm($this->cb('foo_0', 'Foo', 1).$this->cb('foo_1', 'Bar', 1));

    $this->assertEquals($matcher, $checkboxes1);
    $this->assertEquals($matcher, $checkboxes2);
  }

  public function testMultipleArray()
  {
    $checkboxes = Former::checkboxes('foo')->checkboxes(array('Foo' => 'foo', 'Bar' => 'bar'))->__toString();
    $matcher = $this->cgm($this->cb('foo', 'Foo').$this->cb('bar', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testMultipleCustom()
  {
    $checkboxes = Former::checkboxes('foo')->checkboxes($this->checkables)->__toString();
    $matcher = $this->cgm(
    '<label for="foo" class="checkbox">'.
      '<input data-foo="bar" value="bar" id="foo" type="checkbox" name="foo">'.
      'Foo'.
    '</label>'.
    '<label for="foo" class="checkbox">'.
      '<input data-foo="bar" value="bar" id="bar" type="checkbox" name="foo">'.
      'Bar'.
    '</label>');

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testMultipleCustomNoName()
  {
    $checkables = $this->checkables;
    unset($checkables['Foo']['name']);
    unset($checkables['Bar']['name']);

    $checkboxes = Former::checkboxes('foo')->checkboxes($checkables)->__toString();
    $matcher = $this->cgm(
    '<label for="foo_0" class="checkbox">'.
      '<input data-foo="bar" value="bar" id="foo_0" type="checkbox" name="foo_0">'.
      'Foo'.
    '</label>'.
    '<label for="foo_1" class="checkbox">'.
      '<input data-foo="bar" value="bar" id="bar" type="checkbox" name="foo_1">'.
      'Bar'.
    '</label>');

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCheck()
  {
    $checkbox = Former::checkbox('foo')->check()->__toString();
    $matcher = $this->cg($this->cbc());

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCheckOneInSeveral()
  {
    $checkboxes = Former::checkboxes('foo')->checkboxes('foo', 'bar')->check('foo_1')->__toString();
    $matcher = $this->cgm($this->cb('foo_0', 'Foo').$this->cbc('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCheckMultiple()
  {
    $checkboxes = Former::checkboxes('foo')->checkboxes('foo', 'bar')->check(array('foo_0' => false, 'foo_1' => true))->__toString();
    $matcher = $this->cgm($this->cb('foo_0', 'Foo').$this->cbc('foo_1', 'Bar', 1));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testRepopulateFromPost()
  {
    Input::merge(array('foo_0' => true));

    $checkboxes = Former::checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cgm($this->cbc('foo_0', 'Foo').$this->cb('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testRepopulateFromModel()
  {
    Former::populate((object) array('foo_0' => true));

    $checkboxes = Former::checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cgm($this->cbc('foo_0', 'Foo').$this->cb('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testRepeatedOutput()
  {
    $checkbox = Former::checkbox('foo');

    $content = HTML::decode($checkbox);
    $content = HTML::decode($checkbox);

    $this->assertEquals($content, Former::checkbox('foo')->__toString());
  }

  public function testPushedCheckboxes()
  {
    Former::config('push_checkboxes', true);
    $checkbox = Former::checkbox('foo')->text('foo')->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="checkbox">'.
        '<input type="hidden" name="foo" id="foo">'.
        $this->cb('foo').'Foo'.
      '</label>');

    $this->assertEquals($matcher, $checkbox);
    Former::config('push_checkboxes', false);
  }

  public function testCheckboxesKeepOriginalValueOnSubmit()
  {
    Input::merge(array('foo' => ''));

    Former::config('push_checkboxes', true);
    $checkbox = Former::checkbox('foo')->text('foo')->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="checkbox">'.
        '<input type="hidden" name="foo" id="foo">'.
        $this->cb('foo').'Foo'.
      '</label>');

    $this->assertEquals($matcher, $checkbox);
    Former::config('push_checkboxes', false);
  }
}
