<?php
class SelectTest extends FormerTests
{
  public function testSelect()
  {
    $select = Former::select('foo')->__toString();
    $matcher = $this->cg('<select id="foo" name="foo"></select>');

    $this->assertEquals($select, $matcher);
  }
}