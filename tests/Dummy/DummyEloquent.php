<?php
class DummyEloquent
{
  private $id;
  private $name;

  public function __construct($array)
  {
    $this->id   = $array['id'];
    $this->name = $array['name'];
  }

  public function __get($key)
  {
    if ($key == 'roles') return $this->roles();
    if ($key == 'attribute_old') return $this->get_attribute_old();
    if ($key == 'custom') return $this->getCustomAttribute();

    return $this->$key;
  }

  public function __isset($key)
  {
    return in_array($key, array('id', 'name', 'roles'));
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

  // Mutators ------------------------------------------------------ /

  public function get_attribute_old()
  {
    return 'custom';
  }

  public function getCustomAttribute()
  {
    return 'custom';
  }

  public function __toString()
  {
    return $this->name;
  }
}