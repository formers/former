<?php
Bundle::start('former');
Bundle::start('bootstrapper');
Session::start('file');

class FormerTests extends PHPUnit_Framework_TestCase
{
  private function baseMatcher()
  {

  }

  public function testTrue()
  {
    $this->assertTrue(true);
  }
}