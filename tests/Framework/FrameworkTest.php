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
    $current   = $this->app['former.framework']->current();
    $isCurrent = $this->app['former.framework']->is($current);

    $this->assertTrue($isCurrent);
  }

  public function testCanCreateFieldOutsideOfForm()
  {
    $this->former->closeGroup();
    $this->former->close();

    $text = $this->former->text('foobar')->__toString();

    $this->assertEquals('<label for="foobar">Foobar</label><input id="foobar" type="text" name="foobar">', $text);
  }
}
