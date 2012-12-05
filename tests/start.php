<?php
// Base Test class for matchers
abstract class FormerTests extends PHPUnit_Framework_TestCase
{
  protected $app;

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

  protected function cgr($input, $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group required">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  protected function cg($input = '<input type="text" name="foo" id="foo">', $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  protected function cgm($input, $label = '<label class="control-label">Foo</label>')
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  public function setUp()
  {
    $this->app = $this->getApplication();
    $this->resetLabels();
    //Input::clear();
    $this->app['former']->horizontal_open()->__toString();
    $this->app['former']->populate(array());
    $this->app['former']->withErrors(null);
    $this->app['former']->config('automatic_label', true);
    $this->app['former']->config('push_checkboxes', false);
    $this->app['former']->framework('bootstrap');
  }

  public function tearDown()
  {
    $this->app['former']->close();
  }

  public function resetLabels()
  {
    \Laravel\Form::$labels = array();
  }

  protected function getApplication()
  {
    $app = new Illuminate\Container;
    $app['former'] = new Former\Former($app);
    $app['former.helpers'] = new Former\Helpers($app);

    return $app;
  }
}
