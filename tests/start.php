<?php
// Start Former
Bundle::start('former');

// Start Bootstrapper if installed (as it sometimes alias Form)
if (Bundle::exists('bootstrapper')) {
  Bundle::start('bootstrapper');
}

// Start session (don't know why I get bugs with this sometimes)
Session::start('file');

// Base Test class for matchers
abstract class FormerTests extends PHPUnit_Framework_TestCase
{
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

  public static function setUpBeforeClass()
  {
    URL::$base = 'http://test';
    Config::set('application.languages', array('fr', 'en'));
    Config::set('application.index', '');
    Config::set('application.language', 'en');
    Config::set('application.ssl', true);
  }

  public function setUp()
  {
    $this->resetLabels();
    Input::clear();
    \Former\Former::horizontal_open()->__toString();
    \Former\Former::populate(array());
    \Former\Former::withErrors(null);
    \Former\Former::config('automatic_label', true);
    \Former\Former::config('push_checkboxes', false);
    \Former\Former::framework('bootstrap');
  }

  public function tearDown()
  {
    \Former\Former::close();
  }

  public function resetLabels()
  {
    \Form::$labels = array();
  }
}
