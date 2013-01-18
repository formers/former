<?php
include '_illuminate.php';

// Base Test class for matchers
abstract class FormerTests extends PHPUnit_Framework_TestCase
{
  protected static $illuminate;

  // Dummy data ---------------------------------------------------- /

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

  // Matchers ------------------------------------------------------ /

  protected function matchField($attributes = array(), $type = 'text', $name = 'foo')
  {
    $attributes = array_merge($attributes, array('type' => $type, 'name' => $name));

    return array(
      'tag'        => 'input',
      'id'         => $name,
      'attributes' => $attributes,
    );
  }

  protected function matchLabel($name = 'foo', $required = false)
  {
    $text = str_replace('[]', null, ucfirst($name));
    if ($required) $text .= '*';

    return array(
      'tag' => 'label',
      'content' => $text,
      'attributes' => array(
        'for'   => $name,
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
    $this->app->app['form']->labels = array();
  }

  // Custom assertions --------------------------------------------- /

  protected function assertControlGroup($input)
  {
    $this->assertLabel($input);
    $this->assertHTML($this->matchControlGroup(), $input);
  }

  protected function assertLabel($input, $name = 'foo', $required = false)
  {
    $this->assertHTML($this->matchLabel($name, $required), $input);
  }

  protected function controlGroup($input = '<input type="text" name="foo" id="foo" />', $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  protected function controlGroupRequired($input, $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group required">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  protected function controlGroupMultiple($input, $label = '<label class="control-label">Foo</label>')
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

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
