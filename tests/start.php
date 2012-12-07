<?php

// Base Test class for matchers
abstract class FormerTests extends PHPUnit_Framework_TestCase
{
  protected static $illuminate;

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

  // Setup --------------------------------------------------------- /

  public function setUp()
  {
    $this->app = $this->getIlluminate();

    $this->resetLabels();

    $this->app['former']->horizontal_open()->__toString();
    $this->app['former']->populate(array());
    $this->app['former']->withErrors(null);
    $this->app['former']->framework('bootstrap');
  }

  public function tearDown()
  {
    Mockery::close();
    $this->app['former']->close();
  }

  public function resetLabels()
  {
    $this->app['former.laravel.form']->labels = array();
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// DEPENDENCIES ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  private function getIlluminate()
  {
    $app = new Illuminate\Container;

    $app['session'] = Mockery::mock('session');
    $app['session']->shouldReceive('has')->with('errors')->andReturn(false);
    $app['session']->shouldReceive('set')->with('errors')->andReturn(false);
    $app['session']->shouldReceive('getToken')->andReturn('csrf_token');

    $app['config'] = Mockery::mock('config');
    $app['config']->shouldReceive('get')->with('former::fetch_errors')->andReturn(false);
    $app['config']->shouldReceive('get')->with('former::push_checkboxes')->andReturn(false);
    $app['config']->shouldReceive('get')->with('former::framework')->andReturn('bootstrap');
    $app['config']->shouldReceive('get')->with('former::live_validation')->andReturn(true);
    $app['config']->shouldReceive('get')->with('former::required_class')->andReturn('required');
    $app['config']->shouldReceive('get')->with('former::required_text')->andReturn('<sup>*</sup>');
    $app['config']->shouldReceive('get')->with('former::unchecked_value')->andReturn('');
    $app['config']->shouldReceive('get')->with('former::automatic_label')->andReturn(true);
    $app['config']->shouldReceive('get')->with('former::default_form_type')->andReturn('horizontal');
    $app['config']->shouldReceive('get')->with('application.encoding')->andReturn('UTF-8');
    $app['config']->shouldReceive('set')->with('former::framework', 'bootstrap')->andSet('framework', 'bootstrap');
    $app['config']->shouldReceive('set')->with('former::framework', 'zurb')->andSet('framework', 'zurb');

    $app['translator'] = Mockery::mock('translator');
    $app['translator']->shouldReceive('get')->with('pagination.next')->andReturn('Next &raquo;');
    $app['translator']->shouldReceive('get')->with('pagination')->andReturn(array('previous' => '&laquo; Previous', 'next' => 'Next &raquo;'));
    $app['translator']->shouldReceive('get')->with(Mockery::any())->andReturnUsing(function($test) {
      return $test;
    });

    $app['request'] = $this->getRequest();

    $app['url'] = Mockery::mock('url');
    $app['url']->shouldReceive('to')->andReturnUsing(function($url) {
      return $url == '#' ? $url : 'https://test/en/'.$url;
    });

    $app['former.laravel.form'] = $app->share(function($app) { return new Laravel\Form($app); });
    $app['former.laravel.html'] = $app->share(function($app) { return new Laravel\HTML($app); });
    $app['former'] = $app->share(function($app) { return new Former\Former($app); });
    $app['former.helpers'] = $app->share(function($app) { return new Former\Helpers($app); });
    $app['former.framework'] = $app->share(function($app) { return new Former\Framework($app); });

    return $app;
  }

  private function getRequest()
  {
    $request = Mockery::mock('request');
    $request->shouldReceive('url')->andReturn('#');
    $request->shouldReceive('get')->andReturn(null)->byDefault();
    $request->shouldReceive('old')->andReturn(null);

    return $request;
  }
}
