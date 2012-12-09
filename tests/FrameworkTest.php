<?php
use \Former\Former;
use \Former\Framework;

class FrameworkTest extends FormerTests
{
  public function testCanChangeFramework()
  {
    $this->app['former']->framework('ZurbFoundation');

    $this->assertEquals('ZurbFoundation', $this->app['former']->framework());
  }
}
