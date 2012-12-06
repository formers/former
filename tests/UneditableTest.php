<?php
use \Former\Former;

class UneditableTest extends FormerTests
{
  public function testUneditable()
  {
    $input = $this->app['former']->uneditable('foo')->value('bar')->__toString();
    $matcher = $this->cg('<span class="uneditable-input">bar</span>');

    $this->assertEquals($matcher, $input);
  }
}
