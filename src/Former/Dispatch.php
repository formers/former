<?php
/**
 * Dispatch
 *
 * Dispatch calls to Former to the different
 * form creators like Form, Actions, Elements and others
 */
namespace Former;

use \Underscore\Types\Arrays;
use \Underscore\Types\String;

class Dispatch
{
  /**
   * Dispatch a call over to Elements
   *
   * @return string
   */
  public static function toElements($app, $method, $parameters)
  {
    // Disregards if the method isn't an element
    if (!method_exists($elements = new Form\Elements($app), $method)) return false;

    return call_user_func_array(array($elements, $method), $parameters);
  }

  /**
   * Dispatch a call over to Form
   *
   * @return Form
   */
  public static function toForm($app, $method, $parameters)
  {
    // Disregards if the method doesn't contain 'open'
    if (!String::contains($method, 'open')) return false;

    $form = new Form\Form($app);

    return $form->open($method, $parameters);
  }

  /**
   * Dispatch a call over to Group
   *
   * @return Group
   */
  public static function toGroup($app, $method, $parameters)
  {
    // Disregards if the method isn't "group"
    if ($method != 'group') return false;
    $parameters = static::splitArguments($parameters,
      array('label', 'attributes' => array())
    );

    return new Form\Group($app, $parameters->label, $parameters->attributes);
  }

  /**
   * Dispatch a call over to Actions
   *
   * @return Actions
   */
  public static function toActions($app, $method, $parameters)
  {
    if ($method != 'actions') return false;

    return new Form\Actions($app, $parameters);
  }

  public static function toFields($app, $method, $parameters)
  {
    // Listing parameters
    $class = Former::FIELDSPACE.static::getClassFromMethod($method);
    $field = new $class(
      $app,
      $method,
      Arrays::get($parameters, 0),
      Arrays::get($parameters, 1),
      Arrays::get($parameters, 2),
      Arrays::get($parameters, 3),
      Arrays::get($parameters, 4),
      Arrays::get($parameters, 5)
    );

    return $field;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// HELPERS //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Split an array of arguments into actual arguments
   *
   * @param array $parameters The arguments
   * @param array $rules      The defaults and number of arguments to return
   *
   * @return array
   */
  private static function splitArguments($parameters, $rules)
  {
    $arguments = array();
    $count = 0;

    // Extract the arguments one by one
    foreach ($rules as $field => $default) {

      // Handles short-syntax for null arguments
      if (is_int($field)) {
        $field   = $default;
        $default = null;
      }

      // Fetch argument from the parameters
      $arguments[$field] = Arrays::get($parameters, $count, $default);
      $count++;
    }

    return (object) $arguments;
  }

  /**
   * Get the correct class to call according to the created field
   *
   * @param string $method The field created
   * @return string The correct class
   */
  private static function getClassFromMethod($method)
  {
    // If the field's name directly match a class, call it
    $class = ucfirst($method);
    if (class_exists(Former::FIELDSPACE.$class)) {
      return $class;
    }

    // Else convert known fields to their classes
    switch ($method) {
      case 'submit':
      case 'reset':
        $class = 'Button';
        break;
      case 'multiselect':
        $class = 'Select';
        break;
      case 'checkboxes':
        $class = 'Checkbox';
        break;
      case 'radios':
        $class = 'Radio';
        break;
      case 'files':
        $class = 'File';
        break;
      default:
        $class = 'Input';
        break;
    }

    return $class;
  }
}