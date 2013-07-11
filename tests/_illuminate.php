<?php
use Illuminate\Container\Container;

/**
 * Dummy Illuminate app for testing purposes
 */
class IlluminateMock
{

  /**
   * The current instance of the Container
   *
   * @var Container
   */
  public $app;

  /**
   * Build the IoC Container for the tests
   */
  public function __construct()
  {
    $app = new Container;

    // Setup Illuminate
    $app['config']     = $this->getConfig();
    $app['request']    = $this->getRequest();
    $app['session']    = $this->getSession();
    $app['translator'] = $this->getTranslator();
    $app['Illuminate\Routing\UrlGenerator'] = $this->getUrl();
    $app->alias('Illuminate\Routing\UrlGenerator', 'url');
    $app['validator']  = $this->getValidator();

    // Setup bindings
    $serviceProvider = new Former\FormerServiceProvider($app);
    $serviceProvider->bindCoreClasses($app);
    $serviceProvider->bindFormer($app);

    $this->app = $app;

    return $this;
  }

  /**
   * Get the container
   */
  public function get()
  {
    return $this->app;
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
  public function getConfig($live = true, $unchecked = '', $push = false, $automatic = true, $errors = true)
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
  private function getUrl()
  {
    $url = Mockery::mock('Illuminate\Routing\UrlGenerator');
    $url->shouldReceive('getRequest')->andReturn($this->getRequest());
    $url->shouldReceive('to')->andReturnUsing(function ($url) {
      return $url == '#' ? $url : 'https://test/en/'.$url;
    });

    return $url;
  }

  /**
   * Get Validator
   */
  private function getValidator()
  {
    $validator = Mockery::mock('Illuminate\Validation\Validator');
    $validator->shouldReceive('getMessages')->andReturnUsing(function () {
      $messages = Mockery::mock('MessageBag');
      $messages->shouldReceive('first')->with('required')->andReturn('The required field is required.');

      return $messages;
    });

    return $validator;
  }

  /**
   * Get Session manager
   */
  public function getSession($errors = null)
  {
    $session = Mockery::mock('Illuminate\Session\Store');
    if ($errors) {
      $session->shouldReceive('has')->with('errors')->andReturn(true);
      $session->shouldReceive('get')->with('errors')->andReturnUsing(function () use ($errors) {
        $messages = Mockery::mock('MessageBag');
        foreach ($errors as $key => $value) {
          $messages->shouldReceive('first')->with($key)->andReturn($value);
        }
        $messages->shouldReceive('first')->withAnyArgs()->andReturn(null);

        return $messages;
      });
    } else {
      $session->shouldReceive('has')->with('errors')->andReturn(false);
      $session->shouldReceive('get')->with('errors')->andReturn(null);
    }
    $session->shouldReceive('getToken')->andReturn('csrf_token');

    return $session;
  }

  /**
   * Get localization manager
   */
  private function getTranslator()
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
  public function getRequest()
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
