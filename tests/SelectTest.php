<?php
use \Former\Former;

class SelectTest extends FormerTests
{
  private $options = array('foo' => 'bar', 'kal' => 'ter');

  public static function setUpBeforeClass()
  {
    //$this->app['config']->set('application.language', 'en');
  }

  public function testSelect()
  {
    $select = $this->app['former']->select('foo')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testMultiselect()
  {
    $select = $this->app['former']->multiselect('foo')->__toString();
    $matcher = $this->cg('<select multiple="true" id="foo" name="foo"></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectOptions()
  {
    $select = $this->app['former']->select('foo')->options($this->options)->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="foo">bar</option><option value="kal">ter</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testGetSelectOptions()
  {
    $this->app['former']->select('foo')->options($this->options);

    $this->assertEquals($this->app['former']->field()->getOptions(), $this->options);
  }

  public function testSelectPlaceholder()
  {
    $select = $this->app['former']->select('foo')->options($this->options)->placeholder('Pick something')->__toString();
    $matcher = $this->cg(
      '<select id="foo" name="foo">'.
        '<option value="" disabled="" selected="">Pick something</option>'.
        '<option value="foo">bar</option>'.
        '<option value="kal">ter</option>'.
      '</select>');

    $this->assertEquals($matcher, $select);
  }

  public function testPlaceholderUnselected()
  {
    $select = $this->app['former']->select('foo')->value('foo')->options($this->options)->placeholder('Pick something')->__toString();
    $matcher = $this->cg(
      '<select id="foo" name="foo">'.
        '<option value="" disabled="">Pick something</option>'.
        '<option value="foo" selected="selected">bar</option>'.
        '<option value="kal">ter</option>'.
      '</select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectLang()
  {
    $select = $this->app['former']->select('foo')->options($this->app['translator']->get('pagination'), 'previous')->__toString();
    $matcher = $this->cg(
    '<select id="foo" name="foo">'.
      '<option value="previous" selected="selected">&laquo; Previous</option>'.
      '<option value="next">Next &raquo;</option>'.
    '</select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquent()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('id' => $i, 'foo' => 'bar');
    $select = $this->app['former']->select('foo')->fromQuery($eloquent, 'foo')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquentKey()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
    $select = $this->app['former']->select('foo')->fromQuery($eloquent, 'foo', 'age')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquentWrongKey()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
    $select = $this->app['former']->select('foo')->fromQuery($eloquent, 'foo', 'id')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="bar">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectWithAString()
  {
    $select = $this->app['former']->select('foo')->fromQuery('This is not an array', 'foo', 'id')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="0">This is not an array</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectWithAnInteger()
  {
    $select = $this->app['former']->select('foo')->fromQuery(456, 'foo', 'id')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="0">456</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquentArray()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
    $select = $this->app['former']->select('foo')->fromQuery($eloquent, 'foo', 'age')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testNestedRelationships()
  {
    for($i = 0; $i < 2; $i++) $bar[] = (object) array('id' => $i, 'kal' => 'val'.$i);
    $foo = (object) array('bar' => $bar);
    $this->app['former']->populate($foo);

    $select = $this->app['former']->select('bar.kal')->__toString();
    $matcher = $this->cg(
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
      $eloquentObject = $this->getMock('Foo', array('__toString', 'get_key'));
      $eloquentObject
        ->expects($this->any())
        ->method('__toString')
        ->will($this->returnValue('bar'));
      $eloquentObject
        ->expects($this->any())
        ->method('get_key')
        ->will($this->returnValue($i));
      $eloquent[] = $eloquentObject;
    }

    $select = $this->app['former']->select('foo')->fromQuery($eloquent)->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectOptionsValue()
  {
    $select = $this->app['former']->select('foo')->data_foo('bar')->options($this->options, 'kal')->__toString();
    $matcher = $this->cg(
    '<select data-foo="bar" id="foo" name="foo">'.
      '<option value="foo">bar</option>'.
      '<option value="kal" selected="selected">ter</option>'.
    '</select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectOptionsValueMethod()
  {
    $select = $this->app['former']->select('foo')->data_foo('bar')->options($this->options)->select('kal')->__toString();
    $matcher = $this->cg(
    '<select data-foo="bar" id="foo" name="foo">'.
      '<option value="foo">bar</option>'.
      '<option value="kal" selected="selected">ter</option>'.
    '</select>');

    $this->assertEquals($matcher, $select);
  }
}
