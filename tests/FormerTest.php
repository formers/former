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

  // Tests --------------------------------------------------------- /

  public function testCanCreateFormLegends()
  {
    $legend = $this->former->legend('test', $this->testAttributes);

    $this->assertHTML($this->matchLegend(), $legend);
  }

  public function testCanCreateFormLabels()
  {
    $label = $this->former->label('foo');

    $this->assertLabel($label);
  }

  public function testCanCreateCsrfTokens()
  {
    $token = $this->former->token();

    $this->assertHTML($this->matchToken(), $token);
  }
}
