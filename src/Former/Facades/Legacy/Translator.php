<?php
namespace Former\Facades\Legacy;

class Translator extends Redirector
{
  protected $class = 'Lang';

  public function get($key)
  {
    return \Lang::line($key)->get();
  }
}