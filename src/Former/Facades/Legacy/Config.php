<?php
namespace Former\Facades\Legacy;

class Config extends Redirector
{
  protected $class = 'Config';

  public function get($key, $fallback = null)
  {
    $key = str_replace('former::', 'former::config.', $key);

    return \Config::get($key, $fallback);
  }
}