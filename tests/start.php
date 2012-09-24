<?php
// Start Former
Bundle::start('former');

// Start Bootstrapper if installed (as it sometimes alias Form)
if(Bundle::exists('bootstrapper')) {
  Bundle::start('bootstrapper');
}

// Start session (don't know why I get bugs with this sometimes)
Session::start('file');

// Base Test class for matchers
class FormerTests extends PHPUnit_Framework_TestCase
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

  protected function cgr($input, $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group required">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  protected function cg($input, $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  protected function cgm($input, $label = '<label class="control-label">Foo</label>')
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  public function setUp()
  {
    \Former\Former::horizontal_open();
    \Former\Former::populate(array());
    \Former\Former::withErrors(null);
  }

  public function tearDown()
  {
    \Former\Former::close();
  }

  /**
   * This function has no points
   * It's only here because I can't extend Framework_TestCase otherwise
   */
  public function testTrue()
  {
    $this->assertTrue(true);
  }
}