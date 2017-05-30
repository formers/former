<?php
namespace Former\Framework;

use Former\TestCases\FormerTests;

class NudeFrameworkTest extends FormerTests
{

	public function setUp()
	{
		parent::setUp();

		$this->former->framework('Nude');
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanDisplayErrorMessages()
	{
		// Create field
		$this->former->withErrors($this->validator);
		$required = $this->former->text('required')->wrapAndRender();

		// Matcher
		$matcher =
			'<label for="required">Required</label>'.
			'<input id="required" type="text" name="required">'.
			'<span class="help">The required field is required.</span>';

		$this->assertEquals($matcher, $required);
	}

	public function testCanDisplayNestedErrorMessages()
	{
		// Use special one-time-use validator
		$this->former->withErrors($this->mockValidator("foo.bar", "Foo", null));

		// Create field
		$required = $this->former->text("foo[bar]")->id('foo')->label('Foo')->wrapAndRender();

		// Matcher
		$matcher =
			'<label for="foo">Foo</label>'.
			'<input id="foo" type="text" name="foo[bar]">'.
			'<span class="help">The Foo field is required.</span>';

		$this->assertEquals($matcher, $required);
	}

	public function testGroupOpenHasNoElement()
	{
		$group   = $this->former->group('foo')->__toString();
		$matcher = '<label for="Foo">Foo</label>';

		$this->assertEquals($matcher, $group);
	}

	public function testGroupCloseHasNoElement()
	{
		$this->former->group('foo');
		$text = $this->former->closeGroup();

		$this->assertEmpty($text);
	}
}
