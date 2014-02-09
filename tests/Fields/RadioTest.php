<?php
class RadioTest extends FormerTests
{

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// MATCHERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Matches a radio element
   *
   * @param  string  $name
   * @param  string  $label
   * @param  integer $value
   * @param  boolean $inline
   * @param  boolean $checked
   *
   * @return string
   */
  private function matchRadio($name = 'foo', $label = null, $value = 1, $inline = false, $checked = false)
  {
    $radioAttr = array(
      'id'      => $name,
      'type'    => 'radio',
      'name'    => preg_replace('/[0-9]/', null, $name),
      'checked' => 'checked',
      'value'   => $value,
    );
    $labelAttr = array(
      'for'   => $name,
      'class' => 'radio',
    );
    if ($inline) {
      $labelAttr['class'] .= $this->former->framework() === 'TwitterBootstrap3' ? ' radio-inline' : ' inline';
    }
    if (!$checked) unset($radioAttr['checked']);

    $radio = '<input'.$this->attributes($radioAttr).'>';

    return $label ? '<label'.$this->attributes($labelAttr). '>' .$radio.$label. '</label>' : $radio;
  }

  /**
   * Matches a checked radio element
   *
   * @param  string  $name
   * @param  string  $label
   * @param  integer $value
   * @param  boolean $inline
   *
   * @return string
   */
  private function matchCheckedRadio($name = 'foo', $label = null, $value = 1, $inline = false)
  {
    return $this->matchRadio($name, $label, $value, $inline, true);
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testSingle()
  {
    $radio = $this->former->radio('foo')->__toString();
    $matcher = $this->controlGroup($this->matchRadio());

    $this->assertEquals($matcher, $radio);
  }

  public function testSingleWithLabel()
  {
    $radio = $this->former->radio('foo')->text('bar')->__toString();
    $matcher = $this->controlGroup($this->matchRadio('foo', 'Bar'));

    $this->assertEquals($matcher, $radio);
  }

  public function testSingleWithValue()
  {
    $radio = $this->former->radio('foo')->text('bar')->value('foo')->__toString();
    $matcher = $this->controlGroup($this->matchRadio('foo', 'Bar', 'foo'));

    $this->assertEquals($matcher, $radio);
  }

  public function testMultiple()
  {
    $radios = $this->former->radios('foo')->radios('foo', 'bar')->__toString();
    $matcher = $this->controlGroup($this->matchRadio('foo', 'Foo', 0).$this->matchRadio('foo2', 'Bar'));

    $this->assertEquals($matcher, $radios);
  }

  public function testInline()
  {
    $radios1 = $this->former->inline_radios('foo')->radios('foo', 'bar')->__toString();
    $this->resetLabels();
    $radios2 = $this->former->radios('foo')->inline()->radios('foo', 'bar')->__toString();

    $matcher = $this->controlGroup($this->matchRadio('foo', 'Foo', 0, true).$this->matchRadio('foo2', 'Bar', 1, true));

    $this->assertEquals($matcher, $radios1);
    $this->assertEquals($matcher, $radios2);
  }

  public function testInlineTwitterBootstrap3()
  {
    $this->former->framework('TwitterBootstrap3');

    $radios1 = $this->former->inline_radios('foo')->radios('foo', 'bar')->__toString();
    $this->resetLabels();
    $radios2 = $this->former->radios('foo')->inline()->radios('foo', 'bar')->__toString();

    $matcher = $this->formGroup($this->matchRadio('foo', 'Foo', 0, true).$this->matchRadio('foo2', 'Bar', 1, true));

    $this->assertEquals($matcher, $radios1);
    $this->assertEquals($matcher, $radios2);
  }

  public function testStacked()
  {
    $radios1 = $this->former->stacked_radios('foo')->radios('foo', 'bar')->__toString();
    $this->resetLabels();
    $radios2 = $this->former->radios('foo')->stacked()->radios('foo', 'bar')->__toString();

    $matcher = $this->controlGroup($this->matchRadio('foo', 'Foo', 0).$this->matchRadio('foo2', 'Bar', 1));

    $this->assertEquals($matcher, $radios1);
    $this->assertEquals($matcher, $radios2);
  }

  public function testMultipleArray()
  {
    $radios = $this->former->radios('foo')->radios(array('Foo' => 'foo', 'Bar' => 'bar'))->__toString();
    $matcher = $this->controlGroup($this->matchRadio('foo', 'Foo', 0).$this->matchRadio('bar', 'Bar'));

    $this->assertEquals($matcher, $radios);
  }

  public function testMultipleCustom()
  {
    $radios = $this->former->radios('foo')->radios($this->checkables)->__toString();
    $matcher = $this->controlGroup(
    '<label for="foo" class="radio">'.
      '<input data-foo="bar" value="bar" id="foo" type="radio" name="foo">'.
      'Foo'.
    '</label>'.
    '<label for="bar" class="radio">'.
      '<input data-foo="bar" value="bar" id="bar" type="radio" name="foo">'.
      'Bar'.
    '</label>');

    $this->assertEquals($matcher, $radios);
  }

  public function testMultipleCustomNoName()
  {
    $checkables = $this->checkables;
    unset($checkables['Foo']['name']);
    unset($checkables['Bar']['name']);

    $radios = $this->former->radios('foo')->radios($checkables)->__toString();
    $matcher = $this->controlGroup(
    '<label for="foo" class="radio">'.
      '<input data-foo="bar" value="bar" id="foo" type="radio" name="foo">'.
      'Foo'.
    '</label>'.
    '<label for="bar" class="radio">'.
      '<input data-foo="bar" value="bar" id="bar" type="radio" name="foo">'.
      'Bar'.
    '</label>');

    $this->assertEquals($matcher, $radios);
  }

  public function testCheck()
  {
    $radio = $this->former->radio('foo')->check()->__toString();
    $matcher = $this->controlGroup($this->matchCheckedRadio());

    $this->assertEquals($matcher, $radio);
  }

  public function testCheckOneInSeveral()
  {
    $radios = $this->former->radios('foo')->radios('foo', 'bar')->check(0)->__toString();
    $matcher = $this->controlGroup($this->matchCheckedRadio('foo', 'Foo', 0).$this->matchRadio('foo2', 'Bar', 1));

    $this->assertEquals($matcher, $radios);
  }

  public function testCheckMultiple()
  {
    $radios = $this->former->radios('foo')->radios('foo', 'bar')->check(array(0 => false, 1 => true))->__toString();
    $matcher = $this->controlGroup($this->matchRadio('foo', 'Foo', 0).$this->matchCheckedRadio('foo2', 'Bar', 1));

    $this->assertEquals($matcher, $radios);
  }

  public function testCanAttributeIndividualLabelsPerRadio()
  {
    $radios = $this->former->radios('foo')->radios('foo', 'bar')->__toString();
    $matcher = $this->controlGroup($this->matchRadio('foo', 'Foo', 0).$this->matchRadio('foo2', 'Bar', 1));

    $this->assertEquals($matcher, $radios);
  }

  public function testRepopulateFromPost()
  {
    $this->request->shouldReceive('input')->andReturn(0);

    $radios = $this->former->radios('foo')->radios('foo', 'bar')->__toString();
    $matcher = $this->controlGroup($this->matchCheckedRadio('foo', 'Foo', 0).$this->matchRadio('foo2', 'Bar', 1));

    $this->assertEquals($matcher, $radios);
  }

  public function testRepopulateFromModel()
  {
    $this->former->populate((object) array('foo' => 0));

    $radios = $this->former->radios('foo')->radios('foo', 'bar')->__toString();
    $matcher = $this->controlGroup($this->matchCheckedRadio('foo', 'Foo', 0).$this->matchRadio('foo2', 'Bar', 1));

    $this->assertEquals($matcher, $radios);
  }

  public function testInlineRadiosAreRendered()
  {
    $this->former->form()->close();
    $this->former->inline_open();

    $radio = $this->former->radio('foo', 'bar')->__toString();

    $this->assertInternalType('string', $radio);
  }

}
