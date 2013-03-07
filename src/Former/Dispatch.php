<?php
namespace Former;

use Underscore\Types\Arrays;
use Underscore\Types\String;

/**
 * Dispatch calls from Former to the different
 * form creators like Form, Actions, Elements and others
 */
class Dispatch
{
  /**
   * Dispatch a call over to Elements
   *
   * @param Container $app        The application container
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
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
   * @param Container $app        The application container
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return Form
   */
  public static function toForm($app, $method, $parameters)
  {
    // Disregards if the method doesn't contain 'open'
    if (!String::contains($method, 'open')) return false;

    $form = new Form\Form($app);

    return $form->openForm($method, $parameters);
  }

  /**
   * Dispatch a call over to Group
   *
   * @param Container $app        The application container
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return Group
   */
  public static function toGroup($app, $method, $parameters)
  {
    // Disregards if the method isn't "group"
    if ($method != 'group') return false;
    return new Form\Group(
      $app,
      Arrays::get($parameters, 0),
      Arrays::get($parameters, 1, array())
    );
  }

  /**
   * Dispatch a call over to Actions
   *
   * @param Container $app        The application container
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return Actions
   */
  public static function toActions($app, $method, $parameters)
  {
    if ($method != 'actions') return false;
    return new Form\Actions($app, $parameters);
  }

  /**
   * Dispatch a call over to the Fields
   *
   * @param Container $app        The application container
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return Field
   */
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
   * Get the correct class to call according to the created field
   *
   * @param  string $method The field created
   *
   * @return string The correct class
   */
  private static function getClassFromMethod($method)
  {
    // If the field's name directly match a class, call it
    $class = String::from($method)->singular()->title()->obtain();
    if (class_exists(Former::FIELDSPACE.$class)) {
      return $class;
    }

    // Else convert known fields to their classes
    switch ($method) {
      case 'submit':
      case 'link':
      case 'reset':
        $class = 'Button';
        break;
      case 'multiselect':
        $class = 'Select';
        break;
      default:
        $class = 'Input';
        break;
    }

    return $class;
  }
}
