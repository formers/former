<?php

class HiddenTest extends FormerTests
{

  public function testCanCreateHiddenField()
  {
    $input   = $this->former->hidden('foo')->value('bar')->__toString();
    $matcher = $this->matchField(array(), 'hidden');
    $field   = array_except($matcher, 'id');

    $this->assertHTML($field, $input);
  }

  public function testCanPopulateHiddenFields()
  {
    $this->former->populate(array('foo' => 'bar'));

    $input   = $this->former->hidden('foo')->value('bis')->__toString();
    $matcher = $this->matchField(array(), 'hidden');
    $field   = array_except($matcher, 'id');
    $field['attributes']['value'] = 'bar';

    $this->assertHTML($field, $input);
  }

}
