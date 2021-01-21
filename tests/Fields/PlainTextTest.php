<?php
namespace Former\Fields;

use Former\TestCases\FormerTests;
use Illuminate\Support\HtmlString;

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
	 * Matches an plain text input as a div tag
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

	/**
	 * Matches an plain text input as a p tag
	 *
	 * @return array
	 */
	public function matchPlainTextInputWithBS4()
	{
	return array(
			'tag'        => 'div',
			'content'    => 'bar',
			'attributes' => array(
				'class' => 'form-control-plaintext',
			),
		);
	}

	/**
	 * Matches an plain text input as a div tag
	 *
	 * @return array
	 */
	public function matchPlainTextInputWithHtmlValueEscaped()
	{
		return array(
			'tag'        => 'div',
			'content'    => '<script>alert(1);</script>',
			'attributes' => array(
				'class' => 'form-control-static',
			),
		);
	}

	/**
	 * Matches an plain text input as a div tag
	 *
	 * @return array
	 */
	public function matchPlainTextInputWithHtmlValue()
	{
		return array(
			'tag'        => 'div',
			'child'      => array(
				'tag'        => 'strong',
				'content'    => 'bar',
			),
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

	/**
	 * Matches a Form Static Group
	 *
	 * @param  string $input
	 * @param  string $label
	 *
	 * @return boolean
	 */
	protected function formStaticGroupForBS4(
		$input = '<div class="form-control-plaintext" id="foo">bar</div>',
		$label = '<label for="" class="col-form-label col-lg-2 col-sm-4">Foo</label>'
	) {
		return $this->formGroupWithBS4($input, $label);
	}

	/**
	 * Matches a Form Static Group with HTML value escaped
	 *
	 * @param  string $input
	 * @param  string $label
	 *
	 * @return boolean
	 */
	protected function formStaticGroupWithHtmlValueEscaped(
		$input = '<div class="form-control-static" id="foo">&lt;script&gt;alert(1);&lt;/script&gt;</div>',
		$label = '<label for="" class="control-label col-lg-2 col-sm-4">Foo</label>'
	) {
		return $this->formGroup($input, $label);
	}

	/**
	 * Matches a Form Static Group with HTML value
	 *
	 * @param  string $input
	 * @param  string $label
	 *
	 * @return boolean
	 */
	protected function formStaticGroupWithHtmlValue(
		$input = '<div class="form-control-static" id="foo"><strong>bar</strong></div>',
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

	public function testCanCreatePlainTextFieldsWithBS4()
	{
		$this->former->framework('TwitterBootstrap4');
		$input = $this->former->plaintext('foo')->value('bar')->__toString();

		$this->assertHTML($this->matchPlainLabelWithBS3(), $input);
		$this->assertHTML($this->matchPlainTextInputWithBS4(), $input);

		$matcher = $this->formStaticGroupForBS4();
		$this->assertEquals($matcher, $input);
	}

	public function testCanCreatePlainTextFieldsWithHtmlValueEscaped()
	{
		$this->former->framework('TwitterBootstrap3');
		$htmlValue = '<script>alert(1);</script>';
		$input = $this->former->plaintext('foo')->value($htmlValue)->__toString();

		$this->assertHTML($this->matchPlainLabelWithBS3(), $input);
		$this->assertHTML($this->matchPlainTextInputWithHtmlValueEscaped(), $input);

		$matcher = $this->formStaticGroupWithHtmlValueEscaped();
		$this->assertEquals($matcher, $input);
	}

	public function testCanCreatePlainTextFieldsWithHtmlValue()
	{
		$this->former->framework('TwitterBootstrap3');
		$htmlValue = new HtmlString('<strong>bar</strong>');
		$input = $this->former->plaintext('foo')->value($htmlValue)->__toString();

		$this->assertHTML($this->matchPlainLabelWithBS3(), $input);
		$this->assertHTML($this->matchPlainTextInputWithHtmlValue(), $input);

		$matcher = $this->formStaticGroupWithHtmlValue();
		$this->assertEquals($matcher, $input);
	}
}
