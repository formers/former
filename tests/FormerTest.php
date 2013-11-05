<?php
class FormerTest extends FormerTests
{
  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// MATCHERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function matchLegend()
  {
    return array(
      'tag'        => 'legend',
      'content'    => 'Test',
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

  public function matchLabel($name = 'foo', $field = 'foo', $required = false)
  {
    return array(
      'tag'        => 'label',
      'content'    => 'Foo',
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
    $this->former->macro('captcha', function ($name = null) use ($former) {
      return $former->text($name)->raw();
    });

    $this->assertEquals($this->former->text('foo')->raw(), $this->former->captcha('foo'));
    $this->assertHTML($this->matchField(), $this->former->captcha('foo'));
  }

  public function testCanUseClassesAsMacros()
  {
    $this->former->macro('loltext', 'DummyMacros@loltext');

    $this->assertEquals('lolfoobar', $this->former->loltext('foobar'));
  }

  public function testMacrosDontTakePrecedenceOverNativeFields()
  {
    $former = $this->former;
    $this->former->macro('label', function () use ($former) {
      return 'NOPE';
    });

    $this->assertNotEquals('NOPE', $this->former->label('foo'));
  }

  public function testCloseCorrectlyRemoveInstances()
  {
    $this->former->close();

    $this->assertFalse($this->app->bound('former.form'));
  }

  public function testCanUseFacadeWithoutContainer()
  {
    $text = Former\Facades\Former::text('foo')->render();

    $this->assertEquals('<input id="foo" type="text" name="foo">', $text);
  }
}
