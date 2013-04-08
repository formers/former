<?php
use Underscore\Methods\StringMethods as String;
use Underscore\Methods\ArraysMethods as Arrays;

class InputTest extends FormerTests
{
  public function tearDown()
  {
    parent::tearDown();
    $this->former->framework('TwitterBootstrap');
  }

  public function sizes()
  {
    $_sizes = array('mini', 'small', 'medium', 'large', 'xlarge', 'xxlarge', 'span1', 'span6', 'span12', 'foo');
    foreach($_sizes as $s) $sizes[] = array($s);

    return $sizes;
  }

  // Tests --------------------------------------------------------- /

  public function testText()
  {
    $input = $this->former->text('foo')->__toString();

    $this->assertControlGroup($input);
    $this->assertHTML($this->matchField(), $input);
  }

  public function testTextWithoutLabel()
  {
    $this->app->app['config'] = $this->app->getConfig(true, '', false, false);

    $input = $this->former->text('foo')->__toString();
    $matchField = Arrays::remove($this->matchField(), 'id');

    $this->assertHTML($this->matchControlGroup(), $input);
    $this->assertHTML($matchField, $input);
  }

  public function testSingleTextWithoutLabelOnStart()
  {
    $input = $this->former->text('foo', '')->__toString();
    $matchField = Arrays::remove($this->matchField(), 'id');

    $this->assertHTML($this->matchControlGroup(), $input);
    $this->assertHTML($matchField, $input);
  }

  public function testSingleTextWithoutLabel()
  {
    $input = $this->former->text('foo')->label(null)->__toString();
    $matchField = Arrays::remove($this->matchField(), 'id');

    $this->assertHTML($this->matchControlGroup(), $input);
    $this->assertHTML($matchField, $input);
  }

  public function testSearch()
  {
    $input = $this->former->search('foo')->__toString();
    $matchField = Arrays::set($this->matchField(), 'attributes.class', 'search-query');

    $this->assertControlGroup($input);
    $this->assertHTML($matchField, $input);
  }

  public function testTextWithoutBootstrap()
  {
    $this->former->framework('Nude');

    $input = $this->former->text('foo')->data('foo')->class('bar')->__toString();
    $label = Arrays::remove($this->matchLabel(), 'attributes.class');

    $this->assertHTML($label, $input);
    $this->assertHTML($this->matchField(), $input);
  }

  public function testTextWithoutFormInstance()
  {
    $this->former->close();

    $input = $this->former->text('foo')->data('foo')->class('bar')->__toString();

    $label = array('tag' => 'label', 'content' => 'Foo', array('for' => 'foo'));
    $this->assertHTML($label, $input);
    $this->assertHTML($this->matchField(), $input);

    $this->former->horizontal_open();
  }

  public function testTextLabel()
  {
    $static  = $this->former->text('foo')->label('bar', $this->testAttributes)->__toString();
    $label = $this->matchLabel('Bar', 'foo');
    $label['attributes']['class'] = 'foo control-label';
    $label['attributes']['data-foo'] = 'bar';
    $this->assertHTML($label, $static);
    $this->assertHTML($this->matchField(), $static);
    $this->assertHTML($this->matchControlGroup(), $static);

    $input   = $this->former->text('foo', 'bar')->__toString();
    $this->assertHTML($this->matchLabel('Bar', 'foo'), $input);
    $this->assertHTML($this->matchField(), $input);
    $this->assertHTML($this->matchControlGroup(), $input);
  }

  public function testTextLabelWithoutBootstrap()
  {
    $this->former->framework('Nude');

    $static = $this->former->text('foo')->label('bar', $this->testAttributes)->__toString();
    $label = $this->matchLabel('Bar');
    $label['attributes']['class'] = 'foo';
    $label['attributes']['data-foo'] = 'bar';
    $this->assertHTML($label, $static);
    $this->assertHTML($this->matchField(), $static);

    $input  = $this->former->text('foo', 'bar')->__toString();
    $label = $this->matchLabel('Bar');
    unset($label['attributes']['class']);
    $this->assertHTML($label, $static);
    $this->assertHTML($this->matchField(), $static);
  }

  public function testRenameField()
  {
    $input = $this->former->text('foo')->name('bar')->__toString();
    $matcher = $this->controlGroup(
      '<input id="bar" type="text" name="bar">',
      '<label for="bar" class="control-label">Bar</label>');

    $this->assertEquals($matcher, $input);
  }

  public function testValue()
  {
    $static = $this->former->text('foo')->value('bar')->__toString();
    $input  = $this->former->text('foo', null, 'bar')->__toString();
    $matcher = $this->controlGroup('<input id="foo" type="text" name="foo" value="bar">');

    $this->assertHTML($this->matchField(), $static);
    $this->assertHTML($this->matchControlGroup(), $static);
    $this->assertHTML($this->matchField(), $input);
    $this->assertHTML($this->matchControlGroup(), $input);
  }

  public function testForceValue()
  {
    $this->former->populate(array('foo' => 'unbar'));
    $static = $this->former->text('foo')->forceValue('bar')->__toString();
    $matcher = $this->controlGroup('<input id="foo" type="text" name="foo" value="bar">');

    $this->assertEquals($matcher, $static);
  }

  public function testMagicAttribute()
  {
    $static = $this->former->text('foo')->class('foo')->data_bar('bar')->__toString();
    $input  = $this->former->text('foo', null, null, array('class' => 'foo', 'data-bar' => 'bar'))->__toString();

    $this->assertHTML($this->matchField(), $static);
    $this->assertHTML($this->matchControlGroup(), $static);
    $this->assertHTML($this->matchField(), $input);
    $this->assertHTML($this->matchControlGroup(), $input);
  }

  public function testMagicAttributeUnvalue()
  {
    $static = $this->former->text('foo')->require()->__toString();
    $matcher = $this->controlGroup('<input require="true" type="text" name="foo" id="foo">');

    $this->assertHTML($this->matchField(), $static);
    $this->assertHTML($this->matchControlGroup(), $static);
  }

  public function testSetAttributes()
  {
    $attributes = array('class' => 'foo', 'data-foo' => 'bar');

    $static = $this->former->text('foo')->require()->setAttributes($attributes)->__toString();

    $field = $this->matchField();
    $field['attributes']['require'] = 'true';
    $field['attributes']['class'] = 'foo';
    $field['attributes']['data-foo'] = 'bar';
    $this->assertHTML($field, $static);
    $this->assertHTML($this->matchControlGroup(), $static);
  }

  public function testReplaceAttributes()
  {
    $attributes = array('class' => 'foo', 'data-foo' => 'bar');

    $static = $this->former->text('foo')->require()->replaceAttributes($attributes)->__toString();
    $matcher = $this->controlGroup('<input class="foo" data-foo="bar" type="text" name="foo" id="foo">');

    $field = $this->matchField();
    $field['attributes']['class'] = 'foo';
    $field['attributes']['data-foo'] = 'bar';
    $this->assertHTML($field, $static);
    $this->assertHTML($this->matchControlGroup(), $static);
  }

  public function testGetAttribute()
  {
    $former = $this->former->span1_text('name')->foo('bar');

    $this->assertEquals('span1', $former->class);
    $this->assertEquals('bar', $former->foo);
  }

  public function testAddClass()
  {
    $static = $this->former->text('foo')->class('foo')->addClass('bar')->__toString();
    $input  = $this->former->text('foo', null, null, array('class' => 'foo'))->addClass('bar')->__toString();
    $matcher = $this->controlGroup('<input class="foo bar" type="text" name="foo" id="foo">');

    $this->assertHTML($this->matchControlGroup(), $static);
    $this->assertHTML($this->matchField(), $static);
    $this->assertHTML($this->matchControlGroup(), $input);
    $this->assertHTML($this->matchField(), $input);
  }

  /**
   * @dataProvider sizes
   */
  public function testMagicMethods($size)
  {
    $method = $size.'_text';
    $class = String::startsWith($size, 'span') ? $size. ' ' : 'input-'.$size. ' ';
    $static = $this->former->$method('foo')->addClass('bar')->__toString();
    if($class == 'input-foo ') $class = null;

    $field = $this->matchField();
    $field['attributes']['class'] = $class.'bar';
    $this->assertHTML($this->matchControlGroup(), $static);
    $this->assertHTML($field, $static);
  }

  public function testErrors()
  {
    $validator = $this->app->app['validator']->getMessages();

    $this->former->withErrors($validator);
    $required = $this->former->text('required')->__toString();
    $matcher =
    '<div class="control-group error">'.
      '<label for="required" class="control-label">Required</label>'.
      '<div class="controls">'.
        '<input id="required" type="text" name="required">'.
        '<span class="help-inline">The required field is required.</span>'.
      '</div>'.
    '</div>';

    $this->assertEquals($matcher, $required);
  }

  public function testCanDisableErrors()
  {
    $validator = $this->app->app['validator']->getMessages();
    $this->app->app['config'] = $this->app->getConfig(true, '', false, true, false);

    $this->former->withErrors($validator);
    $required = $this->former->text('required')->__toString();
    $matcher =
    '<div class="control-group error">'.
      '<label for="required" class="control-label">Required</label>'.
      '<div class="controls">'.
        '<input id="required" type="text" name="required">'.
      '</div>'.
    '</div>';

    $this->assertEquals($matcher, $required);
  }

  public function testPopulate()
  {
    $this->former->populate(array('foo' => 'bar'));

    $populate = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroup('<input id="foo" type="text" name="foo" value="bar">');

    $this->assertEquals($matcher, $populate);
  }

  public function testPopulateWithSpecificValue()
  {
    $this->former->populate(array('foo' => 'bar'));
    $this->former->populateField('foo', 'foo');

    $populate = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroup('<input id="foo" type="text" name="foo" value="foo">');

    $this->assertEquals($matcher, $populate);
  }

  public function testNestedRelationships()
  {
    $foo = (object) array('bar' => (object) array('kal' => (object) array('ter' => 'men')));
    $this->former->populate($foo);

    $text = $this->former->text('bar.kal.ter')->__toString();
    $matcher = $this->controlGroup(
      '<input id="bar.kal.ter" type="text" name="bar.kal.ter" value="men">',
      '<label for="bar.kal.ter" class="control-label">Bar.kal.ter</label>');

    $this->assertEquals($matcher, $text);
  }

  public function testNestedRelationshipsRenamedField()
  {
    $foo = (object) array('bar' => (object) array('kal' => (object) array('ter' => 'men')));
    $this->former->populate($foo);

    $text = $this->former->text('bar.kal.ter')->name('ter')->__toString();
    $matcher = $this->controlGroup(
      '<input id="ter" type="text" name="ter" value="men">',
      '<label for="ter" class="control-label">Ter</label>');

    $this->assertEquals($matcher, $text);
  }

  public function testMultipleNestedRelationships()
  {
    for($i = 0; $i < 2; $i++) $bar[] = (object) array('kal' => 'val'.$i);
    $foo = (object) array('bar' => $bar);
    $this->former->populate($foo);

    $text = $this->former->text('bar.kal')->__toString();
    $matcher = $this->controlGroup(
      '<input id="bar.kal" type="text" name="bar.kal" value="val0, val1">',
      '<label for="bar.kal" class="control-label">Bar.kal</label>');

    $this->assertEquals($matcher, $text);
  }

  public function testNoPopulatingPasswords()
  {
    $this->former->populate(array('foo' => 'bar'));
    $populate = $this->former->password('foo')->__toString();
    $matcher = $this->controlGroup('<input id="foo" type="password" name="foo">');

    $this->assertEquals($matcher, $populate);
  }

  public function testDatalist()
  {
    $datalist = $this->former->text('foo')->useDatalist(array('foo' => 'bar', 'kel' => 'tar'))->__toString();
    $matcher =
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

  public function testDatalistCustomList()
  {
    $datalist = $this->former->text('foo')->list('bar')->useDatalist(array('foo' => 'bar', 'kel' => 'tar'))->__toString();
    $matcher =
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

}
