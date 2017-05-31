<?php
namespace Former\Fields;

use Former\TestCases\FormerTests;

class PlainTextTest extends FormerTests
{

	////////////////////////////////////////////////////////////////////
	////////////////////////////// MATCHERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Matches a plain label
	 *
	 * @return array
	 */
	public function matchPlainLabel()
	{
		return array(
			'tag'        => 'label',
			'attributes' => array('for' => 'foo'),
		);
	}

	/**
	 * Matches a plain label without 'for' attribute
	 *
	 * @return array
	 */
	public function matchPlainLabelWithBS3()
	{
		return array(
			'tag' => 'label',
		);
	}

	/**
	 * Matches an plain text fallback input
	 * Which is a disabled input
	 *
	 * @return array
	 */
	public function matchPlainTextFallbackInput()
	{
		return array(
			'tag'        => 'input',
			'attributes' => array(
				'disabled' => 'disabled',
				'type'     => 'text',
				'name'     => 'foo',
				'value'    => 'bar',
				'id'       => 'foo',
			),
		);
	}

	/**
	 * Matches an plain text input as a p tag
	 *
	 * @return array
	 */
	public function matchPlainTextInput()
	{
		return array(
			'tag'        => 'div',
			'content'    => 'bar',
			'attributes' => array(
				'class' => 'form-control-static',
			),
		);
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// ASSERTIONS //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Matches a Form Static Group
	 *
	 * @param  string $input
	 * @param  string $label
	 *
	 * @return boolean
	 */
	protected function formStaticGroup(
		$input = '<div class="form-control-static" id="foo">bar</div>',
		$label = '<label for="" class="control-label col-lg-2 col-sm-4">Foo</label>'
	) {
		return $this->formGroup($input, $label);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanCreatePlainTextFallbackInputFields()
	{
		$this->former->framework('Nude');
		$nude = $this->former->plaintext('foo')->value('bar')->__toString();

		$this->assertHTML($this->matchPlainLabel(), $nude);
		$this->assertHTML($this->matchPlainTextFallbackInput(), $nude);

		$this->resetLabels();
		$this->former->framework('ZurbFoundation');
		$zurb = $this->former->plaintext('foo')->value('bar')->__toString();

		$this->assertHTML($this->matchPlainLabel(), $zurb);
		$this->assertHTML($this->matchPlainTextFallbackInput(), $zurb);
	}

	public function testCanCreatePlainTextFieldsWithBS3()
	{
		$this->former->framework('TwitterBootstrap3');
		$input = $this->former->plaintext('foo')->value('bar')->__toString();

		$this->assertHTML($this->matchPlainLabelWithBS3(), $input);
		$this->assertHTML($this->matchPlainTextInput(), $input);

		$matcher = $this->formStaticGroup();
		$this->assertEquals($matcher, $input);
	}
}
