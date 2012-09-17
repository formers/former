<?php
class SelectTest extends FormerTests
{
  public function testSelect()
  {
    $select = Former::select('foo')->__toString();
    $matcher = $this->cg(
      '<label for="foo" class="control-label">Foo</label>',
      '<select id="foo" name="foo"></select>'
    );

    $this->assertEquals($select, $matcher);
  }
}