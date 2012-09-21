<?php
use \Former\Former;

class FormerTest extends FormerTests
{
  public function testLegend()
  {
    $legend = Former::legend('test', array('class' => 'foo', 'data-foo' => 'bar'));
    $matcher = '<legend class="foo" data-foo="bar">Test</legend>';

    $this->assertEquals($matcher, $legend);
  }

  public function testToken()
  {
    $token = Former::token();
    $matcher = '<input type="hidden" name="csrf_token">';

    $this->assertEquals($matcher, $token);
  }
}