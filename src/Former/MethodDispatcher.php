<?php
namespace Former;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Support\Str;

/**
 * Dispatch calls from Former to the different
 * form creators like Form, Actions, Elements and others
 */
class MethodDispatcher
{
  /**
   * The IoC Container
   *
   * @var Container
   */
  protected $app;

  /**
   * An array of fields repositories
   *
   * @var array
   */
  protected $repositories = array();

  /**
   * Build a new Dispatcher
   *
   * @param Container $app
   */
  public function __construct(Container $app, $repositories)
  {
    $this->app          = $app;
    $this->repositories = (array) $repositories;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// REPOSITORIES /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Add a fields repository
   *
   * @param string $repository
   */
  public function addRepository($repository)
  {
    array_unshift($repository, $this->repositories);

    return $this;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// DISPATCHERS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Dispatch a call to a registered macro
   *
   * @param  string $method       The macro's name
   * @param  array  $parameters   The macro's arguments
   *
   * @return mixed
   */
  public function toMacros($method, $parameters)
  {
    if (!$this->app['former']->hasMacro($method)) {
      return false;
    }

    // Get and format macro
    $callback = $this->app['former']->getMacro($method);
    if ($callback instanceof Closure) {
      return call_user_func_array($callback, $parameters);
    }

    // Cancel if the macro is invalid
    elseif (!is_string($callback)) {
      return false;
    }

    // Get class and method
    list($class, $method) = explode('@', $callback);
    $this->app->instance('Illuminate\Container\Container', $this->app);

    return call_user_func_array(array($this->app->make($class), $method), $parameters);
  }

  /**
   * Dispatch a call over to Elements
   *
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return string
   */
  public function toElements($method, $parameters)
  {
    // Disregards if the method isn't an element
    if (!method_exists($elements = new Form\Elements($this->app, $this->app['session']), $method)) {
      return false;
    }

    return call_user_func_array(array($elements, $method), $parameters);
  }

  /**
   * Dispatch a call over to Form
   *
   * @param string  $method     The method called
   * @param array   $parameters Its parameters
   *
   * @return Form
   */
  public function toForm($method, $parameters)
  {
    // Disregards if the method doesn't contain 'open'
    if (!Str::contains($method, 'open') and !Str::contains($method, 'Open')) {
      return false;
    }

    $form = new Form\Form($this->app, $this->app['url'], $this->app['former.populator']);

    return $form->openForm($method, $parameters);
  }

  /**
   * Dispatch a call over to Group
   *
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return Group
   */
  public function toGroup($method, $parameters)
  {
    // Disregards if the method isn't "group"
    if ($method != 'group') {
      return false;
    }

    // Create opener
    $group = new Form\Group(
      $this->app,
      array_get($parameters, 0, null),
      array_get($parameters, 1, null)
    );

    // Set custom group as true
    Form\Group::$opened = true;

    return $group;
  }

  /**
   * Dispatch a call over to Actions
   *
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return Actions
   */
  public function toActions($method, $parameters)
  {
    if ($method != 'actions') {
      return false;
    }

    return new Form\Actions($this->app, $parameters);
  }

  /**
   * Dispatch a call over to the Fields
   *
   * @param string    $method     The method called
   * @param array     $parameters Its parameters
   *
   * @return Field
   */
  public function toFields($method, $parameters)
  {
    // Listing parameters
    $class = $this->getClassFromMethod($method);
    $field = new $class(
      $this->app,
      $method,
      array_get($parameters, 0),
      array_get($parameters, 1),
      array_get($parameters, 2),
      array_get($parameters, 3),
      array_get($parameters, 4),
      array_get($parameters, 5)
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
  protected function getClassFromMethod($method)
  {
    // If the field's name directly match a class, call it
    $class = Str::singular(Str::title($method));
    foreach ($this->repositories as $repository) {
      if (class_exists($repository.$class)) {
        return $repository.$class;
      }
    }

    // Else convert known fields to their classes
    switch ($method) {
      case 'submit':
      case 'link':
      case 'reset':
        $class = Former::FIELDSPACE.'Button';
        break;

      case 'multiselect':
        $class = Former::FIELDSPACE.'Select';
        break;

      default:
        $class = Former::FIELDSPACE.'Input';
        break;
    }

    return $class;
  }
}
