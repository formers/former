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
    $match = '<div class="form-group">'.
             '<div class="col-lg-offset-2 col-lg-10">'.
             '<input class="btn-lg btn" type="submit" value="Submit">'.
             ' <input class="btn" type="submit" value="Submit">'.
             ' <input class="btn-sm btn" type="submit" value="Submit">'.
             ' <input class="btn-xs btn" type="submit" value="Submit">'.
             '</div>'.
             '</div>';

    $this->assertEquals($match, $buttons);
  }

  public function testCanOverrideFrameworkIconSettings()
  {
    // e.g. using other Glyphicon sets
    $icon1 = $this->app['former.framework']->createIcon('facebook', null, array('set'=>'social','prefix'=>'glyphicon'))->__toString();
    $match1 = '<span class="social glyphicon-facebook"></span>';

    $this->assertEquals($match1, $icon1);

    // e.g using Font-Awesome circ v3.2.1
    $icon2 = $this->app['former.framework']->createIcon('flag', null, array('tag'=>'i', 'set' => '', 'prefix'=>'icon'))->__toString();
    $match2 = '<i class="icon-flag"></i>';

    $this->assertEquals($match2, $icon2);
  }

  public function testCanCreateWithErrors()
  {
    $this->former->withErrors($this->validator);

    $required = $this->former->text('required')->__toString();
    $matcher =
    '<div class="form-group has-error">'.
      '<label for="required" class="control-label col-lg-2">Required</label>'.
      '<div class="col-lg-10">'.
        '<input class="form-control" id="required" type="text" name="required">'.
        '<span class="help-block">The required field is required.</span>'.
      '</div>'.
    '</div>';

    $this->assertEquals($matcher, $required);
  }

}
