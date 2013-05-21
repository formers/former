<?php
use Underscore\Methods\ArraysMethods as Arrays;
use Underscore\Methods\StringMethods as String;

class InputTest extends FormerTests
{

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// DATA PROVIDERS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function provideSizes()
  {
    $_sizes = array('mini', 'small', 'medium', 'large', 'xlarge', 'xxlarge', 'span1', 'span6', 'span12', 'foo');
    foreach($_sizes as $s) $sizes[] = array($s);

    return $sizes;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testCanCreateText()
  {
    $input = $this->former->text('foo')->__toString();

    $this->assertControlGroup($input);
    $this->assertHTML($this->matchField(), $input);
  }

  public function testCanCreateTextWithoutLabel()
  {
    $this->app->app['config'] = $this->app->getConfig(true, '', false, false);

    $input = $this->former->text('foo')->__toString();
    $matchField = Arrays::remove($this->matchField(), 'id');

    $this->assertHTML($this->matchControlGroup(), $input);
    $this->assertHTML($matchField, $input);
  }

  public function testCanCreateSingleTextWithoutLabelOnStart()
  {
    $input = $this->former->text('foo', '')->__toString();
    $matchField = Arrays::remove($this->matchField(), 'id');

    $this->assertHTML($this->matchControlGroup(), $input);
    $this->assertHTML($matchField, $input);
  }

  public function testCanCreateSingleTextWithoutLabel()
  {
    $input = $this->former->text('foo')->label(null)->__toString();
    $matchField = Arrays::remove($this->matchField(), 'id');

    $this->assertHTML($this->matchControlGroup(), $input);
    $this->assertHTML($matchField, $input);
  }

  public function testCanCreateSearchField()
  {
    $input = $this->former->search('foo')->__toString();
    $matchField = Arrays::set($this->matchField(), 'attributes.class', 'search-query');

    $this->assertControlGroup($input);
    $this->assertHTML($matchField, $input);
  }

  public function testCanCreateTextFieldWithoutBootstrap()
  {
    $this->former->framework('Nude');

    $input = $this->former->text('foo')->data('foo')->class('bar')->__toString();
    $label = Arrays::remove($this->matchLabel(), 'attributes.class');

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

  public function testCanCreateTextLabelWithoutBootstrap()
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

  public function testCanRenameField()
  {
    $input = $this->former->text('foo')->name('bar')->__toString();
    $matcher = $this->controlGroup(
      '<input id="bar" type="text" name="bar">',
      '<label for="bar" class="control-label">Bar</label>');

    $this->assertEquals($matcher, $input);
  }

  public function testCanSetValueOnFields()
  {
    $static = $this->former->text('foo')->value('bar')->__toString();
    $input  = $this->former->text('foo', null, 'bar')->__toString();
    $matcher = $this->controlGroup('<input id="foo" type="text" name="foo" value="bar">');

    $this->assertHTML($this->matchField(), $static);
    $this->assertHTML($this->matchControlGroup(), $static);
    $this->assertHTML($this->matchField(), $input);
    $this->assertHTML($this->matchControlGroup(), $input);
  }

  public function testCanForceValueOnFields()
  {
    $this->former->populate(array('foo' => 'unbar'));
    $static = $this->former->text('foo')->forceValue('bar')->__toString();
    $matcher = $this->controlGroup('<input id="foo" type="text" name="foo" value="bar">');

    $this->assertEquals($matcher, $static);
  }

  public function testCanCreateViaMagicAttribute()
  {
    $static = $this->former->text('foo')->class('foo')->data_bar('bar')->__toString();
    $input  = $this->former->text('foo', null, null, array('class' => 'foo', 'data-bar' => 'bar'))->__toString();

    $this->assertHTML($this->matchField(), $static);
    $this->assertHTML($this->matchControlGroup(), $static);
    $this->assertHTML($this->matchField(), $input);
    $this->assertHTML($this->matchControlGroup(), $input);
  }

  public function testCanCreateViaMagicAttributeUnvalue()
  {
    $static = $this->former->text('foo')->require()->__toString();
    $matcher = $this->controlGroup('<input require="true" type="text" name="foo" id="foo">');

    $this->assertHTML($this->matchField(), $static);
    $this->assertHTML($this->matchControlGroup(), $static);
  }

  public function testCanSetAttributes()
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

  public function testCanReplaceAttributes()
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

  public function testCanGetAttribute()
  {
    $former = $this->former->span1_text('name')->foo('bar');

    $this->assertEquals('span1', $former->class);
    $this->assertEquals('bar', $former->foo);
  }

  public function testCanAddClass()
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
   * @dataProvider provideSizes
   */
  public function testCanUseMagicMethods($size)
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

  public function testCanCreateWithErrors()
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

  public function testCanCreatePopulate()
  {
    $this->former->populate(array('foo' => 'bar'));

    $populate = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroup('<input id="foo" type="text" name="foo" value="bar">');

    $this->assertEquals($matcher, $populate);
  }

  public function testCanCreatePopulateWithSpecificValue()
  {
    $this->former->populate(array('foo' => 'bar'));
    $this->former->populateField('foo', 'foo');

    $populate = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroup('<input id="foo" type="text" name="foo" value="foo">');

    $this->assertEquals($matcher, $populate);
  }

  public function testCanCreateNestedRelationships()
  {
    $foo = (object) array('bar' => (object) array('kal' => (object) array('ter' => 'men')));
    $this->former->populate($foo);

    $text = $this->former->text('bar.kal.ter')->__toString();
    $matcher = $this->controlGroup(
      '<input id="bar.kal.ter" type="text" name="bar.kal.ter" value="men">',
      '<label for="bar.kal.ter" class="control-label">Bar.kal.ter</label>');

    $this->assertEquals($matcher, $text);
  }

  public function testCanCreateNestedRelationshipsRenamedField()
  {
    $foo = (object) array('bar' => (object) array('kal' => (object) array('ter' => 'men')));
    $this->former->populate($foo);

    $text = $this->former->text('bar.kal.ter')->name('ter')->__toString();
    $matcher = $this->controlGroup(
      '<input id="ter" type="text" name="ter" value="men">',
      '<label for="ter" class="control-label">Ter</label>');

    $this->assertEquals($matcher, $text);
  }

  public function testCanCreateMultipleNestedRelationships()
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

  public function testCanCreateNoPopulatingPasswords()
  {
    $this->former->populate(array('foo' => 'bar'));
    $populate = $this->former->password('foo')->__toString();
    $matcher = $this->controlGroup('<input id="foo" type="password" name="foo">');

    $this->assertEquals($matcher, $populate);
  }

  public function testCanCreateDatalist()
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

  public function testCanCreateDatalistCustomList()
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
