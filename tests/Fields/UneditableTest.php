<?php
class UneditableTest extends FormerTests
{
  public function testCanCreateClassicDisabledFields()
  {
    $this->former->framework('Nude');

    $input = $this->former->uneditable('foo')->value('bar')->__toString();
    $matcher = '<label for="foo">Foo</label><input disabled="true" type="text" name="foo" value="bar" id="foo">';

    $this->assertEquals($matcher, $input);
  }

  public function testCanCreateClassicDisabledFieldsWithZurb()
  {
    $this->former->framework('ZurbFoundation');

    $input = $this->former->uneditable('foo')->value('bar')->__toString();
    $matcher = '<div><label for="foo">Foo</label><input disabled="true" type="text" name="foo" value="bar" id="foo"></div>';

    $this->assertEquals($matcher, $input);
  }

  public function testCanCreateUneditableFieldsWithBootstrap()
  {
    $input = $this->former->uneditable('foo')->value('bar')->__toString();
    $matcher = $this->controlGroup('<span class="uneditable-input">bar</span>');

    $this->assertEquals($matcher, $input);
  }
}
