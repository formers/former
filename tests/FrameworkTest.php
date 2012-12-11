<?php
use \Former\Former;
use \Former\Framework;

class FrameworkTest extends FormerTests
{
  public function testCanChangeFramework()
  {
    $this->former->framework('ZurbFoundation');

    $this->assertEquals('ZurbFoundation', $this->former->framework());
  }
}
