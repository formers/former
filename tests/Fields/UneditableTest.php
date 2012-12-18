<?php
class UneditableTest extends FormerTests
{
  // Matchers ------------------------------------------------------ /

  public function matchPlainLabel()
  {
    return array(
      'tag' => 'label',
      'attributes' => array('for' => 'foo'),
    );
  }

  public function matchInput()
  {
    return array(
      'tag' => 'input',
      'attributes' => array(
        'disabled' => 'true',
        'type'     => 'text',
        'name'     => 'foo',
        'value'    => 'bar',
        'id'       => 'foo',
      ),
    );
  }

  public function matchSpan()
  {
    return array(
      'tag' => 'span',
      'content' => 'bar',
      'attributes' => array(
        'class' => 'uneditable-input',
      ),
    );
  }

  // Tests --------------------------------------------------------- /

  public function testCanCreateClassicDisabledFields()
  {
    $this->former->framework('Nude');
    $nude = $this->former->uneditable('foo')->value('bar')->__toString();

    $this->assertHTML($this->matchPlainLabel(), $nude);
    $this->assertHTML($this->matchInput(), $nude);

    $this->former->framework('ZurbFoundation');
    $zurb = $this->former->uneditable('foo')->value('bar')->__toString();

    $this->assertHTML($this->matchPlainLabel(), $zurb);
    $this->assertHTML($this->matchInput(), $zurb);
  }

  public function testCanCreateUneditableFieldsWithBootstrap()
  {
    $input = $this->former->uneditable('foo')->value('bar')->__toString();

    $this->assertControlGroup($input);
    $this->assertHTML($this->matchSpan(), $input);
  }
}
