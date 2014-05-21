<?php
use Illuminate\Container\Container;
use Former\FormerServiceProvider;

/**
 * A TestCase that creates a mocked Container to use as core
 */
abstract class ContainerTestCase extends PHPUnit_Framework_TestCase
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
      $this->app = FormerServiceProvider::make();

      // Setup Illuminate
      $this->mockSession();
      $this->mockTranslator();
      $this->mockUrl();
      $this->mockValidator();

      static::$appCache = $this->app;
    }

    $this->app = static::$appCache;
    $this->mockConfig();
    $this->mockRequest();
  }

  /**
   * Bind mocked expectations into the Container
   *
   * @param string $binding
   * @param string $name
   * @param Closure $expectations
   *
   * @return Mockery
   */
  public function mock($binding, $name, $expectations)
  {
    $mocked = Mockery::mock($name);
    $mocked = $expectations($mocked);
    if ($mocked instanceof Mockery\CompositeExpectation) {
      $mocked = $mocked->mock();
    }

    // Bind into container
    $this->$binding = $mocked;

    return $mocked;
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
   * Mock the Config repository
   *
   * @return Mockery
   */
  protected function mockConfig(array $options = array())
  {
    $defaults = include realpath(__DIR__.'/../../src/config/config.php');

    $options  = array_merge($defaults, array(
      'automatic_label'         => true,
      'capitalize_translations' => true,
      'default_form_type'       => 'horizontal',
      'error_messages'          => true,
      'fetch_errors'            => false,
      'framework'               => 'TwitterBootstrap',
      'icon_prefix'             => null,
      'icon_set'                => null,
      'icon_tag'                => null,
      'live_validation'         => true,
      'push_checkboxes'         => false,
      'required_class'          => 'required',
      'required_text'           => '*',
      'translate_from'          => 'validation.attributes',
      'unchecked_value'         => '',
      'translatable'            => array('help', 'inlineHelp', 'blockHelp', 'placeholder', 'data_placeholder', 'label'),

      'Nude.icon.prefix'                    => 'icon',
      'Nude.icon.set'                       => null,
      'Nude.icon.tag'                       => 'i',
      'Nude.labelWidths'                    => null,
      'TwitterBootstrap.icon.prefix'        => 'icon',
      'TwitterBootstrap.icon.set'           => null,
      'TwitterBootstrap.icon.tag'           => 'i',
      'TwitterBootstrap.labelWidths'        => null,
      'TwitterBootstrap3.icon.prefix'       => 'glyphicon',
      'TwitterBootstrap3.icon.set'          => 'glyphicon',
      'TwitterBootstrap3.icon.tag'          => 'span',
      'TwitterBootstrap3.labelWidths'       => array('large' => 2, 'small' => 4),
      'TwitterBootstrap3.viewports'         => array('large' => 'lg', 'medium' => 'md', 'small' => 'sm', 'mini' => 'xs'),
      'ZurbFoundation.icon.prefix'          => 'fi',
      'ZurbFoundation.icon.set'             => null,
      'ZurbFoundation.icon.tag'             => 'i',
      'ZurbFoundation.labelWidths'          => array('large' => 2, 'small' => 4),
      'ZurbFoundation.viewports'            => array('large' => '', 'medium' => null, 'small' => 'mobile-', 'mini' => null),
      'ZurbFoundation.wrappedLabelClasses'  => array('right', 'inline'),
      'ZurbFoundation.error_classes'       => array('class' => 'alert-box alert error'),
      'ZurbFoundation4.icon.prefix'         => 'foundicon',
      'ZurbFoundation4.icon.set'            => 'general',
      'ZurbFoundation4.icon.tag'            => 'i',
      'ZurbFoundation4.labelWidths'         => array('small' => 3),
      'ZurbFoundation4.viewports'           => array('large' => 'large', 'medium' => null, 'small' => 'small', 'mini' => null),
      'ZurbFoundation4.wrappedLabelClasses' => array('right','inline'),
      'ZurbFoundation4.error_classes'       => array('class' => 'alert-box radius warning'),
    ), $options);

    return $this->mock('config', 'Config', function ($mock) use ($options) {
      $mock->shouldReceive('application.encoding', Mockery::any())->andReturn('UTF-8');
      $mock->shouldReceive('set')->with(Mockery::any(), Mockery::any());

      foreach ($options as $key => $value) {
        $mock->shouldReceive('get')->with('former::'.$key)->andReturn($value);
        $mock->shouldReceive('get')->with('former::'.$key, Mockery::any())->andReturn($value);
      }

      return $mock;
    });
  }

  /**
   * Mock the UrlGenerator
   *
   * @return Mockery
   */
  protected function mockUrl()
  {
    $request = $this->mockRequest();

    $this->mock('url', 'Illuminate\Routing\UrlGenerator', function ($mock) use ($request) {
      return $mock
        ->shouldReceive('getRequest')->andReturn($request)
        ->shouldReceive('to')->andReturnUsing(function ($url) {
          return $url == '#' ? $url : 'https://test/en/'.$url;
        })
        ->shouldReceive('action')->with('UsersController@edit', array(2))->andReturn('/users/2/edit')
        ->shouldReceive('action')->with('UsersController@edit', 2)->andReturn('/users/2/edit')
        ->shouldReceive('route')->with('user.edit', array(2))->andReturn('/users/2/edit')
        ->shouldReceive('route')->with('user.edit', 2)->andReturn('/users/2/edit');
    });
  }

  /**
   * Mock a Validator instance
   *
   * @return Mockery
   */
  protected function mockValidator()
  {
    $messageBag = $this->mockMessageBag(array(
      'required' => 'The required field is required.'
    ));

    return $this->mock('validator', 'Illuminate\Validation\Validator', function ($mock) use ($messageBag) {
      return $mock->shouldReceive('getMessageBag')->andReturn($messageBag);
    });
  }

  /**
   * Mock the current Session store
   *
   * @param array $errors
   *
   * @return Mockery
   */
  protected function mockSession(array $errors = array())
  {
    $messageBag = $this->mockMessageBag($errors);

    return $this->mock('session', 'Illuminate\Session\Store', function ($mock) use ($messageBag, $errors) {
      $mock->shouldReceive('getToken')->andReturn('csrf_token');
      if ($errors) {
        $mock->shouldReceive('has')->with('errors')->andReturn(true);
        $mock->shouldReceive('get')->with('errors')->andReturn($messageBag);
      } else {
        $mock->shouldReceive('has')->with('errors')->andReturn(false);
        $mock->shouldReceive('get')->with('errors')->andReturn(null);
      }

      return $mock;
    });
  }

  /**
   * Mock a MessageBag instance
   *
   * @param  array $errors
   *
   * @return Mockery
   */
  protected function mockMessageBag(array $errors = array())
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
   * Mock the localization manager
   *
   * @return Mockery
   */
  protected function mockTranslator()
  {
    return $this->mock('translator', 'Illuminate\Translation\Translator', function ($mock) {
      return $mock
        ->shouldReceive('get')->with('pagination.next')->andReturn('Next')
        ->shouldReceive('get')->with('pagination')->andReturn(array('previous' => 'Previous', 'next' => 'Next'))
        ->shouldReceive('get')->with('validation.attributes.field_name_with_underscore')->andReturn(false)
        ->shouldReceive('get')->with('validation.attributes.address.city')->andReturn('City')
        ->shouldReceive('get')->withAnyArgs()->andReturnUsing(function ($key) { return $key; })
        ->shouldReceive('has')->with('field_name_with_underscore')->andReturn(false)
        ->shouldReceive('has')->with('address.city')->andReturn(false)
        ->shouldReceive('has')->with('address[city]')->andReturn(false)
        ->shouldReceive('has')->withAnyArgs()->andReturn(true);
    });
  }

  /**
   * Mock the Request instance
   *
   * @return Mockery
   */
  protected function mockRequest()
  {
    return $this->mock('request', 'Illuminate\Http\Request', function ($mock) {
      return $mock
        ->shouldReceive('url')->andReturn('#')
        ->shouldReceive('input')->andReturn(null)->byDefault()
        ->shouldReceive('old')->andReturn(null)
        ->shouldReceive('path')->andReturnUsing(function () {
          return 'https://test/en/';
        });
    });
  }
}
