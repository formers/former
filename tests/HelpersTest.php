<?php
class HelpersTest extends FormerTests
{
  public function testDoesntUseTranslationsArraysAsLabels()
  {
    $former = $this->former->text('pagination')->__toString();
    $matcher = $this->matchField(array(), 'text', 'pagination');

    $this->assertHTML($matcher, $former);
  }
  
  public function testTranslateFieldNameUnderScoreToSpace()
  {
  	$input = $this->former->text('field_name_with_underscore')->__toString();
    $matcher = $this->matchLabel('Field name with underscore', 'field_name_with_underscore');

    $this->assertHTML($matcher, $input);

  }
}
