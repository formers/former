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
   * Set a property on a static class
   *
   * @param  string $key The property
   * @return string Its value
   */
  public function __set($key, $value)
  {
    $class = '\\'.$this->class;

    $class::$$key = $value;
  }

  /**
   * Get a property from a static class
   *
   * @param  string $key The property
   * @return string Its value
   */
  public function __get($key)
  {
    $class = '\\'.$this->class;

    return $class::$$key;
  }

  /**
   * Redirect a call to a static class
   */
  public function __call($method, $parameters)
  {
    return call_user_func_array('\\'.$this->class.'::'.$method, $parameters);
  }
}