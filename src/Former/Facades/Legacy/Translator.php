<?php
/**
 * Translator
 *
 * Redirect calls to the Translator instance to Laravel's
 * original Lang class
 */
namespace Former\Facades\Legacy;

class Translator extends Redirector
{
  /**
   * The name of the class to redirect to
   * @var string
   */
  protected $class = 'Lang';

  /**
   * Redirect calls to get to the old line method
   *
   * @param string $key The key to get
   *
   * @return string The translated string
   */
  public function get($key)
  {
    return \Lang::line($key)->get();
  }
}