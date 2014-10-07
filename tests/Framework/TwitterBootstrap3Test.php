<?php
namespace Former\Framework;

use Former\TestCases\FormerTests;

class TwitterBootstrap3Test extends FormerTests
{

	public function setUp()
	{
		parent::setUp();

		$this->former->framework('TwitterBootstrap3');
		$this->former->horizontal_open()->__toString();
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// MATCHERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function hmatch($label, $field)
	{
		return '<div class="form-group">'.$label.'<div class="col-lg-10 col-sm-8">'.$field.'</div></div>';
	}

	public function vmatch($label, $field)
	{
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

		$match = $this->vmatch('<label for="foo" class="control-label">Foo</label>',
			'<input class="form-control" id="foo" type="text" name="foo">');

		$this->assertEquals($match, $field);
	}

	public function testHorizontalFormWithDefaultLabelWidths()
	{
		$field = $this->former->text('foo')->__toString();
		$match = $this->hmatch('<label for="foo" class="control-label col-lg-2 col-sm-4">Foo</label>',
			'<input class="form-control" id="foo" type="text" name="foo">');

		$this->assertEquals($match, $field);
	}

	public function testPrependIcon()
	{
		$this->former->open_vertical();
		$icon  = $this->former->text('foo')->prependIcon('ok')->__toString();
		$match = $this->vmatch('<label for="foo" class="control-label">Foo</label>',
			'<div class="input-group">'.
			'<span class="input-group-addon"><span class="glyphicon glyphicon-ok"></span></span>'.
			'<input class="form-control" id="foo" type="text" name="foo">'.
			'</div>');

		$this->assertEquals($match, $icon);
	}

	public function testAppendIcon()
	{
		$this->former->open_vertical();
		$icon  = $this->former->text('foo')->appendIcon('ok')->__toString();
		$match = $this->vmatch('<label for="foo" class="control-label">Foo</label>',
			'<div class="input-group">'.
			'<input class="form-control" id="foo" type="text" name="foo">'.
			'<span class="input-group-addon"><span class="glyphicon glyphicon-ok"></span></span>'.
			'</div>');
		$this->assertEquals($match, $icon);
	}

	public function testTextFieldsGetControlClass()
	{
		$this->former->open_vertical();
		$field = $this->former->text('foo')->__toString();
		$match = $this->vmatch('<label for="foo" class="control-label">Foo</label>',
			'<input class="form-control" id="foo" type="text" name="foo">');

		$this->assertEquals($match, $field);
	}

	public function testButtonSizes()
	{
		$this->former->open_vertical();
		$buttons = $this->former->actions()->lg_submit('Submit')->submit('Submit')->sm_submit('Submit')->xs_submit('Submit')->__toString();
		$match   = '<div>'.
			'<input class="btn-lg btn" type="submit" value="Submit">'.
			' <input class="btn" type="submit" value="Submit">'.
			' <input class="btn-sm btn" type="submit" value="Submit">'.
			' <input class="btn-xs btn" type="submit" value="Submit">'.
			'</div>';

		$this->assertEquals($match, $buttons);
	}

	public function testCanOverrideFrameworkIconSettings()
	{
		// e.g. using other Glyphicon sets
		$icon1  = $this->app['former.framework']->createIcon('facebook', null, array(
			'set'    => 'social',
			'prefix' => 'glyphicon',
		))->__toString();
		$match1 = '<span class="social glyphicon-facebook"></span>';

		$this->assertEquals($match1, $icon1);

		// e.g using Font-Awesome circ v3.2.1
		$icon2  = $this->app['former.framework']->createIcon('flag', null, array(
			'tag'    => 'i',
			'set'    => '',
			'prefix' => 'icon',
		))->__toString();
		$match2 = '<i class="icon-flag"></i>';

		$this->assertEquals($match2, $icon2);
	}

	public function testCanCreateWithErrors()
	{
		$this->former->open_vertical();
		$this->former->withErrors($this->validator);

		$required = $this->former->text('required')->__toString();
		$matcher  =
			'<div class="form-group has-error">'.
			'<label for="required" class="control-label">Required</label>'.
			'<input class="form-control" id="required" type="text" name="required">'.
			'<span class="help-block">The required field is required.</span>'.
			'</div>';

		$this->assertEquals($matcher, $required);
	}

	public function testAddScreenReaderClassToInlineFormLabels()
	{
		$this->former->open_inline();

		$field = $this->former->text('foo')->__toString();

		$match =
			'<div class="form-group">'.
			'<label for="foo" class="sr-only">Foo</label>'.
			'<input class="form-control" id="foo" type="text" name="foo">'.
			'</div>';

		$this->assertEquals($match, $field);
		$this->assertEquals($match, $field);

		$this->former->close();
	}

	public function testHeightSettingForFields()
	{
		$this->former->open_vertical();

		$field = $this->former->lg_text('foo')->__toString();
		$match =
			'<div class="form-group">'.
			'<label for="foo" class="control-label">Foo</label>'.
			'<input class="input-lg form-control" id="foo" type="text" name="foo">'.
			'</div>';
		$this->assertEquals($match, $field);

		$this->resetLabels();
		$field = $this->former->sm_select('foo')->__toString();
		$match =
			'<div class="form-group">'.
			'<label for="foo" class="control-label">Foo</label>'.
			'<select class="input-sm form-control" id="foo" name="foo"></select>'.
			'</div>';
		$this->assertEquals($match, $field);

		$this->former->close();
	}

	public function testAddFormControlClassToInlineActionsBlock()
	{
		$this->former->open_inline();
		$buttons = $this->former->actions()->submit('Foo')->__toString();
		$match   = '<div class="form-group">'.
			'<input class="btn" type="submit" value="Foo">'.
			'</div>';

		$this->assertEquals($match, $buttons);

		$this->former->close();
	}

	public function testButtonsAreWrappedInSpecialClass()
	{
		$button  = $this->former->text('foo')->append($this->former->button('Search'))->wrapAndRender();
		$matcher = '<span class="input-group-btn"><button class="btn" type="button">Search</button></span>';

		$this->assertContains($matcher, $button);
	}
}
