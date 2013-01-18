<?php
/**
 * Session
 *
 * Redirect calls to Session instance to Laravel's
 * original static class
 */
namespace Former\Facades\Legacy;

class Session extends Redirector
{
  /**
   * The name of the class to redirect to
   * @var string
   */
  protected $class = 'Session';

  /**
   * Get the CSRF token
   *
   * @return string A CSRF token
   */
  public function getToken()
  {
    return \Session::csrf_token;
  }
}