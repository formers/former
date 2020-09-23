<?php
namespace Former\Fields;

use Former\Dummy\DummyEloquent;
use Former\TestCases\FormerTests;
use Illuminate\Support\Collection;

class ChoiceRadioTest extends FormerTests
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

	protected function choiceRadio($name = 'foo') {
		$func_get_args = func_get_args();
		$ref = new \ReflectionMethod(__METHOD__);

		foreach ($ref->getParameters() as $key => $param) {
			if(!isset($func_get_args[ $key ]) && $param->isDefaultValueAvailable()){
				$func_get_args[ $key ] = $param->getDefaultValue();
			}
		}

		$field = call_user_func_array(array($this->former, 'choice'), $func_get_args);
		return $field->expanded();
	}

	/**
	 * Matches a radio
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
	private function matchRadio(
		$name = 'foo',
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
			'type'     => 'radio',
			'name'     => $name,
			'checked'  => 'checked',
			'value'    => $value,
		);
		$labelAttr = array(
			'for'   => $id,
			'class' => 'radio',
		);
		if ($inline) {
			if ($this->former->framework() === 'TwitterBootstrap3') {
				$labelAttr['class'] = 'radio-inline';
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
			$control = '<div class="radio'.($disabled ? ' disabled' : null).'">'.$control.'</div>';
		}

		return $control;
	}

	/**
	 * Matches a checked radio
	 *
	 * @param  string  $name
	 * @param  string  $label
	 * @param  integer $value
	 * @param  boolean $inline
	 *
	 * @return string
	 */
	private function matchCheckedRadio($name = 'foo', $id = 'foo', $label = null, $value = 1, $inline = false)
	{
		return $this->matchRadio($name, $id, $label, $value, $inline, true);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanCreateASingleRadio()
	{
		$radio = $this->choiceRadio()->choices(array('foo'))->__toString();
		$matcher  = $this->controlGroup($this->matchRadio('foo', 'foo_0', 'foo', 0));

		$this->assertEquals($matcher, $radio);
	}

	public function testCanCreateASingleCheckedRadio()
	{
		$radio = $this->choiceRadio()->choices(array('foo'))->value(array('0'))->__toString();
		$matcher  = $this->controlGroup($this->matchCheckedRadio('foo', 'foo_0', 'foo', 0));

		$this->assertEquals($matcher, $radio);
	}

	public function testCanCreateARadioWithALabel()
	{
		$radio = $this->choiceRadio()->choices(array('foo' => 'Bar'))->__toString();
		$matcher  = $this->controlGroup($this->matchRadio('foo', 'foo_0', 'Bar', 'foo'));

		$this->assertEquals($matcher, $radio);
	}

	public function testCanCreateMultipleRadioes()
	{
		$radioes = $this->choiceRadio()->choices(array('foo', 'bar'))->__toString();
		$matcher    = $this->controlGroup(
			$this->matchRadio('foo', 'foo_0', 'foo', 0)
			.$this->matchRadio('foo', 'foo_1', 'bar', 1)
		);

		$this->assertEquals($matcher, $radioes);
	}

	public function testCanCreateMultipleRadioesWithLabels()
	{
		$radioes = $this->choiceRadio()->choices($this->choices)->__toString();
		$matcher    = $this->controlGroup(
			$this->matchRadio('foo', 'foo_0', 'baz', 0)
			.$this->matchRadio('foo', 'foo_1', 'bar', 'foo')
			.$this->matchRadio('foo', 'foo_2', 'ter', 'kal')
		);

		$this->assertEquals($matcher, $radioes);
	}

	public function testCanCreateInlineRadioes()
	{
		$radioes1 = $this->former->inline_choice('foo')->expanded()->choices($this->choices)->__toString();
		$this->resetLabels();
		$radioes2 = $this->choiceRadio('foo')->inline()->choices($this->choices)->__toString();
		$matcher     = $this->controlGroup(
			$this->matchRadio('foo', 'foo_0', 'baz', 0, true)
			.$this->matchRadio('foo', 'foo_1', 'bar', 'foo', true)
			.$this->matchRadio('foo', 'foo_2', 'ter', 'kal', true)
		);

		$this->assertEquals($matcher, $radioes1);
		$this->assertEquals($matcher, $radioes2);
	}

	public function testCanCreateInlineRadioesTwitterBootstrap3()
	{
		$this->former->framework('TwitterBootstrap3');

		$radioes1 = $this->former->inline_choice('foo')->expanded()->choices($this->choices)->__toString();
		$this->resetLabels();
		$radioes2 = $this->former->choice('foo')->inline()->expanded()->choices($this->choices)->__toString();
		$matcher     = $this->formGroup(
			$this->matchRadio('foo', 'foo_0', 'baz', 0, true)
			.$this->matchRadio('foo', 'foo_1', 'bar', 'foo', true)
			.$this->matchRadio('foo', 'foo_2', 'ter', 'kal', true)
		);

		$this->assertEquals($matcher, $radioes1);
		$this->assertEquals($matcher, $radioes2);
	}

	public function testCanCreateStackedRadioes()
	{
		$radioes1 = $this->former->stacked_choice('foo')->expanded()->choices($this->choices)->__toString();
		$this->resetLabels();
		$radioes2 = $this->former->choice('foo')->expanded()->stacked()->choices($this->choices)->__toString();
		$matcher     = $this->controlGroup(
			$this->matchRadio('foo', 'foo_0', 'baz', 0)
			.$this->matchRadio('foo', 'foo_1', 'bar', 'foo')
			.$this->matchRadio('foo', 'foo_2', 'ter', 'kal')
		);

		$this->assertEquals($matcher, $radioes1);
		$this->assertEquals($matcher, $radioes2);
	}

	public function testCanCreateStackedRadioesTwitterBootstrap3()
	{

		$this->former->framework('TwitterBootstrap3');

		$radioes1 = $this->former->stacked_choice('foo')->expanded()->choices($this->choices)->__toString();
		$this->resetLabels();
		$radioes2 = $this->former->choice('foo')->expanded()->stacked()->choices($this->choices)->__toString();
		$matcher     = $this->formGroup(
			$this->matchRadio('foo', 'foo_0', 'baz', 0)
			.$this->matchRadio('foo', 'foo_1', 'bar', 'foo')
			.$this->matchRadio('foo', 'foo_2', 'ter', 'kal')
		);

		$this->assertEquals($matcher, $radioes1);
		$this->assertEquals($matcher, $radioes2);
	}

	public function testCanCreateMultipleRadioesViaAnArray()
	{
		$this->resetLabels();
		$radioes = $this->choiceRadio('foo')->choices(array('foo' => 'Foo', 'bar' => 'Bar'))->__toString();
		$matcher    = $this->controlGroup(
			$this->matchRadio('foo', 'foo_0', 'Foo', 'foo')
			.$this->matchRadio('foo', 'foo_1', 'Bar', 'bar')
		);

		$this->assertEquals($matcher, $radioes);
	}

	public function testCanCustomizeRadioesViaAnArray()
	{
		$radioes = $this->choiceRadio()->choices($this->checkables)->__toString();
		$matcher    = $this->controlGroup(
			'<label for="foo_0" class="radio">'.
			'<input data-foo="bar" value="bar" id="foo_0" type="radio" name="foo">'.
			'Foo'.
			'</label>'.
			'<label for="bar" class="radio">'.
			'<input data-foo="bar" value="bar" id="bar" type="radio" name="foo">'.
			'Bar'.
			'</label>');

		$this->assertEquals($matcher, $radioes);
	}

	public function testCanCreateMultipleAnonymousRadioes()
	{
		$checkables = $this->checkables;
		unset($checkables['Foo']['name']);
		unset($checkables['Bar']['name']);

		$radioes = $this->choiceRadio()->choices($checkables)->__toString();
		$matcher    = $this->controlGroup(
			'<label for="foo_0" class="radio">'.
			'<input data-foo="bar" value="bar" id="foo_0" type="radio" name="foo">'.
			'Foo'.
			'</label>'.
			'<label for="bar" class="radio">'.
			'<input data-foo="bar" value="bar" id="bar" type="radio" name="foo">'.
			'Bar'.
			'</label>');

		$this->assertEquals($matcher, $radioes);
	}

	public function testCanCheckOneInSeveralRadioes()
	{
		$radioes = $this->choiceRadio()->choices(array('foo', 'bar'))->value('1')->__toString();
		$matcher    = $this->controlGroup(
			$this->matchRadio('foo', 'foo_0', 'foo', 0)
			.$this->matchCheckedRadio('foo', 'foo_1', 'bar', 1)
		);

		$this->assertEquals($matcher, $radioes);
	}

	public function testCanCheckMultipleRadioesAtOnce()
	{
		$radioes = $this->choiceRadio()->choices(array('foo', 'bar'))->value(array(0, 1))->__toString();
		$matcher    = $this->controlGroup(
			$this->matchCheckedRadio('foo', 'foo_0', 'foo', 0)
			.$this->matchCheckedRadio('foo', 'foo_1', 'bar', 1)
		);

		$this->assertEquals($matcher, $radioes);
	}

	public function testCanRepopulateRadioesFromPost()
	{
		$this->request->shouldReceive('input')->with('_token', '', true)->andReturn('');
		$this->request->shouldReceive('input')->with('foo', '', true)->andReturn('0');

		$radioes = $this->choiceRadio()->choices(array('foo', 'bar'))->__toString();
		$matcher    = $this->controlGroup(
			$this->matchCheckedRadio('foo', 'foo_0', 'foo', 0)
			.$this->matchRadio('foo', 'foo_1', 'bar', 1)
		);

		$this->assertEquals($matcher, $radioes);
	}

	public function testCanPopulateRadioesFromAnObject()
	{
		$this->former->populate((object) array('foo' => '0'));

		$radioes = $this->choiceRadio()->choices(array('foo', 'bar'))->__toString();
		$matcher    = $this->controlGroup(
			$this->matchCheckedRadio('foo', 'foo_0', 'foo', 0)
			.$this->matchRadio('foo', 'foo_1', 'bar', 1)
		);

		$this->assertEquals($matcher, $radioes);
	}

	public function testCanPopulateRadioesWithRelations()
	{
		$collection = new Collection(array(
			new DummyEloquent(array('id' => 1, 'name' => 'foo')),
			new DummyEloquent(array('id' => 2, 'name' => 'bar')),
			new DummyEloquent(array('id' => 3, 'name' => 'bar')),
		));

		$this->former->populate($collection);
		$radioes = $this->choiceRadio('roles')->fromQuery($collection)->__toString();
		$matcher    = $this->controlGroup(
			$this->matchRadio('roles', 'roles_0', 'foo', '1')
			.$this->matchRadio('roles', 'roles_1', 'bar', '2')
			.$this->matchRadio('roles', 'roles_2', 'bar', '3')

			,'<label for="roles" class="control-label">Roles</label>'
			,'roles'
		);

		$this->assertEquals($matcher, $radioes);
	}

	public function testCanDecodeCorrectlyRadioes()
	{
		$radio = $this->choiceRadio()->__toString();

		$content = html_entity_decode($radio, ENT_QUOTES, 'UTF-8');

		$this->assertEquals($content, $this->choiceRadio()->__toString());
	}

	public function testCanRecognizeRadioesValidationErrors()
	{
		$this->mockSession(array('foo' => 'bar', 'bar' => 'baz'));
		$this->former->withErrors();

		$radio  = $this->choiceRadio()->choices(array('Value 01', 'Value 02'))->__toString();

		$matcher =
			'<div class="control-group error">'.
			'<label for="foo" class="control-label">Foo</label>'.
			'<div class="controls">'.
			'<div id="foo">'.
			'<label for="foo_0" class="radio">'.
			'<input id="foo_0" type="radio" name="foo" value="0">Value 01'.
			'</label>'.
			'<label for="foo_1" class="radio">'.
			'<input id="foo_1" type="radio" name="foo" value="1">Value 02'.
			'</label>'.
			'</div>'.
			'<span class="help-inline">bar</span>'.
			'</div>'.
			'</div>';

		$this->assertEquals($matcher, $radio);
	}

	public function testDisabled()
	{
		$radio = $this->choiceRadio()->choices(array('foo'))->disabled()->__toString();
		$matcher  = $this->controlGroup($this->matchRadio('foo', 'foo_0', 'foo', 0, false, false, true));
		$this->assertEquals($matcher, $radio);
	}

	public function testDisabledStackedBS3()
	{
		$this->former->framework('TwitterBootstrap3');
		$radio = $this->choiceRadio()->choices(array('foo'))->disabled()->__toString();
		$matcher  = $this->formGroup($this->matchRadio('foo', 'foo_0', 'foo', 0, false, false, true));
		$this->assertEquals($matcher, $radio);
	}

	public function testToStringMagicMethodShouldOnlyReturnString()
	{
		$this->former->group();
		$output = $this->choiceRadio()->choices(array('foo')).'';
		$this->former->closeGroup();

        $this->assertIsString($output);
	}
}
