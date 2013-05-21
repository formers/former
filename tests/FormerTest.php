<?php
class FormerTest extends FormerTests
{

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// MATCHERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function matchLegend()
  {
    return array(
      'tag' => 'legend',
      'content' => 'Test',
      'attributes' => $this->testAttributes,
    );
  }

  public function matchToken()
  {
    return array(
      'tag' => 'input',
      'attributes' => array(
        'type'  => 'hidden',
        'name'  => '_token',
        'value' => 'csrf_token',
      ),
    );
  }

  public function matchLabel($name = null, $field = null, $required = null)
  {
    return array(
      'tag' => 'label',
      'content' => 'Foo',
      'attributes' => array('for' => '')
    );
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testCanCreateFormLegends()
  {
    $legend = $this->former->legend('test', $this->testAttributes);

    $this->assertHTML($this->matchLegend(), $legend);
  }

  public function testCanCreateFormLabels()
  {
    $label = $this->former->label('foo');

    $this->assertLabel($label);
  }

  public function testCanCreateCsrfTokens()
  {
    $token = $this->former->token();

    $this->assertHTML($this->matchToken(), $token);
  }

  public function testCanCreateFormMacros()
  {
    $former = $this->former;
    $this->former->macro('captcha', function($name = null) use ($former) {
      return $former->text($name)->raw();
    });

    $this->assertEquals($this->former->text('foo')->raw(), $this->former->captcha('foo'));
    $this->assertHTML($this->matchField(), $this->former->captcha('foo'));
  }

  public function testMacrosDontTakePrecedenceOverNativeFields()
  {
    $former = $this->former;
    $this->former->macro('label', function() use ($former) {
      return 'NOPE';
    });

    $this->assertNotEquals('NOPE', $this->former->label('foo'));
  }

}
