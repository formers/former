<?php
class ButtonTest extends FormerTests
{
  // Matchers ------------------------------------------------------ /

  public function matchButton($class, $text, $attributes = array())
  {
    $matcher = array(
      'tag'        => 'button',
      'content'    => $text,
      'attributes' => array(
        'class' => $class,
      ),
    );

    // Supplementary attributes
    if ($attributes) {
      $matcher['attributes'] = array_merge($matcher['attributes'], $attributes);
    }

    return $matcher;
  }

  public function matchInputButton($class, $text, $type = 'submit')
  {
    return array(
      'tag'        => 'input',
      'attributes' => array(
        'type'  => $type,
        'value' => $text,
        'class' => $class,
      ),
    );
  }

  // Tests --------------------------------------------------------- /

  public function testCanCreateAButton()
  {
    $button  = $this->former->button('Save')->__toString();
    $matcher = $this->matchButton('btn', 'Save');

    $this->assertHTML($matcher, $button);
  }

  public function testCanChainMethodsToAButton()
  {
    $button  = $this->former->button('Save')->class('btn btn-primary')->value('Cancel')->__toString();
    $matcher = $this->matchButton('btn btn-primary', 'Cancel');

    $this->assertHTML($matcher, $button);
  }

  public function testCanCreateASubmitButton()
  {
    $button  = $this->former->submit('Save')->class('btn btn-primary')->__toString();
    $matcher = $this->matchInputButton('btn btn-primary', 'Save');

    $this->assertHTML($matcher, $button);
  }

  public function testCanUseFormerObjectMethods()
  {
    $button  = $this->former->button('pagination.next')->setAttributes($this->testAttributes)->__toString();
    $matcher = $this->matchButton('foo', 'Next', array('data-foo' => 'bar'));

    $this->assertHtml($matcher, $button);
  }

  public function testCanDynamicallyCreateButtons()
  {
    $button  = $this->former->large_block_primary_submit('Save')->__toString();
    $matcher = $this->matchInputButton('btn-large btn-block btn-primary btn', 'Save');

    $this->assertHTML($matcher, $button);
  }

  public function testCanCreateAResetButton()
  {
    $button  = $this->former->large_block_inverse_reset('Reset')->__toString();
    $matcher = $this->matchInputButton('btn-large btn-block btn-inverse btn', 'Reset', 'reset');

    $this->assertHTML($matcher, $button);
  }

  public function testCanHaveMultipleInstancesOfAButton()
  {
    $multiple = array($this->former->submit('submit'), $this->former->reset('reset'));
    $multiple = implode(' ', $multiple);
    $matcher = '<input class="btn" type="submit" value="Submit" /> <input class="btn" type="reset" value="Reset" />';

    $this->assertEquals($matcher, $multiple);
  }
}
