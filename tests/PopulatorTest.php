<?php
use Former\Populator;

class PopulatorTest extends FormerTests
{
  public function testCanPopulateClassOnConstruct()
  {
    $populator = new Populator(array('foo', 'bar'));

    $this->assertEquals(array('foo', 'bar'), $populator->all());
  }

  public function testCanResetValues()
  {
    $populator = new Populator(array('foo', 'bar'));
    $populator->reset();

    $this->assertEquals(array(), $populator->all());
  }

  public function testCanSetSingleValue()
  {
    $populator = new Populator(array('foo', 'bar'));
    $populator->put(2, 'bar');

    $this->assertEquals(array('foo', 'bar', 'bar'), $populator->all());
  }

  public function testCanSetValueOnModel()
  {
    $model = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
    $populator = new Populator($model);
    $populator->put('foo', 'bar');

    $this->assertEquals('bar', $populator->get('foo'));
  }

  public function testCanSwapOutValues()
  {
    $populator = new Populator(array('foo', 'bar'));
    $populator->replace(array('bar', 'foo'));

    $this->assertEquals(array('bar', 'foo'), $populator->all());
  }

  public function testCanGetAttributesAndMutators()
  {
    $model = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
    $populator = new Populator($model);

    $this->assertEquals('custom', $populator->get('custom'));
    $this->assertEquals('foo', $populator->get('name'));
  }

  public function testCanGetValueThroughArrayNotation()
  {
    $values = (object) array(
      'foo' => array(
        'bar' => array(
          'bis' => 'ter',
        ),
      ),
    );
    $populator = new Populator($values);

    $this->assertEquals('ter', $populator->get('foo[bar][bis]'));
  }

  public function testCanGetValueOfFieldsWithUnderscores()
  {
    $values = (object) array(
      'foo_bar' => array(
        'bar_bis' => 'ter',
      ),
    );
    $populator = new Populator($values);

    $this->assertEquals('ter', $populator->get('foo_bar[bar_bis]'));
  }

  public function testCanGetRelationships()
  {
    $model = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
    $populator = new Populator($model);
    $values = array(
      new DummyEloquent(array('id' => 1, 'name' => 'foo')),
      new DummyEloquent(array('id' => 3, 'name' => 'bar')),
    );

    $this->assertEquals($values, $populator->get('roles'));
  }

  public function testCanGetNestedArrayValues()
  {
    $populator = new Populator(array('foo' => array(0 => 'one', 1 => 'two')));

    $this->assertEquals('two', $populator->get('foo[1]'));
  }

  public function testCanCastModelToArray()
  {
    $model = new DummyToArray(array(
      'user' => new DummyToArray(array('name' => 'foo'))
    ));
    $populator = new Populator($model);

    $this->assertEquals('foo', $populator->get('user.name'));
  }
}
