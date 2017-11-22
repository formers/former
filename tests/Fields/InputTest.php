<?php
namespace Former\Fields;

use Former\Dummy\DummyEloquent;
use Former\TestCases\FormerTests;
use Illuminate\Contracts\Support\Htmlable;

class HtmlValue implements Htmlable {

	private $value;

	function __construct($value)
	{
		$this->value = $value;
	}

	function toHtml() {
		return $this->value;
	}
}

class InputTest extends FormerTests
{
	public function testCanCreateText()
	{
		$input = $this->former->text('foo')->__toString();

		$this->assertControlGroup($input);
		$this->assertHTML($this->matchField(), $input);
	}

	public function testCanCreateTextWithoutLabel()
	{
		$this->mockConfig(array('automatic_label' => false));

		$input      = $this->former->text('foo')->__toString();
		$matchField = array_except($this->matchField(), 'id');

		$this->assertHTML($this->matchControlGroup(), $input);
		$this->assertHTML($matchField, $input);
	}

	public function testCanCreateSingleTextWithoutLabelOnStart()
	{
		$input      = $this->former->text('foo', '')->__toString();
		$matchField = array_except($this->matchField(), 'id');

		$this->assertHTML($this->matchControlGroup(), $input);
		$this->assertHTML($matchField, $input);
	}

	public function testCanCreateSingleTextWithoutLabel()
	{
		$input      = $this->former->text('foo')->label(null)->__toString();
		$matchField = array_except($this->matchField(), 'id');

		$this->assertHTML($this->matchControlGroup(), $input);
		$this->assertHTML($matchField, $input);
	}

	public function testCanCreateSearchField()
	{
		$input      = $this->former->search('foo')->__toString();
		$matchField = $this->matchField();
		array_set($matchField, 'attributes.class', 'search-query');

		$this->assertControlGroup($input);
		$this->assertHTML($matchField, $input);
	}

	public function testCanCreateTextFieldWithoutBootstrap()
	{
		$this->former->framework('Nude');

		$input = $this->former->text('foo')->data('foo')->class('bar')->__toString();
		$label = $this->matchLabel('Foo');
		array_forget($label, 'attributes.class');

		$this->assertHTML($label, $input);
		$this->assertHTML($this->matchField(), $input);
	}

	public function testCanCreateTextFieldWithoutFormInstance()
	{
		$this->former->close();

		$input = $this->former->text('foo')->data('foo')->class('bar')->__toString();

		$label = array('tag' => 'label', 'content' => 'Foo', array('for' => 'foo'));
		$this->assertHTML($label, $input);
		$this->assertHTML($this->matchField(), $input);

		$this->former->horizontal_open();
	}

	public function testCanCreateTextLabel()
	{
		$static                          = $this->former->text('foo')->label('bar', $this->testAttributes)->__toString();
		$label                           = $this->matchLabel('Bar', 'foo');
		$label['attributes']['class']    = 'foo control-label';
		$label['attributes']['data-foo'] = 'bar';
		$this->assertHTML($label, $static);
		$this->assertHTML($this->matchField(), $static);
		$this->assertHTML($this->matchControlGroup(), $static);

		$this->resetLabels();
		$input = $this->former->text('foo', 'bar')->__toString();
		$this->assertHTML($this->matchLabel('Bar', 'foo'), $input);
		$this->assertHTML($this->matchField(), $input);
		$this->assertHTML($this->matchControlGroup(), $input);
	}

	public function testCanCreateTextLabelWithoutUppercase()
	{
		$static = $this->former->text('foo')->label(new HtmlValue('bar'), $this->testAttributes)->__toString();
		$this->assertEquals(
			'<div class="control-group"><label class="foo control-label" data-foo="bar" for="foo">bar</label>' .
			'<div class="controls"><input id="foo" type="text" name="foo"></div></div>', $static);
	}

	public function testCanCreateTextLabelContainingHtml()
	{
		$static = $this->former->text('foo')->label(new HtmlValue('<span>bar</span>'), $this->testAttributes)->__toString();
		$this->assertEquals(
			'<div class="control-group"><label class="foo control-label" data-foo="bar" for="foo"><span>bar</span></label>' .
			'<div class="controls"><input id="foo" type="text" name="foo"></div></div>', $static);
	}

	public function testCanCreateTextLabelWithoutBootstrap()
	{
		$this->former->framework('Nude');

		$static                          = $this->former->text('foo')->label('bar', $this->testAttributes)->__toString();
		$label                           = $this->matchLabel('Bar');
		$label['attributes']['class']    = 'foo';
		$label['attributes']['data-foo'] = 'bar';
		$this->assertHTML($label, $static);
		$this->assertHTML($this->matchField(), $static);

		$input = $this->former->text('foo', 'bar')->__toString();
		$label = $this->matchLabel('Bar');
		unset($label['attributes']['class']);
		$this->assertHTML($label, $static);
		$this->assertHTML($this->matchField(), $static);
	}

	public function testCanCreateWithErrors()
	{
		$this->former->withErrors($this->validator);

		$required = $this->former->text('required')->__toString();
		$matcher  =
			'<div class="control-group error">'.
			'<label for="required" class="control-label">Required</label>'.
			'<div class="controls">'.
			'<input id="required" type="text" name="required">'.
			'<span class="help-inline">The required field is required.</span>'.
			'</div>'.
			'</div>';

		$this->assertEquals($matcher, $required);
	}

	public function testAnonymousFieldHasNoErrors()
	{
		$this->former->withErrors($this->validator);

		$required = $this->former->text()->__toString();
		$matcher  =
			'<div class="control-group">'.
			'<div class="controls">'.
			'<input type="text">'.
			'</div>'.
			'</div>';

		$this->assertEquals($matcher, $required);
	}

	public function testCanDisableErrors()
	{
		$this->mockConfig(array('error_messages' => false));
		$this->former->withErrors($this->validator);

		$required = $this->former->text('required')->__toString();
		$matcher  =
			'<div class="control-group error">'.
			'<label for="required" class="control-label">Required</label>'.
			'<div class="controls">'.
			'<input id="required" type="text" name="required">'.
			'</div>'.
			'</div>';

		$this->assertEquals($matcher, $required);
	}

	public function testCanCreatePopulate()
	{
		$this->former->populate(array('foo' => 'bar'));

		$populate = $this->former->text('foo')->__toString();
		$matcher  = $this->controlGroup('<input id="foo" type="text" name="foo" value="bar">');

		$this->assertEquals($matcher, $populate);
	}

	public function testCanCreatePopulateWithSpecificValue()
	{
		$this->former->populate(array('foo' => 'bar'));
		$this->former->populateField('foo', 'foo');

		$populate = $this->former->text('foo')->__toString();
		$matcher  = $this->controlGroup('<input id="foo" type="text" name="foo" value="foo">');

		$this->assertEquals($matcher, $populate);
	}

	public function testCanCreateNestedRelationships()
	{
		$foo = (object) array('bar' => (object) array('kal' => (object) array('ter' => 'men')));
		$this->former->populate($foo);

		$text    = $this->former->text('bar.kal.ter')->__toString();
		$matcher = $this->controlGroup(
			'<input id="bar.kal.ter" type="text" name="bar.kal.ter" value="men">',
			'<label for="bar.kal.ter" class="control-label">Bar.kal.ter</label>');

		$this->assertEquals($matcher, $text);
	}

	public function testCanCreateNestedRelationshipsRenamedField()
	{
		$foo = (object) array('bar' => (object) array('kal' => (object) array('ter' => 'men')));
		$this->former->populate($foo);

		$text    = $this->former->text('bar.kal.ter')->name('ter')->__toString();
		$matcher = $this->controlGroup(
			'<input id="ter" type="text" name="ter" value="men">',
			'<label for="ter" class="control-label">Ter</label>');

		$this->assertEquals($matcher, $text);
	}

	public function testCanCreateMultipleNestedRelationships()
	{
		for ($i = 0; $i < 2; $i++) {
			$bar[] = (object) array('kal' => 'val'.$i);
		}
		$foo = (object) array('bar' => $bar);
		$this->former->populate($foo);

		$text    = $this->former->text('bar.kal')->__toString();
		$matcher = $this->controlGroup(
			'<input id="bar.kal" type="text" name="bar.kal" value="val0, val1">',
			'<label for="bar.kal" class="control-label">Bar.kal</label>');

		$this->assertEquals($matcher, $text);
	}

	public function testCanCreateNoPopulatingPasswords()
	{
		$this->former->populate(array('foo' => 'bar'));
		$populate = $this->former->password('foo')->__toString();
		$matcher  = $this->controlGroup('<input id="foo" type="password" name="foo">');

		$this->assertEquals($matcher, $populate);
	}

	public function testCanCreateDatalist()
	{
		$datalist = $this->former->text('foo')->useDatalist(array('foo' => 'bar', 'kel' => 'tar'))->__toString();
		$matcher  =
			'<div class="control-group">'.
			'<label for="foo" class="control-label">Foo</label>'.
			'<div class="controls">'.
			'<input list="datalist_foo" id="foo" type="text" name="foo">'.
			'<datalist id="datalist_foo">'.
			'<option value="bar">foo</option>'.
			'<option value="tar">kel</option>'.
			'</datalist>'.
			'</div>'.
			'</div>';

		$this->assertEquals($matcher, $datalist);
	}

	public function testCanCreateDatalistCustomList()
	{
		$datalist = $this->former->text('foo')->list('bar')->useDatalist(array(
			'foo' => 'bar',
			'kel' => 'tar',
		))->__toString();
		$matcher  =
			'<div class="control-group">'.
			'<label for="foo" class="control-label">Foo</label>'.
			'<div class="controls">'.
			'<input list="bar" id="foo" type="text" name="foo">'.
			'<datalist id="bar">'.
			'<option value="bar">foo</option>'.
			'<option value="tar">kel</option>'.
			'</datalist>'.
			'</div>'.
			'</div>';

		$this->assertEquals($matcher, $datalist);
	}

	public function testCanCreateNumberRange()
	{
		$range = $this->former->number('foo')->range(1, 5)->__toString();

		$this->assertContains('min="1" max="5"', $range);
	}

	public function testLabelCastsToString()
	{
		$object = new DummyEloquent(array('name' => 'Bar'));

		$static = $this->former->checkbox('foo')->label($object)->__toString();
		$label  = $this->matchLabel('Bar', 'foo');
		$this->assertHTML($label, $static);
	}

	public function testUnderscoresInLabelsAreConverted()
	{
		$static = $this->former->text('foo')->label('customer_name')->__toString();
		$label  = $this->matchLabel('Customer name', 'foo');
		$this->assertHTML($label, $static);
	}
}
