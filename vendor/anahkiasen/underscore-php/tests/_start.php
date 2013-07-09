<?php
use Underscore\Types\Arrays;
use Underscore\Types\String;

abstract class UnderscoreWrapper extends PHPUnit_Framework_TestCase
{
  public $array = array('foo' => 'bar', 'bis' => 'ter');
  public $arrayNumbers = array(1, 2, 3);
  public $arrayMulti = array(
    array('foo' => 'bar', 'bis' => 'ter'),
    array('foo' => 'bar', 'bis' => 'ter'),
    array('bar' => 'foo', 'bis' => 'ter'),
  );
  public $object;

  /**
   * Starts the bundle
   */
  public static function setUpBeforeClass()
  {
    // If we're inside Laravel, autoload Underscore
    if (class_exists('Bundle')) {
      Bundle::start('underscore');
    }
  }

  /**
   * Restore data just in case
   */
  public function setUp()
  {
    $this->object = (object) $this->array;
    $this->objectMulti = (object) array(
      (object) $this->arrayMulti[0],
      (object) $this->arrayMulti[1],
      (object) $this->arrayMulti[2],
    );
  }
}

//////////////////////////////////////////////////////////////////////
///////////////////////////// DUMMY CLASSES //////////////////////////
//////////////////////////////////////////////////////////////////////

class DummyDefault extends String
{
  public function getDefault()
  {
    return 'foobar';
  }

  public function toArray()
  {
    return array('foo', 'bar');
  }
}

class DummyClass extends Arrays
{
  public function getUsers()
  {
    $users = array(
      array('foo' => 'bar'),
      array('bar' => 'foo'),
    );

    return $this->setSubject($users);
  }

  public function map($whatever)
  {
    $this->subject = $whatever * 3;

    return $this;
  }
}