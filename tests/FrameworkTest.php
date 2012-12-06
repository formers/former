<?php
use \Former\Former;
use \Former\Framework;

class FrameworkTest extends FormerTests
{
  public function testChangeViaFormerOptions()
  {
    $this->app['former']->config('framework', 'zurb');

    $this->assertEquals('zurb', $this->app['former.framework']->current());
  }

  public function testChangeViaFramework()
  {
    $this->app['former.framework']->useFramework('zurb');

    $this->assertEquals('zurb', $this->app['former.framework']->current());
  }

  public function testChangeViaFormer()
  {
    $this->app['former']->framework('zurb');

    $this->assertEquals('zurb', $this->app['former.framework']->current());
  }
}
