<?php
use \Former\Former;

include 'start.php';

class ButtonTest extends FormerTests
{
  public function testButton()
  {
    $button = Former::button('Save')->__toString();
    $matcher = '<button class="btn">Save</button>';

    $this->assertEquals($matcher, $button);
  }

  public function testButtonMethods()
  {
    $button = Former::button('Save')->class('btn btn-primary')->value('Cancel')->__toString();
    $matcher = '<button class="btn btn-primary">Cancel</button>';

    $this->assertEquals($matcher, $button);
  }

  public function testSubmit()
  {
    $button = Former::submit('Save')->class('btn btn-primary')->__toString();
    $matcher = '<input class="btn btn-primary" type="submit" value="Save">';

    $this->assertEquals($matcher, $button);
  }

  public function testAttributes()
  {
    $button = Former::button('pagination.next')->setAttributes($this->testAttributes)->__toString();
    $matcher = '<button class="foo" data-foo="bar">Next &raquo;</button>';

    $this->assertEquals($matcher, $button);
  }

  public function testDynamicCalls()
  {
    $button = Former::large_block_primary_submit('Save')->__toString();
    $matcher = '<input class="btn-large btn-block btn-primary btn" type="submit" value="Save">';

    $this->assertEquals($matcher, $button);
  }

  public function testResetButton()
  {
    $button = Former::large_block_inverse_reset('Reset')->__toString();
    $matcher = '<input class="btn-large btn-block btn-inverse btn" type="reset" value="Reset">';

    $this->assertEquals($matcher, $button);
  }

  public function testMultipleInstances()
  {
    $multiple = array(Former::submit('submit'), Former::reset('reset'));
    $multiple = implode(' ', $multiple);
    $matcher = '<input class="btn" type="submit" value="Submit"> <input class="btn" type="reset" value="Reset">';

    $this->assertEquals($matcher, $multiple);
  }
}
