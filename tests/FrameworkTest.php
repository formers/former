<?php
use \Former\Former;
use \Former\Framework;

class FrameworkTest extends FormerTests
{
  public function testChangeViaFormerOptions()
  {
    $this->app['former']->config('framework', 'zurb');

    $this->assertEquals(Framework::current(), 'zurb');
  }

  public function testChangeViaFramework()
  {
    Framework::useFramework('zurb');

    $this->assertEquals(Framework::current(), 'zurb');
  }

  public function testChangeViaFormer()
  {
    $this->app['former']->framework('zurb');

    $this->assertEquals(Framework::current(), 'zurb');
  }
}
