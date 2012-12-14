<?php
class FormerTest extends FormerTests
{
  // Matchers ------------------------------------------------------ /

  public function matchLegend()
  {
    return array(
      'tag' => 'legend',
      'content' => 'Test',
      'attributes' => $this->testAttributes,
    );
  }

  public function matchToken()
  {
    return array(
      'tag' => 'input',
      'attributes' => array(
        'type'  => 'hidden',
        'name'  => 'csrf_token',
        'value' => 'csrf_token',
      ),
    );
  }

  public function matchActions($content = 'foo')
  {
    return array(
      'tag' => 'div',
      'content' => $content,
      'attributes' => array('class' => 'form-actions'),
    );
  }

  // Tests --------------------------------------------------------- /

  public function testCanCreateFormLegends()
  {
    $legend = $this->former->legend('test', $this->testAttributes);

    $this->assertHTML($this->matchLegend(), $legend);
  }

  public function testCanCreateCsrfTokens()
  {
    $token = $this->former->token();

    $this->assertHTML($this->matchToken(), $token);
  }

  public function testCanCreateAnActionBlock()
  {
    $action = $this->former->actions('foo');

    $this->assertHTML($this->matchActions(), $action);
  }

  public function testCanCreateAnActionsBlock()
  {
    $actions = $this->former->actions('foo', 'bar');

    $this->assertHTML($this->matchActions('foo bar'), $actions);
  }
}
