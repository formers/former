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

    $radio = '<input'.$this->app['former.helpers']->attributes($checkAttr).'>';

    return $label ? '<label'.$this->app['former.helpers']->attributes($labelAttr). '>' .$radio.$label. '</label>' : $radio;
  }

  private function cbc($name = 'foo', $label = null, $value = 1, $inline = false)
  {
    return $this->cb($name, $label, $value, $inline, true);
  }

  // Tests --------------------------------------------------------- /

  public function testCanCreateASingleCheckbox()
  {
    $checkbox = $this->app['former']->checkbox('foo')->__toString();
    $matcher = $this->cg($this->cb('foo'));

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanCreateACheckboxWithALabel()
  {
    $checkbox = $this->app['former']->checkbox('foo')->text('bar')->__toString();
    $matcher = $this->cg($this->cb('foo', 'Bar'));

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanSetValueOfASingleCheckbox()
  {
    $checkbox = $this->app['former']->checkbox('foo')->text('bar')->value('foo')->__toString();
    $matcher = $this->cg($this->cb('foo', 'Bar', 'foo'));

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanCreateMultipleCheckboxes()
  {
    $checkboxes = $this->app['former']->checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cgm($this->cb('foo_0', 'Foo').$this->cb('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanCreateInlineCheckboxes()
  {
    $checkboxes1 = $this->app['former']->inline_checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $this->resetLabels();
    $checkboxes2 = $this->app['former']->checkboxes('foo')->inline()->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cgm($this->cb('foo_0', 'Foo', 1, true).$this->cb('foo_1', 'Bar', 1, true));

    $this->assertEquals($matcher, $checkboxes1);
    $this->assertEquals($matcher, $checkboxes2);
  }

  public function testCanCreateStackedCheckboxes()
  {
    $checkboxes1 = $this->app['former']->stacked_checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $this->resetLabels();
    $checkboxes2 = $this->app['former']->checkboxes('foo')->stacked()->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cgm($this->cb('foo_0', 'Foo', 1).$this->cb('foo_1', 'Bar', 1));

    $this->assertEquals($matcher, $checkboxes1);
    $this->assertEquals($matcher, $checkboxes2);
  }

  public function testCanCreateMultipleCheckboxesViaAnArray()
  {
    $checkboxes = $this->app['former']->checkboxes('foo')->checkboxes(array('Foo' => 'foo', 'Bar' => 'bar'))->__toString();
    $matcher = $this->cgm($this->cb('foo', 'Foo').$this->cb('bar', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanCustomizeCheckboxesViaAnArray()
  {
    $checkboxes = $this->app['former']->checkboxes('foo')->checkboxes($this->checkables)->__toString();
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

  public function testCanCreateMultipleAnonymousCheckboxes()
  {
    $checkables = $this->checkables;
    unset($checkables['Foo']['name']);
    unset($checkables['Bar']['name']);

    $checkboxes = $this->app['former']->checkboxes('foo')->checkboxes($checkables)->__toString();
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

  public function testCanCheckASingleCheckbox()
  {
    $checkbox = $this->app['former']->checkbox('foo')->check()->__toString();
    $matcher = $this->cg($this->cbc());

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanCheckOneInSeveralCheckboxes()
  {
    $checkboxes = $this->app['former']->checkboxes('foo')->checkboxes('foo', 'bar')->check('foo_1')->__toString();
    $matcher = $this->cgm($this->cb('foo_0', 'Foo').$this->cbc('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanCheckMultipleCheckboxesAtOnce()
  {
    $checkboxes = $this->app['former']->checkboxes('foo')->checkboxes('foo', 'bar')->check(array('foo_0' => false, 'foo_1' => true))->__toString();
    $matcher = $this->cgm($this->cb('foo_0', 'Foo').$this->cbc('foo_1', 'Bar', 1));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanRepopulateCheckboxesFromPost()
  {
    $this->app['request']->shouldReceive('get')->with('foo', '')->andReturn('');
    $this->app['request']->shouldReceive('get')->with('foo_0', '')->andReturn(true);
    $this->app['request']->shouldReceive('get')->with('foo_1', '')->andReturn(false);

    $checkboxes = $this->app['former']->checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cgm($this->cbc('foo_0', 'Foo').$this->cb('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanPopulateCheckboxesFromAnObject()
  {
    $this->app['former']->populate((object) array('foo_0' => true));

    $checkboxes = $this->app['former']->checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->cgm($this->cbc('foo_0', 'Foo').$this->cb('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanDecodeCorrectlyCheckboxes()
  {
    $checkbox = $this->app['former']->checkbox('foo')->__toString();

    $content = $this->app['former.helpers']->decode($checkbox);

    $this->assertEquals($content, $this->app['former']->checkbox('foo')->__toString());
  }

  public function testCanPushUncheckedCheckboxes()
  {
    $this->app['config'] = $this->getConfig(true, '', true);

    $checkbox = $this->app['former']->checkbox('foo')->text('foo')->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="checkbox">'.
        '<input type="hidden" name="foo" value="" id="foo">'.
        $this->cb('foo').'Foo'.
      '</label>');

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanRepopulateCheckboxesOnSubmit()
  {
    $this->app['config'] = $this->getConfig(true, '', true);
    $this->app['request']->shouldReceive('get')->with('foo', '')->andReturn('');

    $checkbox = $this->app['former']->checkbox('foo')->text('foo')->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="checkbox">'.
        '<input type="hidden" name="foo" value="" id="foo">'.
        $this->cb('foo').'Foo'.
      '</label>');

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanCustomizeTheUncheckedValue()
  {
    $this->app['config'] = $this->getConfig(true, 'unchecked', true);

    $checkbox = $this->app['former']->checkbox('foo')->text('foo')->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="checkbox">'.
        '<input type="hidden" name="foo" value="unchecked" id="foo">'.
        $this->cb('foo').'Foo'.
      '</label>');

    $this->assertEquals($matcher, $checkbox);
  }
}
