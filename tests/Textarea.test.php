<?php
use \Former\Former;

class TextareaTest extends FormerTests
{
  public function testUneditable()
  {
    $textarea = Former::textarea('foo')->setAttributes($this->testAttributes)->value('bar')->__toString();
    $matcher = $this->cg('<textarea class="foo" data-foo="bar" name="foo" id="foo" rows="10" cols="50">bar</textarea>');

    $this->assertEquals($matcher, $textarea);
  }
}
