<?php
class DummyEloquent
{
  private $id;
  private $name;

  public function __construct($array)
  {
    $this->id = $array['id'];
    $this->name = $array['name'];
  }

  public function __get($key)
  {
    if ($key == 'roles') return $this->roles();
  }

  public function __isset($key)
  {
    if ($key == 'roles') return true;
  }

  public function roles()
  {
    return array(
      new DummyEloquent(array('id' => 1, 'name' => 'foo')),
      new DummyEloquent(array('id' => 3, 'name' => 'bar')),
    );
  }

  public function getKey()
  {
    return $this->id;
  }

  public function __toString()
  {
    return $this->name;
  }
}