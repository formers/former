<?php
class FrameworkTest extends FormerTests
{
  public function testCanChangeFramework()
  {
    $this->former->framework('ZurbFoundation');

    $this->assertEquals('ZurbFoundation', $this->former->framework());
  }

  public function testCanCheckWhatTheFrameworkIs()
  {
    $current   = $this->app->app['former.framework']->current();
    $isCurrent = $this->app->app['former.framework']->is($current);

    $this->assertTrue($isCurrent);
  }
}
