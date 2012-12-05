<?php
use \Former\Former;
use \Former\Framework;

class FrameworkTest extends FormerTests
{
  public function testChangeViaFormerOptions()
  {
    Former::config('framework', 'zurb');

    $this->assertEquals(Framework::current(), 'zurb');
  }

  public function testChangeViaFramework()
  {
    Framework::useFramework('zurb');

    $this->assertEquals(Framework::current(), 'zurb');
  }

  public function testChangeViaFormer()
  {
    Former::framework('zurb');

    $this->assertEquals(Framework::current(), 'zurb');
  }
}
