<?php
ini_set('memory_limit', '120M');
date_default_timezone_set('UTC');

// Load the Illuminate Container
include '_illuminate.php';

// Dummies
include 'Dummy/DummyButton.php';
include 'Dummy/DummyEloquent.php';

/**
 * Base testing class
 */
abstract class FormerTests extends PHPUnit_Framework_TestCase
{

  /**
   * The current IoC Container
   *
   * @var Container
   */
  protected static $illuminate;

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// DUMMIES /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  protected $checkables = array(
    'Foo' => array(
      'data-foo' => 'bar',
      'value' => 'bar',
      'name' => 'foo',
    ),
    'Bar' => array(
      'data-foo' => 'bar',
      'value' => 'bar',
      'name' => 'foo',
      'id' => 'bar',
    ),
  );

  protected $testAttributes = array(
    'class'    => 'foo',
    'data-foo' => 'bar',
  );

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// MATCHERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  protected function matchField($attributes = array(), $type = 'text', $name = 'foo')
  {
    $attributes = array_merge($attributes, array('type' => $type, 'name' => $name));

    return array(
      'tag'        => 'input',
      'id'         => $name,
      'attributes' => $attributes,
    );
  }

  protected function matchLabel($name = 'foo', $field = 'foo', $required = false)
  {
    $text = str_replace('[]', null, ucfirst($name));
    if ($required) $text .= '*';

    return array(
      'tag' => 'label',
      'content' => $text,
      'attributes' => array(
        'for'   => $field,
        'class' => 'control-label',
      ),
    );
  }

  protected function matchControlGroup()
  {
    return array(
      'tag' => 'div',
      'attributes' => array(
        'class' => 'control-group',
      ),
      'child' => array(
        'tag' => 'div',
        'attributes' => array('class' => 'controls'),
      ),
    );
  }

  protected function matchButton($class, $text, $attributes = array())
  {
    $matcher = array(
      'tag'        => 'button',
      'content'    => $text,
      'attributes' => array(
        'class' => $class,
      ),
    );

    // Supplementary attributes
    if ($attributes) {
      $matcher['attributes'] = array_merge($matcher['attributes'], $attributes);
    }

    return $matcher;
  }

  protected function matchInputButton($class, $text, $type = 'submit')
  {
    return array(
      'tag'        => 'input',
      'attributes' => array(
        'type'  => $type,
        'value' => $text,
        'class' => $class,
      ),
    );
  }

  // Setup --------------------------------------------------------- /

  /**
   * Setup the app for testing
   */
  public function setUp()
  {
    // Create the dummy Illuminate app
    if (!static::$illuminate) static::$illuminate = new IlluminateMock();
    $this->app = static::$illuminate;
    $this->former = $this->app->app['former'];

    // Reset some parameters
    $this->resetLabels();
    $this->former->horizontal_open()->__toString();
    $this->former->framework('TwitterBootstrap');
  }

  public function tearDown()
  {
    Mockery::close();
    $this->former->close();

    // Reset config and POST data
    $this->app->app['config']  = static::$illuminate->getConfig();
    $this->app->app['request'] = static::$illuminate->getRequest();
  }

  public function resetLabels()
  {
    $this->former->labels = array();
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Build a list of HTML attributes from an array
   *
   * @param  array  $attributes
   * @return string
   */
  public function attributes($attributes)
  {
    $html = array();

    foreach ((array) $attributes as $key => $value)
    {
      // For numeric keys, we will assume that the key and the value are the
      // same, as this will convert HTML attributes such as "required" that
      // may be specified as required="required", etc.
      if (is_numeric($key)) $key = $value;

      if ( ! is_null($value))
      {
        $html[] = $key.'="'.$value.'"';
      }
    }

    return (count($html) > 0) ? ' '.implode(' ', $html) : '';
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// ASSERTIONS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Asserts that the input is a control group
   *
   * @param  string $input
   *
   * @return boolean
   */
  protected function assertControlGroup($input)
  {
    $this->assertLabel($input);
    $this->assertHTML($this->matchControlGroup(), $input);
  }

  /**
   * Asserts that the input is a label
   *
   * @param  string $input
   *
   * @return boolean
   */
  protected function assertLabel($input, $name = 'foo', $required = false)
  {
    $this->assertHTML($this->matchLabel($name, $name, $required), $input);
  }

  /**
   * Matches a Control Group
   *
   * @param  string $input
   * @param  string $label
   *
   * @return boolean
   */
  protected function controlGroup($input = '<input type="text" name="foo" id="foo">', $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  /**
   * Matches a required Control Group
   *
   * @param  string $input
   * @param  string $label
   *
   * @return boolean
   */
  protected function controlGroupRequired($input, $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group required">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  /**
   * Assert that a piece of HTML matches an array
   *
   * @param  array  $matcher
   * @param  string $input
   *
   * @return boolean
   */
  public function assertHTML($matcher, $input)
  {
    $this->assertTag(
      $matcher,
      $input,
      "Failed asserting that the HTML matches the provided format :\n\t"
        .$input."\n\t"
        .json_encode($matcher));
  }

}
