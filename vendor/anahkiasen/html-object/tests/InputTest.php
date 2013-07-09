<?php
use HtmlObject\Input;

class InputTest extends HtmlObjectTests
{
  public function testCanCreateBasicInput()
  {
    $input = new Input('text', 'foo', 'bar');
    $matcher = $this->getInputMatcher('text', 'foo', 'bar');

    $this->assertHTML($matcher, $input);
  }

  public function testCanDynamicallyCreateInputTypes()
  {
    $input1 = Input::create('text', 'foo', 'bar');
    $input2 = Input::text('foo', 'bar');
    $matcher = $this->getInputMatcher('text', 'foo', 'bar');

    $this->assertEquals($input1, $input2);
    $this->assertHTML($matcher, $input2);
  }
}