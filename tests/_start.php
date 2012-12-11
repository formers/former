<?php
include '_illuminate.php';

// Base Test class for matchers
abstract class FormerTests extends PHPUnit_Framework_TestCase
{
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

  protected function controlGroup($input = '<input type="text" name="foo" id="foo">', $label = '<label for="foo" class="control-label">Foo</label>')
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

  // Setup --------------------------------------------------------- /

  /**
   * Setup the app for testing
   */
  public function setUp()
  {
    // Create the dummy Illuminate app
    $this->app = new IlluminateMock();
    $this->former = $this->app->app['former'];

    // Reset some parameters
    $this->former->horizontal_open()->__toString();
    $this->former->framework('TwitterBootstrap');
  }

  public function tearDown()
  {
    $this->former->close();

    Mockery::close();
  }

  public function resetLabels()
  {
    $this->app->app['former.laravel.form']->labels = array();
  }
}
