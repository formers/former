<?php
namespace Former\Fields;

use Former\Dummy\DummyEloquent;
use Former\TestCases\FormerTests;

class SwitchTest extends FormerTests
{

	public function setUp(): void
	{
		parent::setUp();
		$this->former->framework('TwitterBootstrap5');
		$this->former->horizontal_open()->__toString();
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// MATCHERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Matches a switch
	 *
	 * @param  string  $name
	 * @param  string  $label
	 * @param  integer $value
	 * @param  boolean $inline
	 * @param  boolean $checked
	 * @param  mixed $disabled If 'disabled', rendered as `disabled="disabled"`. If true, then rendered as `disabled`.
	 * @param  boolean $raw
	 *
	 * @return string
	 */
	private function matchSwitch(
		$name = 'foo',
		$label = null,
		$value = 1,
		$inline = false,
		$checked = false,
		$disabled = false,
		$raw = false
	) {
		$checkAttr = array(
			'class'    => 'form-check-input',
			'disabled' => $disabled === 'disabled' ? 'disabled' : null,
			'id'       => $name,
			'type'     => 'checkbox',
			'name'     => $name,
			'checked'  => 'checked',
			'value'    => $value,
		);
		$labelAttr = array(
			'for'   => $name,
			'class' => 'form-check-label',
		);
		if (!$checked) {
			unset($checkAttr['checked']);
		}
		if (!$disabled) {
			unset($checkAttr['disabled']);
		}

		$switch = '<input'.$this->attributes($checkAttr).'>';

		$controlClasses = 'form-check';
		if ($inline) {
			$controlClasses .= ' form-check-inline';
		}
		$controlClasses .= ' form-switch';

		if ($label) {
			$switch .= '<label'.$this->attributes($labelAttr).'>'.$label.'</label>';
		}

		if ($raw) {
			return $switch;
		}

		return '<div class="'.$controlClasses.'">'.$switch.'</div>';
	}

	/**
	 * Matches a checked switch
	 *
	 * @param  string  $name
	 * @param  string  $label
	 * @param  integer $value
	 * @param  boolean $inline
	 *
	 * @return string
	 */
	private function matchCheckedSwitch($name = 'foo', $label = null, $value = 1, $inline = false)
	{
		return $this->matchSwitch($name, $label, $value, $inline, true);
	}

	/**
	 * Matches a checked switch
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
	private function matchRawSwitch(
		$name = 'foo',
		$label = null,
		$value = 1,
		$inline = false,
		$checked = false,
		$disabled = false
	) {
		return $this->matchSwitch($name, $label, $value, $inline, $checked, $disabled, true);
	}


	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanCreateASingleCheckedSwitch()
	{
		$switch = $this->former->switch('foo')->__toString();
		$matcher  = $this->formGroupWithBS5($this->matchSwitch('foo'));

		$this->assertEquals($matcher, $switch);
	}

	public function testCanCreateASwitchWithALabel()
	{
		$switch  = $this->former->switch('foo')->text('bar')->__toString();
		$matcher = $this->formGroupWithBS5($this->matchSwitch('foo', 'Bar'));

		$this->assertEquals($matcher, $switch);
	}

	public function testCanSetValueOfASingleSwitch()
	{
		$switch  = $this->former->switch('foo')->text('bar')->value('foo')->__toString();
		$matcher = $this->formGroupWithBS5($this->matchSwitch('foo', 'Bar', 'foo'));

		$this->assertEquals($matcher, $switch);
	}

	public function testCanCreateMultipleSwitches()
	{
		$switches = $this->former->switches('foo')->switches('foo', 'bar')->__toString();
		$matcher  = $this->formGroupWithBS5($this->matchSwitch('foo_0', 'Foo').$this->matchSwitch('foo_1', 'Bar'));

		$this->assertEquals($matcher, $switches);
	}

	public function testCanFocusOnASwitch()
	{
		$switches = $this->former->switches('foo')
		                           ->switches('foo', 'bar')
		                           ->on(0)->text('first')->on(1)->text('second')->__toString();

		$matcher  = $this->formGroupWithBS5($this->matchSwitch('foo_0', 'First').$this->matchSwitch('foo_1', 'Second'));

		$this->assertEquals($matcher, $switches);
	}

	public function testCanCreateInlineSwitches()
	{
		$switches1 = $this->former->inline_switches('foo')->switches('foo', 'bar')->__toString();
		$this->resetLabels();
		$switches2 = $this->former->switches('foo')->inline()->switches('foo', 'bar')->__toString();
		$matcher   = $this->formGroupWithBS5($this->matchSwitch('foo_0', 'Foo', 1, true).$this->matchSwitch('foo_1', 'Bar', 1, true));

		$this->assertEquals($matcher, $switches1);
		$this->assertEquals($matcher, $switches2);
	}

	public function testCanCreateStackedSwitches()
	{
		$switches1 = $this->former->stacked_switches('foo')->switches('foo', 'bar')->__toString();
		$this->resetLabels();
		$switches2 = $this->former->switches('foo')->stacked()->switches('foo', 'bar')->__toString();
		$matcher   = $this->formGroupWithBS5($this->matchSwitch('foo_0', 'Foo', 1).$this->matchSwitch('foo_1', 'Bar', 1));

		$this->assertEquals($matcher, $switches1);
		$this->assertEquals($matcher, $switches2);
	}

	public function testCanCreateMultipleSwitchesViaAnArray()
	{
		$this->resetLabels();
		$switches = $this->former->switches('foo')->switches(array('Foo' => 'foo', 'Bar' => 'bar'))->__toString();
		$matcher  = $this->formGroupWithBS5($this->matchSwitch('foo', 'Foo').$this->matchSwitch('bar', 'Bar'));

		$this->assertEquals($matcher, $switches);
	}

	public function testCanCustomizeSwitchesViaAnArray()
	{
		$switches = $this->former->switches('foo')->switches($this->checkables)->__toString();
		$matcher  = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input class="form-check-input" data-foo="bar" value="bar" id="foo" type="checkbox" name="foo">'.
		  		'<label for="foo" class="form-check-label">Foo</label>'.
			'</div>'.
			'<div class="form-check form-switch">'.
				'<input class="form-check-input" data-foo="bar" value="bar" id="bar" type="checkbox" name="foo">'.
				'<label for="bar" class="form-check-label">Bar</label>'.
			'</div>');

		$this->assertEquals($matcher, $switches);
	}

	public function testCanCreateMultipleAnonymousSwitches()
	{
		$checkables = $this->checkables;
		unset($checkables['Foo']['name']);
		unset($checkables['Bar']['name']);

		$switches = $this->former->switches('foo')->switches($checkables)->__toString();
		$matcher  = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input class="form-check-input" data-foo="bar" value="bar" id="foo_0" type="checkbox" name="foo_0">'.
				'<label for="foo_0" class="form-check-label">Foo</label>'.
			'</div>'.
			'<div class="form-check form-switch">'.
				'<input class="form-check-input" data-foo="bar" value="bar" id="bar" type="checkbox" name="foo_1">'.
				'<label for="bar" class="form-check-label">Bar</label>'.
			'</div>'
		);

		$this->assertEquals($matcher, $switches);
	}

	public function testCanCheckASingleSwitch()
	{
		$switch  = $this->former->switch('foo')->check()->__toString();
		$matcher = $this->formGroupWithBS5($this->matchCheckedSwitch('foo'));

		$this->assertEquals($matcher, $switch);
	}

	public function testCanCheckOneInSeveralSwitches()
	{
		$switches = $this->former->switches('foo')->switches('foo', 'bar')->check('foo_1')->__toString();
		$matcher  = $this->formGroupWithBS5($this->matchSwitch('foo_0', 'Foo').$this->matchCheckedSwitch('foo_1', 'Bar'));

		$this->assertEquals($matcher, $switches);
	}

	public function testCanCheckMultipleSwitchesAtOnce()
	{
		$switches = $this->former->switches('foo')->switches('foo', 'bar')->check(array(
			'foo_0' => false,
			'foo_1' => true,
		))->__toString();
		$matcher  = $this->formGroupWithBS5($this->matchSwitch('foo_0', 'Foo').$this->matchCheckedSwitch('foo_1', 'Bar', 1));

		$this->assertEquals($matcher, $switches);
	}

	public function testCanRepopulateSwitchesFromPost()
	{
		$this->request->shouldReceive('input')->with('_token', '', true)->andReturn('');
		$this->request->shouldReceive('input')->with('foo', '', true)->andReturn('');
		$this->request->shouldReceive('input')->with('foo_0', '', true)->andReturn(true);
		$this->request->shouldReceive('input')->with('foo_1', '', true)->andReturn(false);

		$switches = $this->former->switches('foo')->switches('foo', 'bar')->__toString();
		$matcher  = $this->formGroupWithBS5($this->matchCheckedSwitch('foo_0', 'Foo').$this->matchSwitch('foo_1', 'Bar'));

		$this->assertEquals($matcher, $switches);
	}

	public function testCanPopulateSwitchesFromAnObject()
	{
		$this->former->populate((object) array('foo_0' => true));

		$switches = $this->former->switches('foo')->switches('foo', 'bar')->__toString();
		$matcher  = $this->formGroupWithBS5($this->matchCheckedSwitch('foo_0', 'Foo').$this->matchSwitch('foo_1', 'Bar'));

		$this->assertEquals($matcher, $switches);
	}

	public function testCanPopulateSwitchesWithRelations()
	{
		$eloquent = new DummyEloquent(array('id' => 1, 'name' => 3));

		$this->former->populate($eloquent);
		$switches = $this->former->switches('roles')->__toString();
		$matcher  = $this->formGroupWithBS5(
			$this->matchSwitch('1', 'Foo').$this->matchSwitch('3', 'Bar'),
			'<label for="roles" class="col-form-label col-lg-2 col-sm-4 pt-0">Roles</label>');

		$this->assertEquals($matcher, $switches);
	}

	public function testCanRepopulateSwitchesWithRelations()
	{
		$eloquent = new DummyEloquent;

		$roles = array(
			'Value 01' => array(
				'name'  => 'rolesAsCollection[1]',
				'value' => '1',
			),
			'Value 02' => array(
				'name'  => 'rolesAsCollection[2]',
				'value' => '2',
			),
		);

		$this->former->populate($eloquent);
		$switches = $this->former->switches('rolesAsCollection[]')->switches($roles)->__toString();

		$this->assertEquals($this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input class="form-check-input" value="1" id="rolesAsCollection[1]" type="checkbox" name="rolesAsCollection[1]" checked="checked">'.
				'<label for="rolesAsCollection[1]" class="form-check-label">Value 01</label>'.
			'</div>'.
			'<div class="form-check form-switch">'.
				'<input class="form-check-input" value="2" id="rolesAsCollection[2]" type="checkbox" name="rolesAsCollection[2]">'.
				'<label for="rolesAsCollection[2]" class="form-check-label">Value 02</label>'.
			'</div>',
			'<label for="rolesAsCollection" class="col-form-label col-lg-2 col-sm-4 pt-0">RolesAsCollection</label>'), $switches);
	}

	public function testCanDecodeCorrectlySwitches()
	{
		$switch  = $this->former->switch('foo')->__toString();

		$content = html_entity_decode($switch, ENT_QUOTES, 'UTF-8');

		$this->assertEquals($content, $this->former->switch('foo')->__toString());
	}

	public function testCanPushUncheckedSwitches()
	{
		$switch  = $this->former->switch('foo')->text('foo')->push(true);
		$matcher = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input type="hidden" name="foo" value="">'.
				$this->matchRawSwitch('foo').
				'<label for="foo" class="form-check-label">Foo</label>'.
			'</div>');

		$this->assertEquals($matcher, $switch->wrapAndRender());
	}

	public function testCanOverrideGloballyPushedSwitches()
	{
		$this->mockConfig(array('push_checkboxes' => true));
		$switch  = $this->former->switch('foo')->text('foo')->push(false);

		$matcher = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				$this->matchRawSwitch('foo').
				'<label for="foo" class="form-check-label">Foo</label>'.
			'</div>');

		$this->assertEquals($matcher, $switch->wrapAndRender());
	}

	public function testCanPushASingleSwitch()
	{
		$this->mockConfig(array('push_checkboxes' => true));

		$switch  = $this->former->switch('foo')->text('foo')->__toString();
		$matcher = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input type="hidden" name="foo" value="">'.
				$this->matchRawSwitch('foo').
				'<label for="foo" class="form-check-label">Foo</label>'.
			'</div>');

		$this->assertEquals($matcher, $switch);
	}

	public function testCanRepopulateSwitchesOnSubmit()
	{
		$this->mockConfig(array('push_checkboxes' => true));
		$this->request->shouldReceive('input')->andReturn('');

		$switch  = $this->former->switch('foo')->text('foo')->__toString();
		$matcher = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input type="hidden" name="foo" value="">'.
				$this->matchRawSwitch('foo').
				'<label for="foo" class="form-check-label">Foo</label>'.
			'</div>');

		$this->assertEquals($matcher, $switch);
	}


	public function testCanCustomizeTheUncheckedValue()
	{
		$this->mockConfig(array('unchecked_value' => 'unchecked', 'push_checkboxes' => true));

		$switch  = $this->former->switch('foo')->text('foo')->__toString();
		$matcher = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input type="hidden" name="foo" value="unchecked">'.
				$this->matchRawSwitch('foo').
				'<label for="foo" class="form-check-label">Foo</label>'.
			'</div>');

		$this->assertEquals($matcher, $switch);
	}

	public function testCanRecognizeGroupedSwitchesValidationErrors()
	{
		$this->mockSession(array('foo' => 'bar', 'bar' => 'baz'));
		$this->former->withErrors();

		$auto  = $this->former->switches('foo[]', '')->switches('Value 01', 'Value 02')->__toString();
		$chain = $this->former->switches('foo', '')->grouped()->switches('Value 01', 'Value 02')->__toString();

		$matcher =
			'<div class="mb-3 row is-invalid">'.
			 	'<div class="col-lg-10 col-sm-8">'.
					'<div class="form-check form-switch">'.
						'<input class="form-check-input is-invalid" id="foo_0" type="checkbox" name="foo[]" value="0">'.
						'<label for="foo_0" class="form-check-label">Value 01</label>'.
					'</div>'.
					'<div class="form-check form-switch">'.
						'<input class="form-check-input is-invalid" id="foo_1" type="checkbox" name="foo[]" value="1">'.
						'<label for="foo_1" class="form-check-label">Value 02</label>'.
					'</div>'.
					'<div>'.
						'<input type="hidden" class="form-check-input is-invalid">'.
						'<div class="invalid-feedback">bar</div>'.
					'</div>'.
				'</div>'.
			'</div>';

		$this->assertEquals($matcher, $auto);
		$this->assertEquals($matcher, $chain);
	}

	public function testCanHandleAZeroUncheckedValue()
	{
		$this->mockConfig(array('unchecked_value' => 0));
		$switches = $this->former->switches('foo')->value('bar')->__toString();
		$matcher  = $this->formGroupWithBS5($this->matchSwitch('foo', null, 'bar'));

		$this->assertEquals($matcher, $switches);
	}

	public function testRepopulatedValueDoesntChangeOriginalValue()
	{
		$this->markTestSkipped('Test reformulated proves opposite of that stated');

		$this->former->populate(array('foo' => 'bar'));
		$switchTrue  = $this->former->switch('foo')->__toString();
		$matcherTrue = $this->formGroupWithBS5($this->matchCheckedSwitch());

		$this->assertEquals($matcherTrue, $switchTrue);

		$this->former->populate(array('foo' => 'baz'));
		$switchFalse = $this->former->switch('foo')->__toString();
		$matcherFalse  = $this->formGroupWithBS5($this->matchSwitch());

		$this->assertEquals($matcherFalse, $switchFalse);
	}

	public function testCanPushSwitchesWithoutLabels()
	{
		$this->mockConfig(array('automatic_label' => false, 'push_checkboxes' => true));

		$html = $this->former->label('<b>Views per Page</b>')->render();
		$html .= $this->former->switch('per_page')->class('input')->render();

		$this->assertIsString($html);
	}

	public function testDisabled()
	{
		$switch  = $this->former->switch('foo')->disabled()->__toString();
		$matcher = $this->formGroupWithBS5($this->matchSwitch('foo', null, 1, false, false, true));
		$this->assertEquals($matcher, $switch);
	}

	public function testToStringMagicMethodShouldOnlyReturnString()
	{
		$this->former->group();
		$output = $this->former->switch('foo')->text('bar').'';
		$this->former->closeGroup();

		$this->assertIsString($output);
	}

	public function testCanBeManualyDefinied()
	{
		$switch  = $this->former->switch('foo', null, 'foo', ['value' => 'bar'])->__toString();
		$matcher = $this->formGroupWithBS5('<div class="form-check form-switch"><input value="bar" class="form-check-input" id="foo" type="checkbox" name="foo"></div>');

		$this->assertEquals($matcher, $switch);
	}

	public function testCanBeManualyDefiniedAndRepopulated()
	{
		$this->former->populate(array('foo' => 'bar'));
		$switch  = $this->former->switch('foo', null, 'foo', ['value' => 'bar'])->__toString();
		$matcher = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input value="bar" class="form-check-input" id="foo" type="checkbox" name="foo" checked="checked">'.
			'</div>');

		$this->assertEquals($matcher, $switch);
	}

	public function testShouldNotBeCheckedIfHisValueIsManualyChanged()
	{
		$this->former->populate(array('foo' => 'foo'));
		$switch  = $this->former->switch('foo', null, 'foo', ['value' => 'bar'])->__toString();
		$matcher = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input value="bar" class="form-check-input" id="foo" type="checkbox" name="foo">'.
			'</div>');

		$this->assertEquals($matcher, $switch);
	}

	public function testNestedCanSetNonOverriddenConstructorParameters()
	{
		$switch  = $this->former->switch('foo[bar]', 'Foo Bar Label', 'bis', ['class' => 'ter'])->__toString();
		$matcher = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input class="ter form-check-input" id="foo[bar]" type="checkbox" name="foo[bar]" value="bis">'.
			'</div>',
			'<label for="foo[bar]" class="col-form-label col-lg-2 col-sm-4 pt-0">Foo Bar Label</label>'
		);

		$this->assertEquals($matcher, $switch);
	}

	public function testNestedCanSetNonOverriddenConstructorParametersAndBeRepopulated()
	{
		$this->former->populate(array('foo' => array('bar' => 'bis')));
		$switch  = $this->former->switch('foo[bar]', 'Foo Bar Label', 'bis', ['class' => 'ter'])->__toString();
		$matcher = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input class="ter form-check-input" id="foo[bar]" type="checkbox" name="foo[bar]" checked="checked" value="bis">'.
			'</div>',
			'<label for="foo[bar]" class="col-form-label col-lg-2 col-sm-4 pt-0">Foo Bar Label</label>'
		);

		$this->assertEquals($matcher, $switch);
	}

	public function testNestedShouldNotBeCheckedIfContstructorValueParameterIsOverridden()
	{
		$this->former->populate(array('foo' => array('bar' => 'bis')));
		$switch  = $this->former->switch('foo[bar]', null, 'bis', ['value' => 'ter'])->__toString();
		$matcher = $this->formGroupWithBS5(
			'<div class="form-check form-switch">'.
				'<input value="ter" class="form-check-input" id="foo[bar]" type="checkbox" name="foo[bar]">'.
			'</div>',
			'<label for="foo[bar]" class="col-form-label col-lg-2 col-sm-4 pt-0">Foo[bar]</label>'
		);

		$this->assertEquals($matcher, $switch);
	}
}
