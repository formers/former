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
    $current   = $this->former->getFramework()->current();
    $isCurrent = $this->former->getFramework()->is($current);

    $this->assertTrue($isCurrent);
  }

}
