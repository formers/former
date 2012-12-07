<?php
use \Former\Former;

class CheckboxTest extends FormerTests
{
  private function cb($name = 'foo', $label = null, $value = 1, $inline = false, $checked = false)
  {
    $checkAttr = array(
      'id'      => $name,
      'checked' => 'checked',
      'type'    => 'checkbox',
      'name'    => $name,
      'value'   => $value,
    );
    $labelAttr = array(
      'for'   => $name,
      'class' => 'checkbox',
    );
    if ($inline) $labelAttr['class'] .= ' inline';
    if (!$checked) unset($checkAttr['checked']);

    $radio = '<input'.HTML::attributes($checkAttr).'>';

    return $label ? '<label'.HTML::attributes($labelAttr). '>' .$radio.$label. '</label>' : $radio;
  }

  private function cbc($name = 'foo', $label = null, $value = 1, $inline = false)
  {
    return $this->cb($name, $label, $value, $inline, true);
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
    $this->resetLabels();
    $checkboxes2 = Former::checkboxes('foo')->inline()->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cgm($this->cb('foo_0', 'Foo', 1, true).$this->cb('foo_1', 'Bar', 1, true));

    $this->assertEquals($matcher, $checkboxes1);
    $this->assertEquals($matcher, $checkboxes2);
  }

  public function testStacked()
  {
    $checkboxes1 = Former::stacked_checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $this->resetLabels();
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
    '<label for="bar" class="checkbox">'.
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
    '<label for="bar" class="checkbox">'.
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
        '<input type="hidden" name="foo" value="" id="foo">'.
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
        '<input type="hidden" name="foo" value="" id="foo">'.
        $this->cb('foo').'Foo'.
      '</label>');

    $this->assertEquals($matcher, $checkbox);
    Former::config('push_checkboxes', false);
  }

  public function testCustomUncheckedValue()
  {
    Former::config('push_checkboxes', true);
    Former::config('unchecked_value', 'unchecked');
    $checkbox = Former::checkbox('foo')->text('foo')->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="checkbox">'.
        '<input type="hidden" name="foo" value="unchecked" id="foo">'.
        $this->cb('foo').'Foo'.
      '</label>');

    $this->assertEquals($matcher, $checkbox);
    Former::config('push_checkboxes', false);
  }

  public function testCanGroupCheckboxes()
  {
    Former::framework(null);
    $auto = Former::checkboxes('value[]', '')->checkboxes('Value 01', 'Value 02')->__toString();
    $chain = Former::checkboxes('value', '')->grouped()->checkboxes('Value 01', 'Value 02')->__toString();

    $this->assertEquals($chain, $auto);
    $this->assertEquals(
      '<label for="value_0" class="checkbox">'.
        '<input id="value_0" type="checkbox" name="value[]" value="1">Value 01'.
      '</label>'.
      '<label for="value_1" class="checkbox">'.
        '<input id="value_1" type="checkbox" name="value[]" value="1">Value 02'.
      '</label>', $auto);
  }
}
