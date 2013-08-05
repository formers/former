<?php
use Illuminate\Container\Container;
use Former\FormerServiceProvider;

/**
 * Dummy Illuminate app for testing purposes
 */
abstract class IlluminateMock extends PHPUnit_Framework_TestCase
{
  /**
   * A cache of the container
   *
   * @var Container
   */
  protected static $appCache;

  /**
   * The current instance of the Container
   *
   * @var Container
   */
  protected $app;

  /**
   * Build the IoC Container for the tests
   */
  public function setUp()
  {
    if (!static::$appCache) {
      $app = FormerServiceProvider::make();

      // Setup Illuminate
      $app['session']    = $this->mockSession();
      $app['translator'] = $this->mockTranslator();
      $app['url']        = $this->mockUrl();
      $app['validator']  = $this->mockValidator();

      static::$appCache = $app;
    }

    $this->app = static::$appCache;
    $this->app['config']  = $this->mockConfig();
    $this->app['request'] = $this->mockRequest();
  }

  /**
   * Get an instance on the Container
   *
   * @param  string $key
   *
   * @return object
   */
  public function __get($key)
  {
    return $this->app->make($key);
  }

  /**
   * Set an instance on the Container
   *
   * @param string $key
   * @param object $value
   */
  public function __set($key, $value)
  {
    return $this->app[$key] = $value;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// CONTAINER ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get config manager
   *
   * @param boolean $live      Whether live validation should be active
   * @param string  $unchecked The checkable unchecked value
   * @param boolean $push      Whether unchecked checkboxes should be pushed
   * @param boolean $automatic Automatic live validation or not
   */
  protected function mockConfig($live = true, $unchecked = '', $push = false, $automatic = true, $errors = true)
  {
    $config = Mockery::mock('config');
    $config->shouldReceive('get')->with('application.encoding', Mockery::any())->andReturn('UTF-8');
    $config->shouldReceive('get')->with('former::default_form_type', Mockery::any())->andReturn('horizontal');
    $config->shouldReceive('get')->with('former::fetch_errors', Mockery::any())->andReturn(false);
    $config->shouldReceive('get')->with('former::framework')->andReturn('TwitterBootstrap');
    $config->shouldReceive('get')->with('former::translate_from', Mockery::any())->andReturn('validation.attributes');
    $config->shouldReceive('get')->with('former::required_class', Mockery::any())->andReturn('required');
    $config->shouldReceive('get')->with('former::required_text',  Mockery::any())->andReturn('*');

    // Variable configuration keys
    $config->shouldReceive('get')->with('former::live_validation', Mockery::any())->andReturn($live);
    $config->shouldReceive('get')->with('former::unchecked_value', Mockery::any())->andReturn($unchecked);
    $config->shouldReceive('get')->with('former::push_checkboxes', Mockery::any())->andReturn($push);
    $config->shouldReceive('get')->with('former::automatic_label', Mockery::any())->andReturn($automatic);
    $config->shouldReceive('get')->with('former::error_messages',  Mockery::any())->andReturn($errors);
    $config->shouldReceive('get')->with('former::translatable',    Mockery::any())->andReturn(array(
      'help', 'inlineHelp', 'blockHelp',
      'placeholder', 'data_placeholder',
      'label'
    ));

    $config->shouldReceive('set')->with(Mockery::any(), Mockery::any());

    return $config;
  }

  /**
   * Get URL manager
   */
  protected function mockUrl()
  {
    $url = Mockery::mock('Illuminate\Routing\UrlGenerator');
    $url->shouldReceive('getRequest')->andReturn($this->mockRequest());
    $url->shouldReceive('to')->andReturnUsing(function ($url) {
      return $url == '#' ? $url : 'https://test/en/'.$url;
    });

    return $url;
  }

  /**
   * Get Validator
   */
  protected function mockValidator()
  {
    $messageBag = $this->mockMessageBag(array('required' => 'The required field is required.'));

    $validator = Mockery::mock('Illuminate\Validation\Validator');
    $validator->shouldReceive('getMessageBag')->andReturn($messageBag);

    return $validator;
  }

  /**
   * Get Session manager
   */
  protected function mockSession($errors = null)
  {
    $session = Mockery::mock('Illuminate\Session\Store');
    $session->shouldReceive('getToken')->andReturn('csrf_token');

    if ($errors) {
      $messageBag = $this->mockMessageBag($errors);
      $session->shouldReceive('has')->with('errors')->andReturn(true);
      $session->shouldReceive('get')->with('errors')->andReturn($messageBag);
    } else {
      $session->shouldReceive('has')->with('errors')->andReturn(false);
      $session->shouldReceive('get')->with('errors')->andReturn(null);
    }

    return $session;
  }

  /**
   * Mock a MessageBag instance
   *
   * @param  array $errors
   *
   * @return Mockery
   */
  protected function mockMessageBag(array $errors)
  {
    $messages = Mockery::mock('MessageBag');
    foreach ($errors as $key => $value) {
      $messages->shouldReceive('has')->with($key)->andReturn(true);
      $messages->shouldReceive('first')->with($key)->andReturn($value);
    }
    $messages->shouldReceive('first')->withAnyArgs()->andReturn(null);

    return $messages;
  }

  /**
   * Get localization manager
   */
  protected function mockTranslator()
  {
    $translator = Mockery::mock('Illuminate\Translation\Translator');
    $translator->shouldReceive('get')->with('pagination.next')->andReturn('Next');
    $translator->shouldReceive('get')->with('pagination')->andReturn(array('previous' => 'Previous', 'next' => 'Next'));
    $translator->shouldReceive('get')->withAnyArgs()->andReturnUsing(function ($key) {
      return $key;
    });
    $translator->shouldReceive('has')->withAnyArgs()->andReturn(true);

    return $translator;
  }

  /**
   * Get request manager
   */
  protected function mockRequest()
  {
    $request = Mockery::mock('Illuminate\Http\Request');
    $request->shouldReceive('url')->andReturn('#');
    $request->shouldReceive('get')->andReturn(null)->byDefault();
    $request->shouldReceive('old')->andReturn(null);
    $request->shouldReceive('path')->andReturnUsing(function () {
      return 'https://test/en/';
    });

    return $request;
  }
}
