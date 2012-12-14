<?php
class ZurbTest extends FormerTests
{

  public function setUp()
  {
    parent::setUp();
    $this->former->framework('ZurbFoundation');
  }

  // Matchers ------------------------------------------------------ /

  public function matchLabel($name = 'foo', $required = false)
  {
    return array(
      'tag' => 'label',
      'content' => ucfirst($name),
      'attributes' => array(
        'for'   => $name,
      ),
    );
  }

  // Tests --------------------------------------------------------- /

  public function testMagicMethods()
  {
    $input = $this->former->three_text('foo')->__toString();

    $this->assertLabel($input);
    $this->assertHTML($this->matchField(), $input);
  }

  public function testErrorState()
  {
    $input = $this->former->text('foo')->state('error')->__toString();
    $matcher = array('tag' => 'div', 'attributes' => array('class' => 'error'));

    $this->assertLabel($input);
    $this->assertHTML($this->matchField(), $input);
    $this->assertHTML($matcher, $input);
  }

  public function testHelp()
  {
    $input = $this->former->text('foo')->inlineHelp('bar')->__toString();
    $matcher = array('tag' => 'small', 'content' => 'Bar');

    $this->assertLabel($input);
    $this->assertHTML($this->matchField(), $input);
    $this->assertHTML($matcher, $input);
  }

  public function testCantUseBlockHelp()
  {
    $this->setExpectedException('BadMethodCallException');

    $this->former->text('foo')->blockHelp('bar')->__toString();
  }
}
