<?php
namespace Former\Fields;

use Former\TestCases\FormerTests;

class UneditableTest extends FormerTests
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
	 * Matches an uneditable input
	 *
	 * @return array
	 */
	public function matchUneditableInput()
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
	 * Matches an uneditable input as a span
	 *
	 * @return [type] [description]
	 */
	public function matchUneditableSpan()
	{
		return array(
			'tag'        => 'span',
			'content'    => 'bar',
			'attributes' => array(
				'class' => 'uneditable-input',
			),
		);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanCreateClassicDisabledFields()
	{
		$this->former->framework('Nude');
		$nude = $this->former->uneditable('foo')->value('bar')->__toString();

		$this->assertHTML($this->matchPlainLabel(), $nude);
		$this->assertHTML($this->matchUneditableInput(), $nude);

		$this->resetLabels();
		$this->former->framework('ZurbFoundation');
		$zurb = $this->former->uneditable('foo')->value('bar')->__toString();

		$this->assertHTML($this->matchPlainLabel(), $zurb);
		$this->assertHTML($this->matchUneditableInput(), $zurb);
	}

	public function testCanCreateUneditableFieldsWithBootstrap()
	{
		$input = $this->former->uneditable('foo')->value('bar')->__toString();

		$this->assertControlGroup($input);
		$this->assertHTML($this->matchUneditableSpan(), $input);
	}
}
