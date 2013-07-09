<?php
namespace Underscore;

use Closure;
use InvalidArgumentException;

/**
 * Dispatches methods and classes to various places
 */
class Dispatch
{

  /**
   * The namespace containing the Type classes
   */
  const TYPES = 'Underscore\Types\\';

  /**
   * An array of PHP types and what classes they map to
   *
   * @var array
   */
  protected static $classmap = array(
    'array'    => 'Arrays',
    'double'   => 'Number',
    'closure'  => 'Functions',
    'float'    => 'Number',
    'integer'  => 'Number',
    'NULL'     => 'String',
    'object'   => 'Object',
    'real'     => 'Number',
    'string'   => 'String',
  );

  /**
   * Compute the right class to call according to something's type
   *
   * @param mixed   $subjet The subject of a class
   * @return string Its fully qualified corresponding class
   */
  public static function toClass($subject)
  {
    $subjectType = gettype($subject);
    if ($subject instanceof Closure) $subjectType = 'closure';

    // Return correct class
    if (array_key_exists($subjectType, static::$classmap)) {
      return static::TYPES.static::$classmap[$subjectType];
    }

    throw new InvalidArgumentException('The type ' .$subjectType. ' is not supported');
  }

  /**
   * Defer a method to native PHP
   *
   * @param string $class  The class
   * @param string $method The method
   *
   * @return string The correct function to call
   */
  public static function toNative($class, $method)
  {
    // Aliased native function
    $native = Method::getNative($method);
    if ($native) return $native;

    // Transform class to php function prefix
    switch ($class) {
      case static::TYPES.'Arrays':
        $prefix = 'array_';
        break;

      case static::TYPES.'String':
        $prefix = 'str_';
        break;
    }

    // If no function prefix found, return false
    if (!isset($prefix)) return false;

    // Native function
    $function = $prefix.$method;
    if (function_exists($function)) return $function;

    return false;
  }

}
