<?php
namespace Former\Facades\Legacy;

class Redirector
{
  protected $class;

  public function __construct($class = null)
  {
    if ($class) $this->class = $class;
  }

  public function __call($method, $parameters)
  {
    return call_user_func_array('\\'.$this->class.'::'.$method, $parameters);
  }
}