<?php
namespace Former\Traits;

use Former\TestCases\FormerTests;
use Illuminate\Support\Str;

class FieldTest extends FormerTests
{
	////////////////////////////////////////////////////////////////////
	/////////////////////////// DATA PROVIDERS /////////////////////////
	////////////////////////////////////////////////////////////////////

	public function provideSizes()
	{
		$_sizes = array('mini', 'small', 'medium', 'large', 'xlarge', 'xxlarge', 'span1', 'span6', 'span12', 'foo');
		foreach ($_sizes as $s) {
			$sizes[] = array($s);
		}

		return $sizes;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanCreateFieldsOutsideForms()
	{
		$this->former->close();
		$field = $this->former->text('foo')->raw();

		$this->assertEquals('<input id="foo" type="text" name="foo">', $field->__toString());
	}

	public function testCanChangeTheFieldIdAndKeepLabelInSync()
	{
		$field = $this->former->text('foo');
		$field->id('bar');

		$matcher = '<div class="control-group"><label for="bar" class="control-label">Foo</label><div class="controls"><input id="bar" type="text" name="foo"></div></div>';

		$this->assertEquals($matcher, $field->__toString());
	}

	public function testCanChangeTypeMidCourse()
	{
		$field = $this->former->text('foo')->setType('email');

		$this->assertEquals('email', $field->getType());
	}

	public function testCanRenameField()
	{
		$input   = $this->former->text('foo')->name('bar')->__toString();
		$matcher = $this->controlGroup(
			'<input id="bar" type="text" name="bar">',
			'<label for="bar" class="control-label">Bar</label>');

		$this->assertEquals($matcher, $input);
	}

	public function testCanSetValueOnFields()
	{
		$matcher = $this->controlGroup('<input id="foo" type="text" name="foo" value="bar">');
		$static  = $this->former->text('foo')->value('bar')->__toString();
		$this->assertHTML($this->matchField(), $static);
		$this->assertHTML($this->matchControlGroup(), $static);

		$this->resetLabels();
		$input = $this->former->text('foo', null, 'bar')->__toString();
		$this->assertHTML($this->matchField(), $input);
		$this->assertHTML($this->matchControlGroup(), $input);
	}

	public function testCanForceValueOnFields()
	{
		$this->former->populate(array('foo' => 'unbar'));
		$static  = $this->former->text('foo')->forceValue('bar')->__toString();
		$matcher = $this->controlGroup('<input id="foo" type="text" name="foo" value="bar">');

		$this->assertEquals($matcher, $static);
	}

	public function testCanCreateViaMagicAttribute()
	{
		$static = $this->former->text('foo')->class('foo')->data_bar('bar')->__toString();
		$this->assertHTML($this->matchField(), $static);
		$this->assertHTML($this->matchControlGroup(), $static);

		$this->resetLabels();
		$input = $this->former->text('foo', null, null, array('class' => 'foo', 'data-bar' => 'bar'))->__toString();
		$this->assertHTML($this->matchField(), $input);
		$this->assertHTML($this->matchControlGroup(), $input);
	}

	public function testCanCreateViaMagicAttributeUnvalue()
	{
		$static  = $this->former->text('foo')->require()->__toString();
		$matcher = $this->controlGroup('<input require type="text" name="foo" id="foo">');

		$this->assertHTML($this->matchField(), $static);
		$this->assertHTML($this->matchControlGroup(), $static);
	}

	public function testCanSetAttributes()
	{
		$attributes = array('class' => 'foo', 'data-foo' => 'bar');

		$static = $this->former->text('foo')->require()->setAttributes($attributes)->__toString();

		$field                           = $this->matchField();
		$field['attributes']['require']  = null;
		$field['attributes']['class']    = 'foo';
		$field['attributes']['data-foo'] = 'bar';
		$this->assertHTML($field, $static);
		$this->assertHTML($this->matchControlGroup(), $static);
	}

	public function testCanReplaceAttributes()
	{
		$attributes = array('class' => 'foo', 'data-foo' => 'bar');

		$static  = $this->former->text('foo')->require()->replaceAttributes($attributes)->__toString();
		$matcher = $this->controlGroup('<input class="foo" data-foo="bar" type="text" name="foo" id="foo">');

		$field                           = $this->matchField();
		$field['attributes']['class']    = 'foo';
		$field['attributes']['data-foo'] = 'bar';
		$this->assertHTML($field, $static);
		$this->assertHTML($this->matchControlGroup(), $static);
	}

	public function testCanGetAttribute()
	{
		$former = $this->former->span1_text('name')->foo('bar');

		$this->assertEquals('span1', $former->class);
		$this->assertEquals('bar', $former->foo);
	}

	public function testCanAddClass()
	{
		$matcher = $this->controlGroup('<input class="foo bar" type="text" name="foo" id="foo">');
		$static  = $this->former->text('foo')->class('foo')->addClass('bar')->__toString();
		$this->assertHTML($this->matchControlGroup(), $static);
		$this->assertHTML($this->matchField(), $static);

		$this->resetLabels();
		$input = $this->former->text('foo', null, null, array('class' => 'foo'))->addClass('bar')->__toString();
		$this->assertHTML($this->matchControlGroup(), $input);
		$this->assertHTML($this->matchField(), $input);
	}

	/**
	 * @dataProvider provideSizes
	 */
	public function testCanUseMagicMethods($size)
	{
		$method = $size.'_text';
		$class  = Str::startsWith($size, 'span') ? $size.' ' : 'input-'.$size.' ';
		$static = $this->former->$method('foo')->addClass('bar')->__toString();
		if ($class == 'input-foo ') {
			$class = null;
		}

		$field                        = $this->matchField();
		$field['attributes']['class'] = $class.'bar';
		$this->assertHTML($this->matchControlGroup(), $static);
		$this->assertHTML($field, $static);
	}

	public function testAutomaticLabelsForSingleSelectField()
	{
		$field = $this->former->select('foo');

		$matcher = '<div class="control-group"><label for="foo" class="control-label">Foo</label><div class="controls"><select id="foo" name="foo"></select></div></div>';

		$this->assertEquals($matcher, $field->__toString());
	}

	public function testAutomaticLabelsForMultiSelectField()
	{
		$field = $this->former->select('foo[]');

		$matcher = '<div class="control-group"><label for="foo[]" class="control-label">Foo</label><div class="controls"><select id="foo[]" name="foo[]"></select></div></div>';

		$this->assertEquals($matcher, $field->__toString());
	}

	public function testCantHaveDuplicateIdsForFields()
	{
		$field      = $this->former->text('name')->render();
		$fieldTwo   = $this->former->text('name')->render();
		$fieldThree = $this->former->text('name')->render();

		$this->assertEquals('<input id="name" type="text" name="name">', $field);
		$this->assertEquals('<input id="name-2" type="text" name="name">', $fieldTwo);
		$this->assertEquals('<input id="name-3" type="text" name="name">', $fieldThree);
	}

	public function testCanChangeBindingOfField()
	{
		$this->former->populate(array('bar' => 'unbar'));
		$static  = $this->former->text('foo')->bind('bar')->__toString();
		$matcher = $this->controlGroup('<input id="foo" type="text" name="foo" value="unbar">');

		$this->assertEquals($matcher, $static);
	}
}
