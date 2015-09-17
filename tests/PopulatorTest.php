<?php
namespace Former;

use Former\Dummy\DummyEloquent;
use Former\Dummy\DummyToArray;
use Former\TestCases\FormerTests;

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
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
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
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals('custom', $populator->get('custom'));
		$this->assertEquals('foo', $populator->get('name'));
	}

	public function testCanGetValueThroughArrayNotation()
	{
		$values    = (object) array(
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
		$values    = (object) array(
			'foo_bar' => array(
				'bar_bis' => 'ter',
			),
		);
		$populator = new Populator($values);

		$this->assertEquals('ter', $populator->get('foo_bar[bar_bis]'));
	}

	public function testCanGetRelationships()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);
		$values    = array(
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
		$model     = new DummyToArray(array(
			'user' => new DummyToArray(array('name' => 'foo')),
		));
		$populator = new Populator($model);

		$this->assertEquals('foo', $populator->get('user.name'));
	}

	public function testCanGetArrayOfObjectValues()
	{
		$values    = array('foo' => (object) array('bar' => array('bis' => 'ter')));
		$populator = new Populator($values);

		$this->assertEquals('ter', $populator->get('foo[bar][bis]'));
	}

	public function testCanGetRelationshipAttribute()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals('foo', $populator->get('roles[0][name]'));
	}

	public function testCanGetRelationshipMutatedAttribute()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals('custom', $populator->get('roles[0][custom]'));
	}

	public function testCanGetRelationshipCollectionItemAttribute()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals('foo', $populator->get('rolesAsCollection[0][name]'));
	}

	public function testCanGetRelationshipCollectionItemMutatedAttribute()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals('custom', $populator->get('rolesAsCollection[0][custom]'));
	}

	public function testCanFallbackDefaultOnGetSingleValue()
	{
		$populator = new Populator(array('foo', 'bar'));

		$this->assertEquals(null, $populator->get('bis'));
	}

	public function testCanFallbackCustomOnGetSingleValue()
	{
		$populator = new Populator(array('foo', 'bar'));

		$this->assertEquals('custom', $populator->get('bis', 'custom'));
	}

	public function testCanFallbackDefaultOnNestedArrayValueMissingFirstIndex()
	{
		$populator = new Populator(array('foo' => array('bar' => array('bis' => 'ter'))));

		$this->assertEquals(null, $populator->get('nofoo[bar][bis]'));
	}

	public function testCanFallbackCustomOnNestedArrayValueMissingFirstIndex()
	{
		$populator = new Populator(array('foo' => array('bar' => array('bis' => 'ter'))));

		$this->assertEquals('custom', $populator->get('nofoo[bar][bis]', 'custom'));
	}

	public function testCanFallbackDefaultOnNestedArrayValueMissingMiddleIndex()
	{
		$populator = new Populator(array('foo' => array('bar' => array('bis' => 'ter'))));

		$this->assertEquals(null, $populator->get('foo[nobar][bis]'));
	}

	public function testCanFallbackCustomOnNestedArrayValueMissingMiddleIndex()
	{
		$populator = new Populator(array('foo' => array('bar' => array('bis' => 'ter'))));

		$this->assertEquals('custom', $populator->get('foo[nobar][bis]', 'custom'));
	}

	public function testCanFallbackDefaultOnNestedArrayValueMissingLastIndex()
	{
		$populator = new Populator(array('foo' => array('bar' => array('bis' => 'ter'))));

		$this->assertEquals(null, $populator->get('foo[bar][nobis]'));
	}

	public function testCanFallbackCustomOnNestedArrayValueMissingLastIndex()
	{
		$populator = new Populator(array('foo' => array('bar' => array('bis' => 'ter'))));

		$this->assertEquals('custom', $populator->get('foo[bar][nobis]', 'custom'));
	}

	public function testCanFallbackDefaultOnNestedArrayValueNotEnoughValues()
	{
		$populator = new Populator(array('foo' => 'bar'));

		$this->assertEquals(null, $populator->get('foo[bar][bis]'));
	}

	public function testCanFallbackCustomOnNestedArrayValueNotEnoughValues()
	{
		$populator = new Populator(array('foo' => 'bar'));

		$this->assertEquals('custom', $populator->get('foo[bar][bis]', 'custom'));
	}

	public function testCanFallbackDefaultOnNestedArrayValueMissingIndexNotSkipped()
	{
		$populator = new Populator(array('foo' => array('bar' => array('bis' => 'ter'))));

		$this->assertEquals(null, $populator->get('foo[nobar][bar]'));
	}

	public function testCanFallbackCustomOnNestedArrayValueMissingIndexNotSkipped()
	{
		$populator = new Populator(array('foo' => array('bar' => array('bis' => 'ter'))));

		$this->assertEquals('custom', $populator->get('foo[nobar][bar]', 'custom'));
	}

	public function testCanFallbackDefaultOnMissingAttribute()
	{
		$values    = (object) array('foo' => array('bar' => array('bis' => 'ter')));
		$populator = new Populator($values);

		$this->assertEquals(null, $populator->get('nofoo'));
	}

	public function testCanFallbackCustomOnMissingAttribute()
	{
		$values    = (object) array('foo' => array('bar' => array('bis' => 'ter')));
		$populator = new Populator($values);

		$this->assertEquals('custom', $populator->get('nofoo', 'custom'));
	}

	public function testCanFallbackDefaultOnMissingNestedArrayAttribute()
	{
		$values    = (object) array('foo' => array('bar' => array('bis' => 'ter')));
		$populator = new Populator($values);

		$this->assertEquals(null, $populator->get('foo[nobar]'));
	}

	public function testCanFallbackCustomOnMissingNestedArrayAttribute()
	{
		$values    = (object) array('foo' => array('bar' => array('bis' => 'ter')));
		$populator = new Populator($values);

		$this->assertEquals('custom', $populator->get('foo[nobar]', 'custom'));
	}

	public function testCanFallbackDefaultOnMissingNestedObjectAttribute()
	{
		$values    = (object) array('foo' => (object) array('bar' => array('bis' => 'ter')));
		$populator = new Populator($values);

		$this->assertEquals(null, $populator->get('foo[nobar]'));
	}

	public function testCanFallbackCustomOnMissingNestedObjectAttribute()
	{
		$values    = (object) array('foo' => (object) array('bar' => array('bis' => 'ter')));
		$populator = new Populator($values);

		$this->assertEquals('custom', $populator->get('foo[nobar]', 'custom'));
	}

	public function testCanFallbackDefaultOnMissingRelationshipAttribute()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals(null, $populator->get('roles[0][foo]'));
	}

	public function testCanFallbackCustomOnMissingRelationshipAttribute()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals('custom', $populator->get('roles[0][foo]', 'custom'));
	}

	public function testCanFallbackDefaultOnMissingRelationshipIndex()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals(null, $populator->get('roles[2][name]'));
	}

	public function testCanFallbackCustomOnMissingRelationshipIndex()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals('custom', $populator->get('roles[2][name]', 'custom'));
	}

	public function testCanFallbackDefaultOnMissingRelationshipCollectionItemAttribute()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals(null, $populator->get('rolesAsCollection[0][foo]'));
	}

	public function testCanFallbackCustomOnMissingRelationshipCollectionItemAttribute()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals('custom', $populator->get('rolesAsCollection[0][foo]', 'custom'));
	}

	public function testCanFallbackDefaultOnMissingRelationshipCollectionIndex()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals(null, $populator->get('rolesAsCollection[2][name]'));
	}

	public function testCanFallbackCustomOnMissingRelationshipCollectionIndex()
	{
		$model     = new DummyEloquent(array('id' => 1, 'name' => 'foo'));
		$populator = new Populator($model);

		$this->assertEquals('custom', $populator->get('rolesAsCollection[2][name]', 'custom'));
	}

	public function testCanAvoidFallbackCustom()
	{
		$values    = (object) array('foo' => (object) array('bar' => array('bis' => 'ter')));
		$populator = new Populator($values);

		$this->assertEquals('ter', $populator->get('foo[bar][bis]', array('bis' => 'ter')));
	}

	public function testCanGetClassNamesImplementingToArray()
	{
		$populator = new Populator(array('foo' => '\Former\Dummy\DummyEloquent'));

		$this->assertEquals('\Former\Dummy\DummyEloquent', $populator->get('foo[0]'));
	}
}
