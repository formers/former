<?php
namespace Former\Fields;

use Former\Dummy\DummyEloquent;
use Former\TestCases\FormerTests;
use Illuminate\Support\Collection;

class ChoiceCheckboxTest extends FormerTests
{
	/**
	 * An array of dummy options
	 *
	 * @var array
	 */
	private $choices = array(0 => 'baz', 'foo' => 'bar', 'kal' => 'ter');

	////////////////////////////////////////////////////////////////////
	////////////////////////////// MATCHERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Matches a Control Group
	 *
	 * @param  string $input
	 * @param  string $label
	 *
	 * @return boolean
	 */
	protected function controlGroup(
		$input = '<input type="text" name="foo" id="foo">',
		$label = '<label for="foo" class="control-label">Foo</label>',
		$id = 'foo'
	) {
		return parent::controlGroup('<div id="'.$id.'">'.$input.'</div>', $label);
	}

	/**
	 * Matches a Form Group
	 *
	 * @param  string $input
	 * @param  string $label
	 *
	 * @return boolean
	 */
	protected function formGroup(
		$input = '<input type="text" name="foo" id="foo">',
		$label = '<label for="foo" class="control-label col-lg-2 col-sm-4">Foo</label>',
		$id = 'foo'
	) {
		return parent::formGroup('<div id="'.$id.'">'.$input.'</div>', $label);
	}

	protected function choiceCheckbox($name = 'foo') {
		$func_get_args = func_get_args();
		$ref = new \ReflectionMethod(__METHOD__);

		foreach ($ref->getParameters() as $key => $param) {
			if(!isset($func_get_args[ $key ]) && $param->isDefaultValueAvailable()){
				$func_get_args[ $key ] = $param->getDefaultValue();
			}
		}

		$field = call_user_func_array(array($this->former, 'choice'), $func_get_args);
		return $field->multiple()->expanded();
	}

	/**
	 * Matches a checkbox
	 *
	 * @param  string  $name
	 * @param  string  $label
	 * @param  integer $value
	 * @param  boolean $inline
	 * @param  boolean $checked
	 * @param  mixed $disabled If 'disabled', rendered as `disabled="disabled"`. If true, then rendered as `disabled`.
	 *
	 * @return string
	 */
	private function matchCheckbox(
		$name = 'foo[]',
		$id = 'foo',
		$label = null,
		$value = 1,
		$inline = false,
		$checked = false,
		$disabled = false
	) {
		$checkAttr = array(
			'disabled' => $disabled === 'disabled' ? 'disabled' : null,
			'id'       => $id,
			'type'     => 'checkbox',
			'name'     => $name,
			'checked'  => 'checked',
			'value'    => $value,
		);
		$labelAttr = array(
			'for'   => $id,
			'class' => 'checkbox',
		);
		if ($inline) {
			if ($this->former->framework() === 'TwitterBootstrap3') {
				$labelAttr['class'] = 'checkbox-inline';
			} else {
				$labelAttr['class'] .= ' inline';
			}
		}
		if (!$checked) {
			unset($checkAttr['checked']);
		}
		if (!$disabled) {
			unset($checkAttr['disabled']);
		}

		$radio = '<input'.$this->attributes($checkAttr).'>';

		if (!$inline && $this->former->framework() === 'TwitterBootstrap3') {
			$labelAttr['class'] = "";
		}

		$control = $label ? '<label'.$this->attributes($labelAttr).'>'.$radio.$label.'</label>' : $radio;

		if (!$inline && $this->former->framework() === 'TwitterBootstrap3') {
			$control = '<div class="checkbox'.($disabled ? ' disabled' : null).'">'.$control.'</div>';
		}

		return $control;
	}

	/**
	 * Matches a checked checkbox
	 *
	 * @param  string  $name
	 * @param  string  $label
	 * @param  integer $value
	 * @param  boolean $inline
	 *
	 * @return string
	 */
	private function matchCheckedCheckbox($name = 'foo[]', $id = 'foo', $label = null, $value = 1, $inline = false)
	{
		return $this->matchCheckbox($name, $id, $label, $value, $inline, true);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanCreateASingleCheckbox()
	{
		$checkbox = $this->choiceCheckbox()->choices(array('foo'))->__toString();
		$matcher  = $this->controlGroup($this->matchCheckbox('foo[]', 'foo_0', 'foo', 0));

		$this->assertEquals($matcher, $checkbox);
	}

	public function testCanCreateASingleCheckedCheckbox()
	{
		$checkbox = $this->choiceCheckbox()->choices(array('foo'))->value(array('0'))->__toString();
		$matcher  = $this->controlGroup($this->matchCheckedCheckbox('foo[]', 'foo_0', 'foo', 0));

		$this->assertEquals($matcher, $checkbox);
	}

	public function testCanCreateACheckboxWithALabel()
	{
		$checkbox = $this->choiceCheckbox()->choices(array('foo' => 'Bar'))->__toString();
		$matcher  = $this->controlGroup($this->matchCheckbox('foo[]', 'foo_0', 'Bar', 'foo'));

		$this->assertEquals($matcher, $checkbox);
	}

	public function testCanCreateMultipleCheckboxes()
	{
		$checkboxes = $this->choiceCheckbox()->choices(array('foo', 'bar'))->__toString();
		$matcher    = $this->controlGroup(
			$this->matchCheckbox('foo[]', 'foo_0', 'foo', 0)
			.$this->matchCheckbox('foo[]', 'foo_1', 'bar', 1)
		);

		$this->assertEquals($matcher, $checkboxes);
	}

	public function testCanCreateMultipleCheckboxesWithLabels()
	{
		$checkboxes = $this->choiceCheckbox()->choices($this->choices)->__toString();
		$matcher    = $this->controlGroup(
			$this->matchCheckbox('foo[]', 'foo_0', 'baz', 0)
			.$this->matchCheckbox('foo[]', 'foo_1', 'bar', 'foo')
			.$this->matchCheckbox('foo[]', 'foo_2', 'ter', 'kal')
		);

		$this->assertEquals($matcher, $checkboxes);
	}

	public function testCanCreateInlineCheckboxes()
	{
		$checkboxes1 = $this->former->inline_choice('foo')->multiple()->expanded()->choices($this->choices)->__toString();
		$this->resetLabels();
		$checkboxes2 = $this->choiceCheckbox('foo')->inline()->choices($this->choices)->__toString();
		$matcher     = $this->controlGroup(
			$this->matchCheckbox('foo[]', 'foo_0', 'baz', 0, true)
			.$this->matchCheckbox('foo[]', 'foo_1', 'bar', 'foo', true)
			.$this->matchCheckbox('foo[]', 'foo_2', 'ter', 'kal', true)
		);

		$this->assertEquals($matcher, $checkboxes1);
		$this->assertEquals($matcher, $checkboxes2);
	}

	public function testCanCreateInlineCheckboxesTwitterBootstrap3()
	{
		$this->former->framework('TwitterBootstrap3');

		$checkboxes1 = $this->former->inline_choice('foo')->multiple()->expanded()->choices($this->choices)->__toString();
		$this->resetLabels();
		$checkboxes2 = $this->former->choice('foo')->inline()->multiple()->expanded()->choices($this->choices)->__toString();
		$matcher     = $this->formGroup(
			$this->matchCheckbox('foo[]', 'foo_0', 'baz', 0, true)
			.$this->matchCheckbox('foo[]', 'foo_1', 'bar', 'foo', true)
			.$this->matchCheckbox('foo[]', 'foo_2', 'ter', 'kal', true)
		);

		$this->assertEquals($matcher, $checkboxes1);
		$this->assertEquals($matcher, $checkboxes2);
	}

	public function testCanCreateStackedCheckboxes()
	{
		$checkboxes1 = $this->former->stacked_choice('foo')->multiple()->expanded()->choices($this->choices)->__toString();
		$this->resetLabels();
		$checkboxes2 = $this->former->choice('foo')->multiple()->expanded()->stacked()->choices($this->choices)->__toString();
		$matcher     = $this->controlGroup(
			$this->matchCheckbox('foo[]', 'foo_0', 'baz', 0)
			.$this->matchCheckbox('foo[]', 'foo_1', 'bar', 'foo')
			.$this->matchCheckbox('foo[]', 'foo_2', 'ter', 'kal')
		);

		$this->assertEquals($matcher, $checkboxes1);
		$this->assertEquals($matcher, $checkboxes2);
	}

	public function testCanCreateStackedCheckboxesTwitterBootstrap3()
	{

		$this->former->framework('TwitterBootstrap3');

		$checkboxes1 = $this->former->stacked_choice('foo')->multiple()->expanded()->choices($this->choices)->__toString();
		$this->resetLabels();
		$checkboxes2 = $this->former->choice('foo')->multiple()->expanded()->stacked()->choices($this->choices)->__toString();
		$matcher     = $this->formGroup(
			$this->matchCheckbox('foo[]', 'foo_0', 'baz', 0)
			.$this->matchCheckbox('foo[]', 'foo_1', 'bar', 'foo')
			.$this->matchCheckbox('foo[]', 'foo_2', 'ter', 'kal')
		);

		$this->assertEquals($matcher, $checkboxes1);
		$this->assertEquals($matcher, $checkboxes2);
	}

	public function testCanCreateMultipleCheckboxesViaAnArray()
	{
		$this->resetLabels();
		$checkboxes = $this->choiceCheckbox('foo')->choices(array('foo' => 'Foo', 'bar' => 'Bar'))->__toString();
		$matcher    = $this->controlGroup(
			$this->matchCheckbox('foo[]', 'foo_0', 'Foo', 'foo')
			.$this->matchCheckbox('foo[]', 'foo_1', 'Bar', 'bar')
		);

		$this->assertEquals($matcher, $checkboxes);
	}

	public function testCanCustomizeCheckboxesViaAnArray()
	{
		$checkboxes = $this->choiceCheckbox()->choices($this->checkables)->__toString();
		$matcher    = $this->controlGroup(
			'<label for="foo_0" class="checkbox">'.
			'<input data-foo="bar" value="bar" id="foo_0" type="checkbox" name="foo[]">'.
			'Foo'.
			'</label>'.
			'<label for="bar" class="checkbox">'.
			'<input data-foo="bar" value="bar" id="bar" type="checkbox" name="foo[]">'.
			'Bar'.
			'</label>');

		$this->assertEquals($matcher, $checkboxes);
	}

	public function testCanCreateMultipleAnonymousCheckboxes()
	{
		$checkables = $this->checkables;
		unset($checkables['Foo']['name']);
		unset($checkables['Bar']['name']);

		$checkboxes = $this->choiceCheckbox()->choices($checkables)->__toString();
		$matcher    = $this->controlGroup(
			'<label for="foo_0" class="checkbox">'.
			'<input data-foo="bar" value="bar" id="foo_0" type="checkbox" name="foo[]">'.
			'Foo'.
			'</label>'.
			'<label for="bar" class="checkbox">'.
			'<input data-foo="bar" value="bar" id="bar" type="checkbox" name="foo[]">'.
			'Bar'.
			'</label>');

		$this->assertEquals($matcher, $checkboxes);
	}

	public function testCanCheckOneInSeveralCheckboxes()
	{
		$checkboxes = $this->choiceCheckbox()->choices(array('foo', 'bar'))->value('1')->__toString();
		$matcher    = $this->controlGroup(
			$this->matchCheckbox('foo[]', 'foo_0', 'foo', 0)
			.$this->matchCheckedCheckbox('foo[]', 'foo_1', 'bar', 1)
		);

		$this->assertEquals($matcher, $checkboxes);
	}

	public function testCanCheckMultipleCheckboxesAtOnce()
	{
		$checkboxes = $this->choiceCheckbox()->choices(array('foo', 'bar'))->value(array(0, 1))->__toString();
		$matcher    = $this->controlGroup(
			$this->matchCheckedCheckbox('foo[]', 'foo_0', 'foo', 0)
			.$this->matchCheckedCheckbox('foo[]', 'foo_1', 'bar', 1)
		);

		$this->assertEquals($matcher, $checkboxes);
	}

	public function testCanRepopulateCheckboxesFromPost()
	{
		$this->request->shouldReceive('input')->with('_token', '', true)->andReturn('');
		$this->request->shouldReceive('input')->with('foo', '', true)->andReturn('0');

		$checkboxes = $this->choiceCheckbox()->choices(array('foo', 'bar'))->__toString();
		$matcher    = $this->controlGroup(
			$this->matchCheckedCheckbox('foo[]', 'foo_0', 'foo', 0)
			.$this->matchCheckbox('foo[]', 'foo_1', 'bar', 1)
		);

		$this->assertEquals($matcher, $checkboxes);
	}

	public function testCanPopulateCheckboxesFromAnObject()
	{
		$this->former->populate((object) array('foo' => '0'));

		$checkboxes = $this->choiceCheckbox()->choices(array('foo', 'bar'))->__toString();
		$matcher    = $this->controlGroup(
			$this->matchCheckedCheckbox('foo[]', 'foo_0', 'foo', 0)
			.$this->matchCheckbox('foo[]', 'foo_1', 'bar', 1)
		);

		$this->assertEquals($matcher, $checkboxes);
	}

	public function testCanPopulateCheckboxesWithRelations()
	{
		$collection = new Collection(array(
			new DummyEloquent(array('id' => 1, 'name' => 'foo')),
			new DummyEloquent(array('id' => 2, 'name' => 'bar')),
			new DummyEloquent(array('id' => 3, 'name' => 'bar')),
		));

		$this->former->populate($collection);
		$checkboxes = $this->choiceCheckbox('roles')->fromQuery($collection)->__toString();
		$matcher    = $this->controlGroup(
			$this->matchCheckbox('roles[]', 'roles_0', 'foo', '1')
			.$this->matchCheckbox('roles[]', 'roles_1', 'bar', '2')
			.$this->matchCheckbox('roles[]', 'roles_2', 'bar', '3')

			,'<label for="roles" class="control-label">Roles</label>'
			,'roles'
		);

		$this->assertEquals($matcher, $checkboxes);
	}

	public function testCanDecodeCorrectlyCheckboxes()
	{
		$checkbox = $this->choiceCheckbox()->__toString();

		$content = html_entity_decode($checkbox, ENT_QUOTES, 'UTF-8');

		$this->assertEquals($content, $this->choiceCheckbox()->__toString());
	}

	public function testCanRecognizeCheckboxesValidationErrors()
	{
		$this->mockSession(array('foo' => 'bar', 'bar' => 'baz'));
		$this->former->withErrors();

		$checkbox  = $this->choiceCheckbox()->choices(array('Value 01', 'Value 02'))->__toString();

		$matcher =
			'<div class="control-group error">'.
			'<label for="foo" class="control-label">Foo</label>'.
			'<div class="controls">'.
			'<div id="foo">'.
			'<label for="foo_0" class="checkbox">'.
			'<input id="foo_0" type="checkbox" name="foo[]" value="0">Value 01'.
			'</label>'.
			'<label for="foo_1" class="checkbox">'.
			'<input id="foo_1" type="checkbox" name="foo[]" value="1">Value 02'.
			'</label>'.
			'</div>'.
			'<span class="help-inline">bar</span>'.
			'</div>'.
			'</div>';

		$this->assertEquals($matcher, $checkbox);
	}

	public function testDisabled()
	{
		$checkbox = $this->choiceCheckbox()->choices(array('foo'))->disabled()->__toString();
		$matcher  = $this->controlGroup($this->matchCheckbox('foo[]', 'foo_0', 'foo', 0, false, false, true));
		$this->assertEquals($matcher, $checkbox);
	}

	public function testDisabledStackedBS3()
	{
		$this->former->framework('TwitterBootstrap3');
		$checkbox = $this->choiceCheckbox()->choices(array('foo'))->disabled()->__toString();
		$matcher  = $this->formGroup($this->matchCheckbox('foo[]', 'foo_0', 'foo', 0, false, false, true));
		$this->assertEquals($matcher, $checkbox);
	}

	public function testToStringMagicMethodShouldOnlyReturnString()
	{
		$this->former->group();
		$output = $this->choiceCheckbox()->choices(array('foo')).'';
		$this->former->closeGroup();

		$this->assertIsString($output);
	}
}
