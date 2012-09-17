<?php
Bundle::start('former');
Bundle::start('bootstrapper');
Session::start('file');

class FormerTests extends PHPUnit_Framework_TestCase
{
  private function cg($label, $input)
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  public function testTrue()
  {
    $this->assertTrue(true);
  }
}