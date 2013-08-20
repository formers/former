<?php
class HelpersTest extends FormerTests
{
  public function testDoesntUseTranslationsArraysAsLabels()
  {
    $former = $this->former->text('pagination')->__toString();
    $matcher = $this->matchField(array(), 'text', 'pagination');

    $this->assertHTML($matcher, $former);
  }
}
