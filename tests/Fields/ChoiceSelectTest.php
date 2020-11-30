<?php
namespace Former\Fields;

use Former\Dummy\DummyEloquent;
use Former\TestCases\FormerTests;
use HtmlObject\Element;
use Illuminate\Support\Collection;

class ChoiceSelectTest extends FormerTests
{
	/**
	 * An array of dummy options
	 *
	 * @var array
	 */
	private $choices = array(0 => 'baz', 'foo' => 'bar', 'kal' => 'ter');

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testSelect()
	{
		$select  = $this->former->choice('foo')->__toString();
		$matcher = $this->controlGroup('<select id="foo" name="foo"></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testMultiselect()
	{
		$select  = $this->former->choice('foo')->multiple()->__toString();
		$matcher = $this->controlGroup('<select id="foo" multiple name="foo[]"></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testMultiselectOptions()
	{
		$select  = $this->former->choice('foo')->multiple()->choices($this->choices)->value(array('foo', 'kal'))->__toString();
		$matcher = $this->controlGroup('<select id="foo" multiple name="foo[]"><option value="0">baz</option><option value="foo" selected="selected">bar</option><option value="kal" selected="selected">ter</option></select>');
		$this->assertEquals($matcher, $select);
	}

	public function testSelectOptions()
	{
		$select  = $this->former->choice('foo')->choices($this->choices)->__toString();
		$matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">baz</option><option value="foo">bar</option><option value="kal">ter</option></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testGetSelectOptions()
	{
		$select = $this->former->choice('foo')->choices($this->choices);

		foreach ($this->choices as $key => $choice) {
			$choices[] = Element::create('option', $choice, array('value' => $key));
		}

		$this->assertEquals($select->getOptions(), $choices);
	}

	public function testSelectPlaceholder()
	{
		$select  = $this->former->choice('foo')->choices($this->choices)->placeholder('Pick something')->__toString();
		$matcher = $this->controlGroup(
			'<select id="foo" name="foo">'.
			'<option value="" disabled="disabled" selected="selected">Pick something</option>'.
			'<option value="0">baz</option>'.
			'<option value="foo">bar</option>'.
			'<option value="kal">ter</option>'.
			'</select>');

		$this->assertEquals($matcher, $select);
	}

	public function testPlaceholderUnselected()
	{
		$select  = $this->former->choice('foo')->value('foo')->choices($this->choices)->placeholder('Pick something')->__toString();
		$matcher = $this->controlGroup(
			'<select id="foo" name="foo">'.
			'<option value="" disabled="disabled">Pick something</option>'.
			'<option value="0">baz</option>'.
			'<option value="foo" selected="selected">bar</option>'.
			'<option value="kal">ter</option>'.
			'</select>');

		$this->assertEquals($matcher, $select);
	}

    public function testSelectNumeric()
    {
        $select  = $this->former->choice('foo')->value(0)->choices($this->choices)->placeholder('Pick something')->__toString();
        $matcher = $this->controlGroup(
            '<select id="foo" name="foo">'.
            '<option value="" disabled="disabled">Pick something</option>'.
            '<option value="0" selected="selected">baz</option>'.
            '<option value="foo">bar</option>'.
            '<option value="kal">ter</option>'.
            '</select>');

        $this->assertEquals($matcher, $select);
    }

    public function testSelectNumericString()
    {
        $select  = $this->former->choice('foo')->value((string)0)->choices($this->choices)->placeholder('Pick something')->__toString();
        $matcher = $this->controlGroup(
            '<select id="foo" name="foo">'.
            '<option value="" disabled="disabled">Pick something</option>'.
            '<option value="0" selected="selected">baz</option>'.
            '<option value="foo">bar</option>'.
            '<option value="kal">ter</option>'.
            '</select>');

        $this->assertEquals($matcher, $select);
    }

	public function testSelectLang()
	{
		$select  = $this->former->choice('foo')->choices($this->translator->get('pagination'))->value('previous')->__toString();
		$matcher = $this->controlGroup(
			'<select id="foo" name="foo">'.
			'<option value="previous" selected="selected">Previous</option>'.
			'<option value="next">Next</option>'.
			'</select>');

		$this->assertEquals($matcher, $select);
	}

	public function testSelectEloquent()
	{
		for ($i = 0; $i < 2; $i++) {
			$eloquent[] = (object) array('id' => $i, 'foo' => 'bar');
		}
		$select  = $this->former->choice('foo')->fromQuery($eloquent, 'foo')->__toString();
		$matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testSelectEloquentKey()
	{
		for ($i = 0; $i < 2; $i++) {
			$eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
		}
		$select  = $this->former->choice('foo')->fromQuery($eloquent, 'foo', 'age')->__toString();
		$matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testSelectEloquentWrongKey()
	{
		for ($i = 0; $i < 2; $i++) {
			$eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
		}
		$select  = $this->former->choice('foo')->fromQuery($eloquent, 'foo', 'id')->__toString();
		$matcher = $this->controlGroup('<select id="foo" name="foo"><option value="bar">bar</option></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testSelectWithAString()
	{
		$select  = $this->former->choice('foo')->fromQuery('This is not an array', 'foo', 'id')->__toString();
		$matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">This is not an array</option></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testSelectWithAnInteger()
	{
		$select  = $this->former->choice('foo')->fromQuery(456, 'foo', 'id')->__toString();
		$matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">456</option></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testSelectEloquentArray()
	{
		for ($i = 0; $i < 2; $i++) {
			$eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
		}
		$select  = $this->former->choice('foo')->fromQuery($eloquent, 'foo', 'age')->__toString();
		$matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testSelectEloquentArrayWithOptionTextAsFunction()
	{
		$optionTextFunction = function($model) {
			return $model->foo;
		};
		for ($i = 0; $i < 2; $i++) {
			$eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
		}
		$select  = $this->former->choice('foo')->fromQuery($eloquent, $optionTextFunction, 'age')->__toString();
		$matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testSelectEloquentArrayWithOptionAttributes()
	{
		$optionTextFunction = function($model) {
			return $model->foo . $model->age;
		};
		$optionDataFunction = function($model) {
			return $model->foo;
		};
		$optionAttributes = [
			'value' => 'age',
			'data-test' => $optionDataFunction,
		];
		for ($i = 0; $i < 2; $i++) {
			$eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
		}
		$select  = $this->former->choice('foo')->fromQuery($eloquent, $optionTextFunction, $optionAttributes)->__toString();
		$matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0" data-test="bar">bar0</option><option value="1" data-test="bar">bar1</option></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testNestedRelationships()
	{
		for ($i = 0; $i < 2; $i++) {
			$bar[] = (object) array('id' => $i, 'kal' => 'val'.$i);
		}
		$foo = (object) array('bar' => $bar);
		$this->former->populate($foo);

		$select  = $this->former->choice('bar.kal')->__toString();
		$matcher = $this->controlGroup(
			'<select id="bar.kal" name="bar.kal">'.
			'<option value="0">val0</option>'.
			'<option value="1">val1</option>'.
			'</select>',
			'<label for="bar.kal" class="control-label">Bar.kal</label>');

		$this->assertEquals($matcher, $select);
	}

	public function testSelectEloquentMagicMethods()
	{
		for ($i = 0; $i < 2; $i++) {
			$eloquentObject = new DummyEloquent(array('id' => $i, 'name' => 'bar'));
			$eloquent[]     = $eloquentObject;
		}

		$select  = $this->former->choice('foo')->fromQuery($eloquent)->__toString();
		$matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

		$this->assertEquals($matcher, $select);
	}

	public function testSelectOptionsValue()
	{
		$select  = $this->former->choice('foo')->data_foo('bar')->choices($this->choices)->value('kal')->__toString();
		$matcher = $this->controlGroup(
			'<select data-foo="bar" id="foo" name="foo">'.
			'<option value="0">baz</option>'.
			'<option value="foo">bar</option>'.
			'<option value="kal" selected="selected">ter</option>'.
			'</select>');

		$this->assertEquals($matcher, $select);
	}

	public function testSelectOptionsValueMethod()
	{
		$select  = $this->former->choice('foo')->data_foo('bar')->choices($this->choices)->value('kal')->__toString();
		$matcher = $this->controlGroup(
			'<select data-foo="bar" id="foo" name="foo">'.
			'<option value="0">baz</option>'.
			'<option value="foo">bar</option>'.
			'<option value="kal" selected="selected">ter</option>'.
			'</select>');

		$this->assertEquals($matcher, $select);
	}

	public function testCanAddAdditionalOptionsToCreatedSelect()
	{
		$select = $this->former->choice('foo')->addChoice(null)->choices($this->choices);
		$select->addChoice('bis', 'ter');
		$matcher = $this->controlGroup(
			'<select id="foo" name="foo">'.
			'<option value=""></option>'.
			'<option value="0">baz</option>'.
			'<option value="foo">bar</option>'.
			'<option value="kal">ter</option>'.
			'<option value="ter">bis</option>'.
			'</select>');

		$this->assertEquals($matcher, $select->__toString());
	}

	public function testPopulateUnexistingOptionsDoesntThrowError()
	{
		$this->former->populate(array('foo' => 'foo'));
		$select  = $this->former->choice('foo')->choices(array('bar' => 'Bar'));
		$matcher = $this->controlGroup(
			'<select id="foo" name="foo">'.
			'<option value="bar">Bar</option>'.
			'</select>');

		$this->assertEquals($matcher, $select->__toString());
	}

	public function testCanPopulateWithCollections()
	{
		$collection = new Collection(array(
			new DummyEloquent(array('id' => 1, 'name' => 'foo')),
			new DummyEloquent(array('id' => 2, 'name' => 'bar')),
		));

		$select  = $this->former->choice('foo')->fromQuery($collection);
		$matcher = $this->controlGroup(
			'<select id="foo" name="foo">'.
			'<option value="1">foo</option>'.
			'<option value="2">bar</option>'.
			'</select>');

		$this->assertEquals($matcher, $select->__toString());
	}

	public function testCanRenderSelectsDynamically()
	{
		$html[] = $this->former->choice('frmVehicleYears')->label('Vehicle Year')->choices($this->choices)->wrapAndRender();
		$html[] = $this->former->choice('frmVehicleMake')->label('Make')->choices($this->choices)->wrapAndRender();

		$results = implode(' ', $html);
		$this->assertStringContainsString('control-group', $results);
	}

	public function testCanPopulateMultipleSelects()
	{
		$collection = new Collection(array(
			new DummyEloquent(array('id' => 1, 'name' => 'foo')),
			new DummyEloquent(array('id' => 2, 'name' => 'bar')),
			new DummyEloquent(array('id' => 3, 'name' => 'bar')),
		));

		$select  = $this->former->choice('foo')->fromQuery($collection)->value(array(1, 2))->render();
		$matcher =
			'<select id="foo" name="foo">'.
			'<option value="1" selected="selected">foo</option>'.
			'<option value="2" selected="selected">bar</option>'.
			'<option value="3">bar</option>'.
			'</select>';

		$this->assertEquals($matcher, $select);
	}

	public function testCanRepopulateFromCollection()
	{
		$model = new DummyEloquent;

		$collection = new Collection(array(
			new DummyEloquent(array('id' => 1, 'name' => 'foo')),
			new DummyEloquent(array('id' => 2, 'name' => 'bar')),
			new DummyEloquent(array('id' => 3, 'name' => 'bar')),
		));

		/**
		 * $model->roles returns a Collection with id's of 1 and 3, so these ID's should end up selected
		 */
		$this->former->populate($model);
		$select  = $this->former->choice('roles')->fromQuery($collection)->__toString();

		$matcher = $this->controlGroup(
			'<select id="roles" name="roles">'.
			'<option value="1" selected="selected">foo</option>'.
			'<option value="2">bar</option>'.
			'<option value="3" selected="selected">bar</option>'.
			'</select>',
			'<label for="roles" class="control-label">Roles</label>'
			);

		$this->assertEquals($matcher, $select);
	}

	public function testCanRepopulateMultipleSelectsFromPost()
	{
		$options = array(
			'foo' => 'foo_name',
			'bar' => 'bar_name',
			'baz' => 'baz_name',
		);

		$this->request->shouldReceive('input')->with('_token', '', true)->andReturn('foobar');
		$this->request->shouldReceive('input')->with('test', '', true)->andReturn(array('foo', 'bar'));

		$select  = $this->former->choice('test')->multiple()->choices($options)->render();
		$matcher =
			'<select id="test" multiple name="test[]">'.
			'<option value="foo" selected="selected">foo_name</option>'.
			'<option value="bar" selected="selected">bar_name</option>'.
			'<option value="baz">baz_name</option>'.
			'</select>';

		$this->assertEquals($matcher, $select);
	}

	public function testCanCreateRangeSelects()
	{
		$select = $this->former->choice('foo')->range(1, 10);

		$this->assertEquals(range(1, 10), array_keys($select->getChoices()));
		$this->assertStringContainsString('<option value="1">1</option>', $select->render());
		$this->assertStringContainsString('<option value="10">10</option>', $select->render());
	}

	public function testCanCreateSelectGroups()
	{
		$values = array('foo' => array(1 => 'foo', 2 => 'bar'), 'bar' => array(3 => 'foo', 4 => 'bar'));
		$select = $this->former->choice('foo')->choices($values);

		$matcher =
			'<select id="foo" name="foo">'.
			'<optgroup label="foo">'.
			'<option value="1">foo</option><option value="2">bar</option>'.
			'</optgroup>'.
			'<optgroup label="bar">'.
			'<option value="3">foo</option><option value="4">bar</option>'.
			'</optgroup>'.
			'</select>';
		$this->assertEquals($matcher, $select->render());
	}

	public function testCanPopulateSelectGroups()
	{
		$values = array('foo' => array(1 => 'foo', 2 => 'bar'), 'bar' => array(3 => 'foo', 4 => 'bar'));
		$select = $this->former->choice('foo')->choices($values)->value(4);

		$matcher =
			'<select id="foo" name="foo">'.
			'<optgroup label="foo">'.
			'<option value="1">foo</option><option value="2">bar</option>'.
			'</optgroup>'.
			'<optgroup label="bar">'.
			'<option value="3">foo</option><option value="4" selected="selected">bar</option>'.
			'</optgroup>'.
			'</select>';
		$this->assertEquals($matcher, $select->render());
	}

	public function testCanUseEmptyPlaceholders()
	{
		$select = $this->former->choice('foo')->choices(array(
			'' => '',
			0  => 'foo',
			1  => 'bar',
		));

		$matcher = '<select id="foo" name="foo"><option value=""></option><option value="0">foo</option><option value="1">bar</option></select>';

		$this->assertEquals($matcher, $select->render());
	}

	public function testCanPassAttributesToOptions()
	{
		$select = $this->former->choice('foo')->choices(array(
			'foo' => array('value' => 'bar', 'class' => 'myclass'),
			'baz' => array('value' => 'qux', 'class' => 'myclass'),
		))->value('bar');

		$matcher = '<select id="foo" name="foo"><option value="bar" class="myclass" selected="selected">foo</option><option value="qux" class="myclass">baz</option></select>';

		$this->assertEquals($matcher, $select->render());
	}

	public function testCanRepopulateFromPostArrayNotation()
	{
		$options = array('foo', 'bar');
		$this->request->shouldReceive('input')->with('_token', '', true)->andReturn('foobar');
		$this->request->shouldReceive('input')->with('foo', '', true)->andReturn(array(0, 1));

		$select  = $this->former->choice('foo[]')->choices($options);
		$matcher = '<select id="foo[]" name="foo[]"><option value="0" selected="selected">foo</option><option value="1" selected="selected">bar</option></select>';

		$this->assertEquals($matcher, $select->render());
	}

	public function testCanRepopulateFromPostStringIndexedArrayNotation()
	{
		$options = array('foo' => 'foo_name', 'bar' => 'bar_name');
		$this->request->shouldReceive('input')->with('_token', '', true)->andReturn('foobar');
		$this->request->shouldReceive('input')->with('foo', '', true)->andReturn(array('foo', 'bar'));

		$select  = $this->former->choice('foo[]')->choices($options);
		$matcher =
			'<select id="foo[]" name="foo[]">'.
			'<option value="foo" selected="selected">foo_name</option>'.
			'<option value="bar" selected="selected">bar_name</option>'.
			'</select>';

		$this->assertEquals($matcher, $select->render());
	}

	public function testMultiselectRepopulationDoesntCreateOptions()
	{
		$options = array(1 => 'foo', 2 => 'bar');
		$this->request->shouldReceive('input')->with('_token', '', true)->andReturn('foobar');
		$this->request->shouldReceive('input')->with('foo', '', true)->andReturn(array(1));

		$select  = $this->former->choice('foo')->multiple()->choices($options);
		$matcher = '<select id="foo" multiple name="foo[]"><option value="1" selected="selected">foo</option><option value="2">bar</option></select>';

		$this->assertEquals($matcher, $select->render());
	}

	public function testSelectCanPickRightOptionWithOptgroups()
	{
		$items = array(
			'foo' => array(
				1 => 'foo',
			),
			'bar' => array(
				3 => 'bar',
				4 => 'baz',
			),
		);

		$select  = $this->former->choice('category_id')->choices($items)->value(1);
		$matcher = '<optgroup label="foo"><option value="1" selected="selected">foo</option></optgroup><optgroup label="bar"><option value="3">bar</option><option value="4">baz</option></optgroup>';

		$this->assertStringContainsString($matcher, $select->render());
	}
}
