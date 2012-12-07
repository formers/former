<?php
use \Former\Former;
use \Former\Framework;

class FrameworkTest extends FormerTests
{
  public function testChangeViaFormerOptions()
  {
    $this->app['former']->config('framework', 'ZurbFoundation');

    $this->assertEquals('ZurbFoundation', $this->app['former']->framework());
  }

  public function testChangeViaFormer()
  {
    $this->app['former']->framework('ZurbFoundation');

    $this->assertEquals('ZurbFoundation', $this->app['former']->framework());
  }
}
