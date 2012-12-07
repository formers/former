<?php
use \Former\Former;
use \Former\Config;

class InputTest extends FormerTests
{
  public function tearDown()
  {
    parent::tearDown();
    $this->app['former']->framework('bootstrap');
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
    $input = $this->app['former']->text('foo')->__toString();
    $matcher = $this->cg('<input type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
  }

  public function testTextWithoutLabel()
  {
    $this->app['config'] = $this->getConfig(true, '', false, false);

    $input = $this->app['former']->text('foo')->__toString();
    $matcher = $this->cg('<input type="text" name="foo">', null);

    $this->assertEquals($matcher, $input);
  }

  public function testSingleTextWithoutLabelOnStart()
  {
    $input = $this->app['former']->text('foo', '')->__toString();
    $matcher = $this->cg('<input type="text" name="foo">', null);

    $this->assertEquals($matcher, $input);
  }

  public function testSingleTextWithoutLabel()
  {
    $input = $this->app['former']->text('foo')->label(null)->__toString();
    $matcher = $this->cg('<input type="text" name="foo">', null);

    $this->assertEquals($matcher, $input);
  }

  public function testSearch()
  {
    $input = $this->app['former']->search('foo')->__toString();
    $matcher = $this->cg('<input class="search-query" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
  }

  public function testTextWithoutBootstrap()
  {
    $this->app['former']->framework('none');

    $input = $this->app['former']->text('foo')->data('foo')->class('bar')->__toString();
    $matcher = '<label for="foo">Foo</label><input data="foo" class="bar" type="text" name="foo" id="foo">';

    $this->assertEquals($matcher, $input);
  }

  public function testTextWithoutFormInstance()
  {
    $this->app['former']->close();

    $input = $this->app['former']->text('foo')->data('foo')->class('bar')->__toString();
    $matcher = '<label for="foo">Foo</label><input data="foo" class="bar" type="text" name="foo" id="foo">';

    $this->assertEquals($matcher, $input);

    $this->app['former']->horizontal_open();
  }

  public function testHiddenField()
  {
    $input = $this->app['former']->hidden('foo')->value('bar')->__toString();
    $matcher = '<input type="hidden" name="foo" value="bar">';

    $this->assertEquals($matcher, $input);
  }

  public function testTextLabel()
  {
    $static  = $this->app['former']->text('foo')->label('bar', $this->testAttributes)->__toString();
    $matcher = $this->cg(
      '<input type="text" name="foo" id="foo">',
      '<label for="foo" class="foo control-label" data-foo="bar">Bar</label>');
    $this->assertEquals($matcher, $static);

    $input   = $this->app['former']->text('foo', 'bar')->__toString();
    $matcher = $this->cg(
      '<input type="text" name="foo" id="foo">',
      '<label for="foo" class="control-label">Bar</label>');
    $this->assertEquals($matcher, $input);
  }

  public function testTextLabelWithoutBootstrap()
  {
    $this->app['former']->framework('none');

    $static = $this->app['former']->text('foo')->label('bar', $this->testAttributes)->__toString();
    $matcher = '<label for="foo" class="foo" data-foo="bar">Bar</label><input type="text" name="foo" id="foo">';
    $this->assertEquals($matcher, $static);

    $input  = $this->app['former']->text('foo', 'bar')->__toString();
    $matcher = '<label for="foo">Bar</label><input type="text" name="foo" id="foo">';
    $this->assertEquals($matcher, $input);
  }

  public function testRenameField()
  {
    $input = $this->app['former']->text('foo')->name('bar')->__toString();
    $matcher = $this->cg(
      '<input type="text" name="bar" id="bar">',
      '<label for="bar" class="control-label">Bar</label>');

    $this->assertEquals($matcher, $input);
  }

  public function testValue()
  {
    $static = $this->app['former']->text('foo')->value('bar')->__toString();
    $input  = $this->app['former']->text('foo', null, 'bar')->__toString();
    $matcher = $this->cg('<input type="text" name="foo" value="bar" id="foo">');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testForceValue()
  {
    $this->app['former']->populate(array('foo' => 'unbar'));
    $static = $this->app['former']->text('foo')->forceValue('bar')->__toString();
    $matcher = $this->cg('<input type="text" name="foo" value="bar" id="foo">');

    $this->assertEquals($matcher, $static);
  }

  public function testMagicAttribute()
  {
    $static = $this->app['former']->text('foo')->class('foo')->data_bar('bar')->__toString();
    $input  = $this->app['former']->text('foo', null, null, array('class' => 'foo', 'data-bar' => 'bar'))->__toString();
    $matcher = $this->cg('<input class="foo" data-bar="bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  public function testMagicAttributeUnvalue()
  {
    $static = $this->app['former']->text('foo')->require()->__toString();
    $matcher = $this->cg('<input require="true" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $static);
  }

  public function testSetAttributes()
  {
    $attributes = array('class' => 'foo', 'data-foo' => 'bar');

    $static = $this->app['former']->text('foo')->require()->setAttributes($attributes)->__toString();
    $matcher = $this->cg('<input require="true" class="foo" data-foo="bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $static);
  }

  public function testSetAttributesOverwrite()
  {
    $attributes = array('class' => 'foo', 'data-foo' => 'bar');

    $static = $this->app['former']->text('foo')->require()->setAttributes($attributes, false)->__toString();
    $matcher = $this->cg('<input class="foo" data-foo="bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $static);
  }

  public function testGetAttribute()
  {
    $former = $this->app['former']->span1_text('name')->foo('bar');

    $this->assertEquals('span1', $former->class);
    $this->assertEquals('bar', $former->foo);
  }

  public function testAddClass()
  {
    $static = $this->app['former']->text('foo')->class('foo')->addClass('bar')->__toString();
    $input  = $this->app['former']->text('foo', null, null, array('class' => 'foo'))->addClass('bar')->__toString();
    $matcher = $this->cg('<input class="foo bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($matcher, $static);
  }

  /**
   * @dataProvider sizes
   */
  public function testMagicMethods($size)
  {
    $method = $size.'_text';
    $class = starts_with($size, 'span') ? $size. ' ' : 'input-'.$size. ' ';
    $static = $this->app['former']->$method('foo')->addClass('bar')->__toString();
    if($class == 'input-foo ') $class = null;

    $matcher = $this->cg('<input class="' .$class. 'bar" type="text" name="foo" id="foo">');

    $this->assertEquals($matcher, $static);
  }

  public function testErrors()
  {
    $this->markTestSkipped('Validator class unfinished');

    $validator = $this->app['validator']->make(array('required' => null), array('required' => 'required'));
    $validator->speaks('en');
    $validator->valid();

    $this->app['former']->withErrors($validator);
    $required = $this->app['former']->text('required')->__toString();
    $matcher =
    '<div class="control-group error">'.
      '<label for="required" class="control-label">Required</label>'.
      '<div class="controls">'.
        '<input type="text" name="required" id="required">'.
        '<span class="help-inline">The required field is required.</span>'.
      '</div>'.
    '</div>';

    $this->assertEquals($matcher, $required);
  }

  public function testPopulate()
  {
    $this->app['former']->populate(array('foo' => 'bar'));

    $populate = $this->app['former']->text('foo')->__toString();
    $matcher = $this->cg('<input type="text" name="foo" value="bar" id="foo">');

    $this->assertEquals($matcher, $populate);
  }

  public function testPopulateWithSpecificValue()
  {
    $this->app['former']->populate(array('foo' => 'bar'));
    $this->app['former']->populateField('foo', 'foo');

    $populate = $this->app['former']->text('foo')->__toString();
    $matcher = $this->cg('<input type="text" name="foo" value="foo" id="foo">');

    $this->assertEquals($matcher, $populate);
  }

  public function testNestedRelationships()
  {
    $foo = (object) array('bar' => (object) array('kal' => (object) array('ter' => 'men')));
    $this->app['former']->populate($foo);

    $text = $this->app['former']->text('bar.kal.ter')->__toString();
    $matcher = $this->cg(
      '<input type="text" name="bar.kal.ter" value="men" id="bar.kal.ter">',
      '<label for="bar.kal.ter" class="control-label">Bar.kal.ter</label>');

    $this->assertEquals($matcher, $text);
  }

  public function testNestedRelationshipsRenamedField()
  {
    $foo = (object) array('bar' => (object) array('kal' => (object) array('ter' => 'men')));
    $this->app['former']->populate($foo);

    $text = $this->app['former']->text('bar.kal.ter')->name('ter')->__toString();
    $matcher = $this->cg(
      '<input type="text" name="ter" value="men" id="ter">',
      '<label for="ter" class="control-label">Ter</label>');

    $this->assertEquals($matcher, $text);
  }

  public function testMultipleNestedRelationships()
  {
    for($i = 0; $i < 2; $i++) $bar[] = (object) array('kal' => 'val'.$i);
    $foo = (object) array('bar' => $bar);
    $this->app['former']->populate($foo);

    $text = $this->app['former']->text('bar.kal')->__toString();
    $matcher = $this->cg(
      '<input type="text" name="bar.kal" value="val0, val1" id="bar.kal">',
      '<label for="bar.kal" class="control-label">Bar.kal</label>');

    $this->assertEquals($matcher, $text);
  }

  public function testNoPopulatingPasswords()
  {
    $this->app['former']->populate(array('foo' => 'bar'));
    $populate = $this->app['former']->password('foo')->__toString();
    $matcher = $this->cg('<input type="password" name="foo" id="foo">');

    $this->assertEquals($matcher, $populate);
  }

  public function testDatalist()
  {
    $datalist = $this->app['former']->text('foo')->useDatalist(array('foo' => 'bar', 'kel' => 'tar'))->__toString();
    $matcher =
    '<div class="control-group">'.
      '<label for="foo" class="control-label">Foo</label>'.
      '<div class="controls">'.
        '<input list="datalist_foo" type="text" name="foo" id="foo">'.
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
    $datalist = $this->app['former']->text('foo')->list('bar')->useDatalist(array('foo' => 'bar', 'kel' => 'tar'))->__toString();
    $matcher =
    '<div class="control-group">'.
      '<label for="foo" class="control-label">Foo</label>'.
      '<div class="controls">'.
        '<input list="bar" type="text" name="foo" id="foo">'.
        '<datalist id="bar">'.
          '<option value="bar">foo</option>'.
          '<option value="tar">kel</option>'.
        '</datalist>'.
      '</div>'.
    '</div>';

    $this->assertEquals($matcher, $datalist);
  }

}
