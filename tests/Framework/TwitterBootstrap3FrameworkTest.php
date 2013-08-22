<?php

class TwitterBootstrap3FrameworkTest extends FormerTests
{

  public function setUp()
  {
    parent::setUp();

    $this->former->framework('TwitterBootstrap3');
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testFrameworkIsRecognized()
  {
    $this->assertNotEquals('TwitterBootstrap', $this->former->framework());
    $this->assertEquals('TwitterBootstrap3', $this->former->framework());
  }

  public function testPrependIcon()
  {
    $icon = $this->former->text('foo')->prependIcon('ok')->__toString();
    $match = '<div class="form-group"><label for="foo">Foo</label>'.
             '<div class="input-group">'.
             '<span class="input-group-addon"><span class="glyphicon glyphicon-ok"></span></span>'.
             '<input id="foo" type="text" name="foo">'.
             '</div></div>';

    $this->assertEquals($match, $icon);
  }

  public function testAppendIcon()
  {
    $icon = $this->former->text('foo')->appendIcon('ok')->__toString();
    $match = '<div class="form-group"><label for="foo">Foo</label>'.
             '<div class="input-group">'.
             '<input id="foo" type="text" name="foo">'.
             '<span class="input-group-addon"><span class="glyphicon glyphicon-ok"></span></span>'.
             '</div></div>';

    $this->assertEquals($match, $icon);
  }

}
