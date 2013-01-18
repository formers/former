<?php
/**
 * Redirector
 *
 * Redirects chained calls to static classes
 */
namespace Former\Facades\Legacy;

class Redirector
{
  /**
   * The name of the class to redirect to
   * @var string
   */
  protected $class;

  /**
   * Set up a new Redirector
   *
   * @param string $class The class to redirect to
   */
  public function __construct($class = null)
  {
    if ($class) $this->class = $class;
  }

  /**
   * Redirect a call to a static class
   */
  public function __call($method, $parameters)
  {
    return call_user_func_array('\\'.$this->class.'::'.$method, $parameters);
  }
}