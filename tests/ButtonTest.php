<?php
use \Former\Former;

include '_start.php';

class ButtonTest extends FormerTests
{
  public function testCanCreateAButton()
  {
    $button = $this->former->button('Save')->__toString();
    $matcher = '<button class="btn">Save</button>';

    $this->assertEquals($matcher, $button);
  }

  public function testCanChainMethodsToAButton()
  {
    $button = $this->former->button('Save')->class('btn btn-primary')->value('Cancel')->__toString();
    $matcher = '<button class="btn btn-primary">Cancel</button>';

    $this->assertEquals($matcher, $button);
  }

  public function testCanCreateASubmitButton()
  {
    $button = $this->former->submit('Save')->class('btn btn-primary')->__toString();
    $matcher = '<input class="btn btn-primary" type="submit" value="Save">';

    $this->assertEquals($matcher, $button);
  }

  public function testCanUseFormerObjectMethods()
  {
    $button = $this->former->button('pagination.next')->setAttributes($this->testAttributes)->__toString();
    $matcher = '<button class="foo" data-foo="bar">Next &raquo;</button>';

    $this->assertEquals($matcher, $button);
  }

  public function testCanDynamicallyCreateButtons()
  {
    $button = $this->former->large_block_primary_submit('Save')->__toString();
    $matcher = '<input class="btn-large btn-block btn-primary btn" type="submit" value="Save">';

    $this->assertEquals($matcher, $button);
  }

  public function testCanCreateAResetButton()
  {
    $button = $this->former->large_block_inverse_reset('Reset')->__toString();
    $matcher = '<input class="btn-large btn-block btn-inverse btn" type="reset" value="Reset">';

    $this->assertEquals($matcher, $button);
  }

  public function testCanHaveMultipleInstancesOfAButton()
  {
    $multiple = array($this->former->submit('submit'), $this->former->reset('reset'));
    $multiple = implode(' ', $multiple);
    $matcher = '<input class="btn" type="submit" value="Submit"> <input class="btn" type="reset" value="Reset">';

    $this->assertEquals($matcher, $multiple);
  }
}
