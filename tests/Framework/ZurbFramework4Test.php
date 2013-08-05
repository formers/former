<?php
class ZurbFramework4Test extends FormerTests
{

  public function setUp()
  {
    parent::setUp();

    $this->former->framework('ZurbFoundation4');
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
    $input = $this->former->large_submit('Save')->__toString();
    $matcher = $this->matchInputButton('large button', 'Save');

    $this->assertHTML($matcher, $input);
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
    $matcher = array('tag' => 'small', 'content' => 'Bar');

    $this->assertLabel($input);
    $this->assertHTML($this->matchField(), $input);
    $this->assertHTML($matcher, $input);
  }

  public function testCantUseBootstrapReservedMethods()
  {
    $this->setExpectedException('BadMethodCallException');

    $this->former->text('foo')->blockHelp('bar')->__toString();
  }

}
