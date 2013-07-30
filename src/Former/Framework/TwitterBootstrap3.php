<?php
namespace Former\Framework;

use Former\Interfaces\FrameworkInterface;
use Former\Traits\Field;
use Former\Traits\Framework;
use HtmlObject\Element;
use Illuminate\Container\Container;
use Underscore\Methods\ArraysMethods as Arrays;
use Underscore\Methods\StringMethods as String;

/**
 * The Twitter Bootstrap form framework
 */
class TwitterBootstrap3 extends TwitterBootstrap implements FrameworkInterface
{

  /**
   * The button types available
   * @var array
   */
  private $buttons = array(
    'large', 'small', 'block',
    'default', 'primary', 'success', 'info', 'warning', 'danger', 'link'
  );

  /**
   * The field sizes available
   * @var array
   */
  private $fields = array(
    'col-1', 'col-2', 'col-3', 'col-4', 'col-5', 'col-6', 'col-7', 'col-8', 'col-9',
    'col-10', 'col-11', 'col-12',
    'col-sm-1', 'col-sm-2', 'col-sm-3', 'col-sm-4', 'col-sm-5', 'col-sm-6', 'col-sm-7',
    'col-sm-8', 'col-sm-9', 'col-sm-10', 'col-sm-11', 'col-sm-12',
    'col-lg-1', 'col-lg-2', 'col-lg-3', 'col-lg-4', 'col-lg-5', 'col-lg-6', 'col-lg-7',
    'col-lg-8', 'col-lg-9', 'col-lg-10', 'col-lg-11', 'col-lg-12'
  );

  /**
   * The field states available
   * @var array
   */
  protected $states = array(
    'has-success', 'has-warning', 'has-error',
  );

  /**
   * Create a new TwitterBootstrap instance
   *
   * @param \Illuminate\Container\Container $app
   */
  public function __construct(Container $app)
  {
    $this->app = $app;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// FILTER ARRAYS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Filter field classes
   *
   * @param  array $classes An array of classes
   * @return array A filtered array
   */
  public function filterFieldClasses($classes)
  {
    // Filter classes
    $classes = array_intersect($classes, $this->fields);

    // Prepend field type
    $classes = Arrays::each($classes, function($class) {
      return String::startsWith($class, 'col') ? $class : 'input-'.$class;
    });

    return $classes;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// ADD CLASSES //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Add group classes
   *
   * @return string A list of group classes
   */
  public function getGroupClasses()
  {
    return 'form-group';
  }

  /**
   * Add actions block class
   *
   * @param  array  $attributes The attributes
   * @return array
   */
  public function getActionClasses()
  {
    return '';
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// RENDER BLOCKS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Render an icon
   *
   * @param string $icon       The icon name
   * @param array  $attributes Its attributes
   *
   * @return string
   */
  public function createIcon($iconType, $attributes = array())
  {
    $icon = Element::create('glyphicon', null, $attributes);

    // Check for empty icons
    if (!$iconType) return false;

    // Create icon
    $icon->addClass('glyphicon-'.$iconType);

    return $icon;
  }

}
