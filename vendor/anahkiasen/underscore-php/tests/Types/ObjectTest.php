<?php
use Underscore\Types\Object;

class ObjectTest extends UnderscoreWrapper
{

  public function testCanCreateObject()
  {
    $object = Object::create();

    $this->assertInstanceOf('stdClass', $object->obtain());
  }

  public function testCanObjectifyAnArray()
  {
    $object = Object::from(array('foo' => 'bar'));
    $this->assertEquals('bar', $object->foo);

    $object->bis = 'ter';
    $this->assertEquals('ter', $object->bis);

    $this->assertEquals(array('foo' => 'bar', 'bis' => 'ter'), (array) $object->obtain());
  }

  public function testCanGetKeys()
  {
    $object = Object::keys($this->object);

    $this->assertEquals(array('foo', 'bis'), $object);
  }

  public function testCanGetValues()
  {
    $object = Object::Values($this->object);

    $this->assertEquals(array('bar', 'ter'), $object);
  }

  public function testCanGetMethods()
  {
    $methods = array(
      'getDefault',
      'toArray',
      '__construct',
      '__toString',
      'create',
      'from',
      '__get',
      '__set',
      'isEmpty',
      'setSubject',
      'obtain',
      'extend',
      '__callStatic',
      '__call',
    );

    $this->assertEquals($methods, Object::methods(new DummyDefault));
  }

  public function testCanPluckColumns()
  {
    $object = Object::pluck($this->objectMulti, 'foo');
    $matcher = (object) array('bar', 'bar', null);

    $this->assertEquals($matcher, $object);
  }

  public function testCanSetValues()
  {
    $object = (object) array('foo' => array('foo' => 'bar'), 'bar' => 'bis');
    $object = Object::set($object, 'foo.bar.bis', 'ter');

    $this->assertEquals('ter', $object->foo['bar']['bis']);
    $this->assertObjectHasAttribute('bar', $object);
  }

  public function testCanRemoveValues()
  {
    $array = Object::remove($this->objectMulti, '0.foo');
    $matcher = (array) $this->objectMulti;
    unset($matcher[0]->foo);

    $this->assertEquals((object) $matcher, $array);
  }

  public function testCanConvertToJson()
  {
    $under = Object::toJSON($this->object);

    $this->assertEquals('{"foo":"bar","bis":"ter"}', $under);
  }

  public function testCanSort()
  {
    $child = (object) array('sort' => 5);
    $child_alt = (object) array('sort' => 12);
    $object = (object) array('name' => 'foo', 'age' => 18, 'child' => $child);
    $object_alt = (object) array('name' => 'bar', 'age' => 21, 'child' => $child_alt);
    $collection = array($object, $object_alt);

    $under = Object::sort($collection, 'name', 'asc');
    $this->assertEquals(array($object_alt, $object), $under);

    $under = Object::sort($collection, 'child.sort', 'desc');
    $this->assertEquals(array($object_alt, $object), $under);

    $under = Object::sort($collection, function($value) {
      return $value->child->sort;
    }, 'desc');
    $this->assertEquals(array($object_alt, $object), $under);
  }

  public function testCanConvertToArray()
  {
    $object = Object::toArray($this->object);

    $this->assertEquals($this->array, $object);
  }

  public function testCanUnpackObjects()
  {
    $multi = (object) array('attributes' => array('name' => 'foo', 'age' => 18));
    $objectAuto = Object::unpack($multi);
    $objectManual = Object::unpack($multi, 'attributes');

    $this->assertObjectHasAttribute('name', $objectAuto);
    $this->assertObjectHasAttribute('age', $objectAuto);
    $this->assertEquals('foo', $objectAuto->name);
    $this->assertEquals(18, $objectAuto->age);
    $this->assertEquals($objectManual, $objectAuto);
  }

  public function testCanReplaceValues()
  {
    $object = Object::replace($this->object, 'foo', 'notfoo', 'notbar');
    $matcher = (object) array('notfoo' => 'notbar', 'bis' => 'ter');

    $this->assertEquals($matcher, $object);
  }

  public function testCanSetAnGetValues()
  {
    $object  = $this->object;
    $getset = Object::setAndGet($object, 'set', 'get');
    $get    = Object::get($object, 'set');

    $this->assertEquals($getset, 'get');
    $this->assertEquals($get, $getset);
  }
}
