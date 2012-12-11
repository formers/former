<?php
class UneditableTest extends FormerTests
{
  public function testUneditable()
  {
    $input = $this->former->uneditable('foo')->value('bar')->__toString();
    $matcher = $this->controlGroup('<span class="uneditable-input">bar</span>');

    $this->assertEquals($matcher, $input);
  }
}
