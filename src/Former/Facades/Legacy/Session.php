<?php
namespace Former\Facades\Legacy;

class Session extends Redirector
{
  protected $class = 'Session';

  public function getToken()
  {
    return \Session::csrf_token;
  }
}