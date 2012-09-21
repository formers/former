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
  protected function cg(
    $input,
    $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  public static function setUpBeforeClass()
  {
    \Former\Former::horizontal_open();
  }

  public static function tearDownAfterClass()
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