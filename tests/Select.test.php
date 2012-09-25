<?php
use \Former\Former;

class SelectTest extends FormerTests
{
  private $options = array('foo' => 'bar', 'kal' => 'ter');

  public function testSelect()
  {
    $select = Former::select('foo')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testMultiselect()
  {
    $select = Former::multiselect('foo')->__toString();
    $matcher = $this->cg('<select multiple="true" id="foo" name="foo"></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectOptions()
  {
    $select = Former::select('foo')->options($this->options)->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="foo">bar</option><option value="kal">ter</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquent()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('id' => $i, 'foo' => 'bar');
    $select = Former::select('foo')->fromQuery($eloquent, 'foo')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquentKey()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
    $select = Former::select('foo')->fromQuery($eloquent, 'foo', 'age')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquentWrongKey()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
    $select = Former::select('foo')->fromQuery($eloquent, 'foo', 'id')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="bar">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquentArray()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
    $select = Former::select('foo')->fromQuery($eloquent, 'foo', 'age')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectOptionsValue()
  {
    $select = Former::select('foo')->data_foo('bar')->options($this->options, 'kal')->__toString();
    $matcher = $this->cg(
    '<select data-foo="bar" id="foo" name="foo">'.
      '<option value="foo">bar</option>'.
      '<option value="kal" selected="selected">ter</option>'.
    '</select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectOptionsValueMethod()
  {
    $select = Former::select('foo')->data_foo('bar')->options($this->options)->select('kal')->__toString();
    $matcher = $this->cg(
    '<select data-foo="bar" id="foo" name="foo">'.
      '<option value="foo">bar</option>'.
      '<option value="kal" selected="selected">ter</option>'.
    '</select>');

    $this->assertEquals($matcher, $select);
  }
}
