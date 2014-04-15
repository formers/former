<?php
class ZurbFrameworkTest extends FormerTests
{

  public function setUp()
  {
    parent::setUp();

    $this->former->framework('ZurbFoundation');
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// MATCHERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function matchLabel($name = 'foo', $field = 'foo', $required = false)
  {
    return array(
      'tag' => 'label',
      'content' => ucfirst($name),
      'attributes' => array(
        'for'   => $field,
      ),
    );
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testCanUseMagicMethods()
  {
    $input = $this->former->three_text('foo')->__toString();

    $this->assertLabel($input);
    $this->assertHTML($this->matchField(), $input);
  }

  public function testCanSetAnErrorStateOnAField()
  {
    $input = $this->former->text('foo')->state('error')->__toString();
    $matcher = array('tag' => 'div', 'attributes' => array('class' => 'error'));

    $this->assertLabel($input);
    $this->assertHTML($this->matchField(), $input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanAppendHelpTexts()
  {
    $input = $this->former->text('foo')->inlineHelp('bar')->__toString();
    $matcher = array('tag' => 'span', 'content' => 'Bar');

    $this->assertLabel($input);
    $this->assertHTML($this->matchField(), $input);
    $this->assertHTML($matcher, $input);
  }

  public function testCantUseBootstrapReservedMethods()
  {
    $this->setExpectedException('BadMethodCallException');

    $this->former->text('foo')->blockHelp('bar')->__toString();
  }

  public function testVerticalFormInputField()
  {
    $this->former->vertical_open();
    $field = $this->former->text('foo')->__toString();

    $match = '<div>'.
               '<label for="foo">Foo</label>'.
               '<input id="foo" type="text" name="foo">'.
             '</div>';

    $this->assertEquals($match, $field);
  }

  public function testHorizontalFormInputField()
  {
    $field = $this->former->text('foo')->__toString();

    $match = '<div class="row">'.
               '<div class="two mobile-four columns">'.
                 '<label for="foo" class="right inline">Foo</label>'.
               '</div>'.
               '<div class="ten mobile-eight columns">'.
                 '<input id="foo" type="text" name="foo">'.
               '</div>'.
             '</div>';

    $this->assertEquals($match, $field);
  }

  public function testHelpTextHasCorrectClasses()
  {

    $input = $this->former->text('foo')->inlineHelp('bar')->__toString();
    $matcher = array('tag' => 'span', 'attributes' => array( 'class' => 'alert-box alert error' ), 'content' => 'Bar');
    $this->assertHTML($matcher, $input);

  }


}
