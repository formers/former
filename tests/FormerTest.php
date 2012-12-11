<?php
use \Former\Former;

class FormerTest extends FormerTests
{
  public function testLegend()
  {
    $legend = $this->former->legend('test', array('class' => 'foo', 'data-foo' => 'bar'));
    $matcher = '<legend class="foo" data-foo="bar">Test</legend>';

    $this->assertEquals($matcher, $legend);
  }

  public function testToken()
  {
    $token = $this->former->token();
    $matcher = '<input type="hidden" name="csrf_token" value="csrf_token">';

    $this->assertEquals($matcher, $token);
  }

  public function testAction()
  {
    $action = $this->former->actions('foo');
    $matcher = '<div class="form-actions">foo</div>';

    $this->assertEquals($matcher, $action);
  }

  public function testActions()
  {
    $actions = $this->former->actions('foo', 'bar');
    $matcher = '<div class="form-actions">foo bar</div>';

    $this->assertEquals($matcher, $actions);
  }
}
