<?php
namespace Former;

use Former\TestCases\FormerTests;

class ActionsTest extends FormerTests
{
	////////////////////////////////////////////////////////////////////
	////////////////////////////// MATCHERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function matchActions($content = 'foo')
	{
		return array(
			'tag'        => 'div',
			'content'    => $content,
			'attributes' => array('class' => 'form-actions'),
		);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

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
			'only'  => array(
				'tag' => 'button',
			),
		);

		$this->assertHTML($matcher, $actions);
	}

	public function testCanUseObjectsAsActions()
	{
		$actions = $this->former->actions($this->former->submit('submit'), $this->former->reset('reset'))->__toString();

		$matcher          = array('tag' => 'div', 'attributes' => array('class' => 'form-actions'));
		$matcher['child'] = $this->matchInputButton('btn', 'Submit', 'submit');
		$this->assertHTML($matcher, $actions);

		$matcher['child'] = $this->matchInputButton('btn', 'Reset', 'reset');
		$this->assertHTML($matcher, $actions);
	}

	public function testCanChainMethodsToActionsBlock()
	{
		$actions               = $this->former->actions('content')->id('foo')->addClass('bar')->data_foo('bar')->__toString();
		$matcher               = $this->matchActions('content');
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
		$matcher['child'] = $this->matchInputButton('btn-large btn-primary btn', 'Submit', 'submit');
		$this->assertHTML($matcher, $actions);

		$matcher['child'] = $this->matchInputButton('btn-small btn-block btn-inverse btn', 'Reset', 'reset');
		$this->assertHTML($matcher, $actions);
	}
}
