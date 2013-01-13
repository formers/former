<?php
include '_start.php';

class ActionsTest extends FormerTests
{
  // Matchers ------------------------------------------------------ /

  public function matchActions($content = 'foo')
  {
    return array(
      'tag'        => 'div',
      'content'    => $content,
      'attributes' => array('class' => 'form-actions'),
    );
  }

  public function matchButton($classes, $type, $value)
  {
    return array(
      'tag'  => 'input',
      'attributes' => array(
        'type' => $type,
        'value' => $value,
        'class' => $classes. ' btn',
      ),
    );
  }

  // Tests --------------------------------------------------------- /

  public function testCanCreateAnActionBlock()
  {
    $action = $this->former->actions('foo')->__toString();

    $this->assertHTML($this->matchActions(), $action);
  }

  public function testCanCreateAnActionsBlock()
  {
    $actions = $this->former->actions('foo', 'bar')->__toString();

    $this->assertHTML($this->matchActions('foo bar'), $actions);
  }

  public function testCanCreateAnActionsBlockWithTags()
  {
    $actions = $this->former->actions('<button>Submit</button>', '<button type="reset">Reset</button>')->__toString();
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
    $actions = $this->former->actions($this->former->submit('submit'), $this->former->reset('reset'))->__toString();
    $matcher = '<div class="form-actions"><input class="btn" type="submit" value="Submit" /> <input class="btn" type="reset" value="Reset" /></div>';

    $this->assertEquals($matcher, $actions);
  }

  public function testCanChainMethodsToActionsBlock()
  {
    $actions = $this->former->actions('content')->id('foo')->addClass('bar')->data_foo('bar')->__toString();
    $matcher = $this->matchActions('content');
    $matcher['attributes'] = array(
      'id'       => 'foo',
      'class'    => 'bar form-actions',
      'data-foo' => 'bar',
    );

    $this->assertHTML($matcher, $actions);
  }

  public function testCanChainActionsToActionsBlock()
  {
    $actions = $this->former->actions()
      ->data_submit('foo')
      ->large_primary_submit('submit')
      ->small_block_inverse_reset('reset')
      ->__toString();

    // Match block
    $matcher = $this->matchActions();
    unset($matcher['content']);
    $this->assertHTML($matcher, $actions);

    // Match buttons
    $matcher = $this->matchButton('btn-large btn-primary', 'submit', 'Submit');
    $this->assertHTML($matcher, $actions);

    $matcher = $this->matchButton('btn-small btn-block btn-inverse', 'reset', 'Reset');
    $this->assertHTML($matcher, $actions);
  }
}
