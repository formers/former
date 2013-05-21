<?php
use Underscore\Methods\ArraysMethods as Arrays;

class HiddenTest extends FormerTests
{

  public function testCanCreateHiddenField()
  {
    $input = $this->former->hidden('foo')->value('bar')->__toString();
    $field = Arrays::remove($this->matchField(array(), 'hidden'), 'id');

    $this->assertHTML($field, $input);
  }

  public function testCanPopulateHiddenFields()
  {
    $this->former->populate(array('foo' => 'bar'));
    $input = $this->former->hidden('foo')->value('bis')->__toString();

    $field = Arrays::remove($this->matchField(array(), 'hidden'), 'id');
    $field['attributes']['value'] = 'bar';

    $this->assertHTML($field, $input);
  }

}
