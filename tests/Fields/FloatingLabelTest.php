<?php
namespace Former\Fields;

use Former\TestCases\FormerTests;
use HtmlObject\Element;

class FloatingLabelTest extends FormerTests
{
	/**
	 * An array of dummy options
	 *
	 * @var array
	 */
	protected $options = array('One', 'Two', 'Three');
	public function setUp(): void
	{
		parent::setUp();
		$this->former->framework('TwitterBootstrap5');
		$this->former->vertical_open()->__toString();
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// MATCHERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Matches a floating label
	 *
	 * @return array
	 */
	public function matchFloatingLabel()
	{
		return array(
			'tag' => 'label',
		);
	}

	/**
	 * Matches an input text tag
	 *
	 * @return array
	 */
	public function matchFloatingInputText()
	{
	return array(
			'tag'        => 'input',
			'attributes' => array(
				'id'       => 'foo',
				'class'    => 'form-control',
				'placeholder' => 'Dummy placeholder',
				'type'     => 'text',
				'name'     => 'foo',
			),
		);
	}

	/**
	 * Matches a textarea tag
	 *
	 * @return array
	 */
	public function matchFloatingTextarea()
	{
	return array(
			'tag'        => 'textarea',
			'attributes' => array(
				'id'       => 'foo',
				'class'    => 'form-control',
				'placeholder' => 'Dummy placeholder',
				'name'     => 'foo',
			),
		);
	}

	/**
	 * Matches a select tag
	 *
	 * @return array
	 */
	public function matchFloatingSelect()
	{
	return array(
			'tag'        => 'select',
			'children'	=> array(
				'count' => 4,
				'only' => array('tag' => 'option'),
			),
			'attributes' => array(
				'id'       => 'foo',
				'class'    => 'form-select',
				'name'     => 'foo',
			),
		);
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// ASSERTIONS //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Matches a Form Floating Label Group
	 *
	 * @param  string $input
	 * @param  string $label
	 *
	 * @return boolean
	 */
	protected function formFloatingLabelGroup(
		$input = '<input class="form-control" placeholder="Dummy placeholder" id="foo" type="text" name="foo">',
		$label = '<label for="foo" class="form-label">Foo</label>'
	) {
		return '<div class="mb-3 form-floating">'.$input.$label.'</div>';
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanCreateFloatingLabelInputText()
	{
		$input = $this->former
			->text('foo')
			->placeholder('Dummy placeholder')
			->floatingLabel()
			->__toString();

		$this->assertHTML($this->matchFloatingLabel(), $input);
		$this->assertHTML($this->matchFloatingInputText(), $input);

		$matcher = $this->formFloatingLabelGroup();
		$this->assertEquals($matcher, $input);
	}

	public function testCanCreateFloatingLabelTextarea()
	{
		$textarea = $this->former
			->textarea('foo')
			->placeholder('Dummy placeholder')
			->floatingLabel()
			->__toString();

		$this->assertHTML($this->matchFloatingLabel(), $textarea);
		$this->assertHTML($this->matchFloatingTextarea(), $textarea);

		$matcher = $this->formFloatingLabelGroup('<textarea class="form-control" placeholder="Dummy placeholder" id="foo" name="foo"></textarea>');
		$this->assertEquals($matcher, $textarea);
	}

	public function testCanCreateFloatingLabelSelect()
	{
		$selectElement = $this->former
			->select('foo')
			->options($this->options)
			->placeholder('Choose an option')
			->floatingLabel();

		$select = $selectElement->__toString();

		$this->assertHTML($this->matchFloatingLabel(), $select);
		$this->assertHTML($this->matchFloatingSelect(), $select);

		$placeholderOption = Element::create('option', 'Choose an option', array('value' => '', 'disabled' => 'disabled', 'selected' => 'selected'));

		$options = array($placeholderOption);
		foreach ($this->options as $key => $option) {
			$options[] = Element::create('option', $option, array('value' => $key));
		}
		$this->assertEquals($selectElement->getOptions(), $options);

		$matcher = $this->formFloatingLabelGroup('<select class="form-select" id="foo" name="foo">'.implode('', $options).'</select>');
		$this->assertEquals($matcher, $select);
	}
}
