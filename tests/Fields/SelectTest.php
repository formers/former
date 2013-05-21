<?php
class SelectTest extends FormerTests
{

  /**
   * An array of dummy options
   *
   * @var array
   */
  private $options = array('foo' => 'bar', 'kal' => 'ter');

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testSelect()
  {
    $select = $this->former->select('foo')->__toString();
    $matcher = $this->controlGroup('<select id="foo" name="foo"></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testMultiselect()
  {
    $select = $this->former->multiselect('foo')->__toString();
    $matcher = $this->controlGroup('<select id="foo" multiple="true" name="foo[]"></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testMultiselectOptions()
  {
    $select = $this->former->multiselect('foo')->options($this->options)->value(array('foo', 'kal'))->__toString();
    $matcher = $this->controlGroup('<select id="foo" multiple="true" name="foo[]"><option value="foo" selected="selected">bar</option><option value="kal" selected="selected">ter</option></select>');
    $this->assertEquals($matcher, $select);
  }


  public function testSelectOptions()
  {
    $select = $this->former->select('foo')->options($this->options)->__toString();
    $matcher = $this->controlGroup('<select id="foo" name="foo"><option value="foo">bar</option><option value="kal">ter</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testGetSelectOptions()
  {
    $select = $this->former->select('foo')->options($this->options);

    foreach ($this->options as $key => $option) {
      $options[$key] = HtmlObject\Element::create('option', $option, array('value' => $key));
    }

    $this->assertEquals($select->getOptions(), $options);
  }

  public function testSelectPlaceholder()
  {
    $select = $this->former->select('foo')->options($this->options)->placeholder('Pick something')->__toString();
    $matcher = $this->controlGroup(
      '<select id="foo" name="foo">'.
        '<option value="" disabled="disabled" selected="selected">Pick something</option>'.
        '<option value="foo">bar</option>'.
        '<option value="kal">ter</option>'.
      '</select>');

    $this->assertEquals($matcher, $select);
  }

  public function testPlaceholderUnselected()
  {
    $select = $this->former->select('foo')->value('foo')->options($this->options)->placeholder('Pick something')->__toString();
    $matcher = $this->controlGroup(
      '<select id="foo" name="foo">'.
        '<option value="" disabled="disabled">Pick something</option>'.
        '<option value="foo" selected="selected">bar</option>'.
        '<option value="kal">ter</option>'.
      '</select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectLang()
  {
    $select = $this->former->select('foo')->options($this->app->app['translator']->get('pagination'), 'previous')->__toString();
    $matcher = $this->controlGroup(
    '<select id="foo" name="foo">'.
      '<option value="previous" selected="selected">Previous</option>'.
      '<option value="next">Next</option>'.
    '</select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquent()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('id' => $i, 'foo' => 'bar');
    $select = $this->former->select('foo')->fromQuery($eloquent, 'foo')->__toString();
    $matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquentKey()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
    $select = $this->former->select('foo')->fromQuery($eloquent, 'foo', 'age')->__toString();
    $matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquentWrongKey()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
    $select = $this->former->select('foo')->fromQuery($eloquent, 'foo', 'id')->__toString();
    $matcher = $this->controlGroup('<select id="foo" name="foo"><option value="bar">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectWithAString()
  {
    $select = $this->former->select('foo')->fromQuery('This is not an array', 'foo', 'id')->__toString();
    $matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">This is not an array</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectWithAnInteger()
  {
    $select = $this->former->select('foo')->fromQuery(456, 'foo', 'id')->__toString();
    $matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">456</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectEloquentArray()
  {
    for($i = 0; $i < 2; $i++) $eloquent[] = (object) array('age' => $i, 'foo' => 'bar');
    $select = $this->former->select('foo')->fromQuery($eloquent, 'foo', 'age')->__toString();
    $matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testNestedRelationships()
  {
    for($i = 0; $i < 2; $i++) $bar[] = (object) array('id' => $i, 'kal' => 'val'.$i);
    $foo = (object) array('bar' => $bar);
    $this->former->populate($foo);

    $select = $this->former->select('bar.kal')->__toString();
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
      $eloquent[] = $eloquentObject;
    }

    $select = $this->former->select('foo')->fromQuery($eloquent)->__toString();
    $matcher = $this->controlGroup('<select id="foo" name="foo"><option value="0">bar</option><option value="1">bar</option></select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectOptionsValue()
  {
    $select = $this->former->select('foo')->data_foo('bar')->options($this->options, 'kal')->__toString();
    $matcher = $this->controlGroup(
    '<select data-foo="bar" id="foo" name="foo">'.
      '<option value="foo">bar</option>'.
      '<option value="kal" selected="selected">ter</option>'.
    '</select>');

    $this->assertEquals($matcher, $select);
  }

  public function testSelectOptionsValueMethod()
  {
    $select = $this->former->select('foo')->data_foo('bar')->options($this->options)->select('kal')->__toString();
    $matcher = $this->controlGroup(
    '<select data-foo="bar" id="foo" name="foo">'.
      '<option value="foo">bar</option>'.
      '<option value="kal" selected="selected">ter</option>'.
    '</select>');

    $this->assertEquals($matcher, $select);
  }

  public function testCanAddAdditionalOptionsToCreatedSelect()
  {
    $select = $this->former->select('foo')->addOption(null)->options($this->options);
    $select->addOption('bis', 'ter');
    $matcher = $this->controlGroup(
    '<select id="foo" name="foo">'.
      '<option value=""></option>'.
      '<option value="foo">bar</option>'.
      '<option value="kal">ter</option>'.
      '<option value="ter">bis</option>'.
    '</select>');

    $this->assertEquals($matcher, $select->__toString());
  }

  public function testPopulateUnexistingOptionsDoesntThrowError()
  {
    $this->former->populate(array('foo' => 'foo'));
    $select = $this->former->select('foo')->options(array('bar' => 'Bar'));
    $matcher = $this->controlGroup(
    '<select id="foo" name="foo">'.
      '<option value="bar">Bar</option>'.
    '</select>');

    $this->assertEquals($matcher, $select->__toString());
  }

  public function testCanPopulateWithCollections()
  {
    $collection = new Illuminate\Support\Collection(array(
      new DummyEloquent(array('id' => 1, 'name' => 'foo')),
      new DummyEloquent(array('id' => 2, 'name' => 'bar'))
    ));

    $select = $this->former->select('foo')->fromQuery($collection);
    $matcher = $this->controlGroup(
    '<select id="foo" name="foo">'.
      '<option value="1">foo</option>'.
      '<option value="2">bar</option>'.
    '</select>');

    $this->assertEquals($matcher, $select->__toString());
  }

}
