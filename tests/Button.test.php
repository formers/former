<?php
use \Former\Former;

include 'start.php';

class ButtonTest extends FormerTests
{
  public function testButton()
  {
    $button = Former::button('Save')->__toString();
    $matcher = '<input type="submit" value="Save">';

    $this->assertEquals($matcher, $button);
  }

  public function testButtonMethods()
  {
    $button = Former::button('Save')->class('btn btn-primary')->value('Cancel')->__toString();
    $matcher = '<input class="btn btn-primary" type="submit" value="Cancel">';

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
    $button = Former::button('validation.url')->setAttributes($this->testAttributes)->__toString();
    $matcher = '<input class="foo" data-foo="bar" type="submit" value="Click me">';

    $this->assertEquals($matcher, $button);
  }
}