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

  public function matchLabel($name = null, $required = null)
  {
    return array(
      'tag' => 'label',
      'content' => 'Foo',
      'attributes' => array('for' => '')
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

  public function testCanCreateFormLabels()
  {
    $label = $this->former->label('foo');

    $this->assertHTML($this->matchLabel(), $label);
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

  public function testCanCreateAnActionsBlockWithTags()
  {
    $actions = $this->former->actions('<button>Submit</button>', '<button type="reset">Reset</button>');
    $matcher = $this->matchActions();
    unset($matcher['content']);
    $matcher['children'] = array(
      'count' => 2,
      'only' => array(
        'tag' => 'button',
      ),
    );

    $this->assertHTML($matcher, $actions);
  }

  public function testCanUseObjectsAsActions()
  {
    $actions = $this->former->actions($this->former->submit('submit'), $this->former->reset('reset'));
    $matcher = '<div class="form-actions"><input class="btn" type="submit" value="Submit"> <input class="btn" type="reset" value="Reset"></div>';

    $this->assertEquals($matcher, $actions);
  }
}
