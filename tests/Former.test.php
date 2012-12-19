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

  public function testAction()
  {
    $action = Former::actions('foo');
    $matcher = '<div class="form-actions">foo</div>';

    $this->assertEquals($matcher, $action);
  }

  public function testActions()
  {
    $actions = Former::actions('foo', 'bar');
    $matcher = '<div class="form-actions">foo bar</div>';

    $this->assertEquals($matcher, $actions);
  }

  public function testDoesntUseTranslationsArraysAsLabels()
  {
    $input = Former::text('pagination')->__toString();
    $matcher = $this->cg('<input type="text" name="pagination" id="pagination">', '<label for="pagination" class="control-label">Pagination</label>');

    $this->assertEquals($matcher, $input);
  }
}
