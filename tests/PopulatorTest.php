<?php
use Former\Populator;

class PopulatorTest extends FormerTests
{

  public function testCanPopulateClassOnConstruct()
  {
    $populator = new Populator(array('foo', 'bar'));

    $this->assertEquals(array('foo', 'bar'), $populator->getValues());
  }

  public function testCanResetValues()
  {
    $populator = new Populator(array('foo', 'bar'));
    $populator->reset();

    $this->assertEquals(array(), $populator->getValues());
  }

  public function testCanSetSingleValue()
  {
    $populator = new Populator(array('foo', 'bar'));
    $populator->setValue(2, 'bar');

    $this->assertEquals(array('foo', 'bar', 'bar'), $populator->getValues());
  }

  public function testCanSwapOutValues()
  {
    $populator = new Populator(array('foo', 'bar'));
    $populator->setValues(array('bar', 'foo'));

    $this->assertEquals(array('bar', 'foo'), $populator->getValues());
  }

  public function testCanGetAttributesAndMutators()
  {
    $model = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
    $populator = new Populator($model);

    $this->assertEquals('custom', $populator->getValue('attribute_old'));
    $this->assertEquals('custom', $populator->getValue('custom'));
    $this->assertEquals('foo', $populator->getValue('name'));
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

    $this->assertEquals('ter', $populator->getValue('foo[bar][bis]'));
  }

  public function testCanGetRelationships()
  {
    $model = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
    $populator = new Populator($model);
    $values = array(
      new DummyEloquent(array('id' => 1, 'name' => 'foo')),
      new DummyEloquent(array('id' => 3, 'name' => 'bar')),
    );

    $this->assertEquals($values, $populator->getValue('roles'));
  }

}