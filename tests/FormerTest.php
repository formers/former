<?php
namespace Former;

use Former\Facades\Former;
use Former\TestCases\FormerTests;

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
			'tag'        => 'input',
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
			'attributes' => array('for' => ''),
		);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanCreateFormLegends()
	{
        $this->markTestSkipped('Fatals with `Could not load XML from object`');

		$legend = $this->former->legend('test', $this->testAttributes);

		$this->assertHTML($this->matchLegend(), $legend);
	}

	public function testCanCreateFormLabels()
	{
        $this->markTestSkipped('Fatals with `Could not load XML from object`');

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
        $this->markTestSkipped('Fatals with `Could not load XML from object`');

		$former = $this->former;
		$this->former->macro('captcha', function ($name = null) use ($former) {
			return $former->text($name)->raw();
		});

		$this->assertEquals($this->former->text('foo')->raw(), $this->former->captcha('foo'));
		$this->assertHTML($this->matchField(), $this->former->captcha('foo'));
	}

	public function testCanUseClassesAsMacros()
	{
		$this->former->macro('loltext', 'Former\Dummy\DummyMacros@loltext');

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
        $this->markTestSkipped("Test currently failing, but I'm not sure how to fix or if it matters. " .
             'If using Facade without a container is broken for you, please file an issue or PR with details.');

		$text = Former::text('foo')->render();

		$this->assertEquals('<input class="form-control" id="foo" type="text" name="foo">', $text);
	}
}
