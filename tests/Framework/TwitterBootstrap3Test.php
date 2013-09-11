<?php

class TwitterBootstrap3Test extends FormerTests
{

  public function setUp()
  {
    parent::setUp();

    $this->former->framework('TwitterBootstrap3');
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// MATCHERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function hmatch($label, $field) {
    return '<div class="form-group">'.$label.'<div class="col-lg-10">'.$field.'</div></div>';
  }

  public function vmatch($label, $field) {
    return '<div class="form-group">'.$label.$field.'</div>';
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testFrameworkIsRecognized()
  {
    $this->assertNotEquals('TwitterBootstrap', $this->former->framework());
    $this->assertEquals('TwitterBootstrap3', $this->former->framework());
  }

  public function testVerticalFormFieldsDontInheritHorizontalMarkup()
  {
    $this->former->open_vertical();
    $field = $this->former->text('foo')->__toString();
    $this->former->close();

    $match = $this->vmatch('<label for="foo">Foo</label>',
                           '<input class="form-control" id="foo" type="text" name="foo">');

    $this->assertEquals($match, $field);
  }

  public function testPrependIcon()
  {
    $icon = $this->former->text('foo')->prependIcon('ok')->__toString();
    $match = $this->hmatch('<label for="foo" class="control-label col-lg-2">Foo</label>',
                           '<div class="input-group">'.
                           '<span class="input-group-addon"><span class="glyphicon glyphicon-ok"></span></span>'.
                           '<input class="form-control" id="foo" type="text" name="foo">'.
                           '</div>');

    $this->assertEquals($match, $icon);
  }

  public function testAppendIcon()
  {
    $icon = $this->former->text('foo')->appendIcon('ok')->__toString();
    $match = $this->hmatch('<label for="foo" class="control-label col-lg-2">Foo</label>',
                           '<div class="input-group">'.
                           '<input class="form-control" id="foo" type="text" name="foo">'.
                           '<span class="input-group-addon"><span class="glyphicon glyphicon-ok"></span></span>'.
                           '</div>');
    $this->assertEquals($match, $icon);
  }

  public function testTextFieldsGetControlClass()
  {
    $field = $this->former->text('foo')->__toString();
    $match =  $this->hmatch('<label for="foo" class="control-label col-lg-2">Foo</label>',
                            '<input class="form-control" id="foo" type="text" name="foo">');

    $this->assertEquals($match, $field);
  }

  public function testButtonSizes()
  {
    $buttons = $this->former->actions()->lg_submit('Submit')->submit('Submit')->sm_submit('Submit')->xs_submit('Submit')->__toString();
    $match = '<div>'.
             '<input class="btn-lg btn" type="submit" value="Submit">'.
             ' <input class="btn" type="submit" value="Submit">'.
             ' <input class="btn-sm btn" type="submit" value="Submit">'.
             ' <input class="btn-xs btn" type="submit" value="Submit">'.
             '</div>';

    $this->assertEquals($match, $buttons);
  }

}
