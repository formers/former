<?php
namespace Former\Framework;

use Former\TestCases\FormerTests;

class ZurbFramework5Test extends FormerTests
{

	public function setUp()
	{
		parent::setUp();

		$this->former->framework('ZurbFoundation5');
		$this->former->horizontal_open()->__toString();
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// MATCHERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function matchLabel($name = 'foo', $field = 'foo', $required = false)
	{
		return array(
			'tag'        => 'label',
			'content'    => ucfirst($name),
			'attributes' => array(
				'for' => $field,
			),
		);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanUseMagicMethods()
	{
		$input   = $this->former->large_submit('Save')->__toString();
		$matcher = $this->matchInputButton('large button', 'Save');

		$this->assertHTML($matcher, $input);
	}

	public function testCanSetAnErrorStateOnAField()
	{
		$input   = $this->former->text('foo')->state('error')->__toString();
		$matcher = array('tag' => 'div', 'attributes' => array('class' => 'error'));

		$this->assertLabel($input);
		$this->assertHTML($this->matchField(), $input);
		$this->assertHTML($matcher, $input);
	}

	public function testCanAppendHelpTexts()
	{
		$input   = $this->former->text('foo')->inlineHelp('bar')->__toString();
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

	public function testCreateIconWithFrameworkSpecificIcon()
	{
		$icon  = $this->app['former.framework']->createIcon('star')->__toString();
		$match = '<i class="fi-star"></i>';

		$this->assertEquals($match, $icon);
	}

	public function testCanAppendIcon()
	{
		$this->former->vertical_open();
		$input = $this->former->text('foo')->appendIcon('star')->__toString();
		$match = '<div>'.
			'<label for="foo">Foo</label>'.
			'<div class="row collapse">'.
			'<div class="large-10 small-9 columns">'.
			'<input id="foo" type="text" name="foo">'.
			'</div>'.
			'<div class="large-2 small-3 columns">'.
			'<span class="postfix">'.
			'<i class="fi-star"></i>'.
			'</span>'.
			'</div>'.
			'</div>'.
			'</div>';

		$this->assertEquals($match, $input);
	}

	public function testCreateOverideIconSettingsWithFrameworkSpecificIcon()
	{
		$icon  = $this->app['former.framework']->createIcon('star')->__toString();
		$match = '<i class="fi-star"></i>';

		$this->assertEquals($match, $icon);
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
			'<div class="small-3 columns">'.
			'<label for="foo" class="right inline">Foo</label>'.
			'</div>'.
			'<div class="small-9 columns">'.
			'<input id="foo" type="text" name="foo">'.
			'</div>'.
			'</div>';

		$this->assertEquals($match, $field);
	}

	public function testHelpTextHasCorrectClasses()
	{

		$input   = $this->former->text('foo')->inlineHelp('bar')->__toString();
		$matcher = array('tag' => 'small', 'attributes' => array('class' => 'error'), 'content' => 'Bar');
		$this->assertHTML($matcher, $input);
	}
}
