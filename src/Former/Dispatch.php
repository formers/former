<?php
namespace Former;

use Illuminate\Container\Container;
use Underscore\Methods\ArraysMethods as Arrays;
use Underscore\Types\String;

/**
 * Dispatch calls from Former to the different
 * form creators like Form, Actions, Elements and others
 */
class Dispatch
{

  /**
   * Dispatch a call to a registered macro
   *
   * @param  Former $former
   * @param  string $method       The macro's name
   * @param  array  $parameters   The macro's arguments
   *
   * @return mixed
   */
  public static function toMacros(Former $former, $method, $parameters)
  {
    if (!$former->hasMacro($method)) return false;

    return call_user_func_array($former->getMacro($method), $parameters);
  }

  /**
   * Dispatch a call over to Elements
   *
   * @param Container $app        The application container
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return string
   */
  public static function toElements(Container $app, $method, $parameters)
  {
    // Disregards if the method isn't an element
    if (!method_exists($elements = new Form\Elements($app['former'], $app['session']), $method)) return false;

    return call_user_func_array(array($elements, $method), $parameters);
  }

  /**
   * Dispatch a call over to Form
   *
   * @param Former  $app        The application container
   * @param string  $method     The method called
   * @param array   $parameters Its parameters
   *
   * @return Form
   */
  public static function toForm(Former $former, $method, $parameters)
  {
    // Disregards if the method doesn't contain 'open'
    if (!String::contains($method, 'open')) return false;

    $form = new Form\Form($former, $former->getContainer('url'), $former->getPopulator());

    return $form->openForm($method, $parameters);
  }

  /**
   * Dispatch a call over to Group
   *
   * @param Former    $app        The application container
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return Group
   */
  public static function toGroup(Former $former, $method, $parameters)
  {
    // Disregards if the method isn't "group"
    if ($method != 'group') return false;

    return new Form\Group(
      $former,
      Arrays::get($parameters, 0, null),
      Arrays::get($parameters, 1, array())
    );
  }

  /**
   * Dispatch a call over to Actions
   *
   * @param Former    $app        The application container
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return Actions
   */
  public static function toActions(Former $former, $method, $parameters)
  {
    if ($method != 'actions') return false;

    return new Form\Actions($former, $parameters);
  }

  /**
   * Dispatch a call over to the Fields
   *
   * @param Former    $app        The application container
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return Field
   */
  public static function toFields(Former $former, $method, $parameters)
  {
    // Listing parameters
    $class = Former::FIELDSPACE.static::getClassFromMethod($method);
    $field = new $class(
      $former,
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
  protected static function getClassFromMethod($method)
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
