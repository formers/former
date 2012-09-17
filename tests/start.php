<?php
Bundle::start('former');
Bundle::start('bootstrapper');
Session::start('file');

class FormerTests extends PHPUnit_Framework_TestCase
{
  protected function cg(
    $input,
    $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  public function testTrue()
  {
    $this->assertTrue(true);
  }
}