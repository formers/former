<?php
class HelpersTest extends FormerTests
{
  public function testDoesntUseTranslationsArraysAsLabels()
  {
    $former  = $this->former->text('pagination')->__toString();
    $matcher = $this->matchField(array(), 'text', 'pagination');

    $this->assertHTML($matcher, $former);
  }

  public function testTranslateFieldNameUnderScoreToSpace()
  {
    $input   = $this->former->text('field_name_with_underscore')->__toString();
    $matcher = $this->matchLabel('Field name with underscore', 'field_name_with_underscore');

    $this->assertHTML($matcher, $input);
  }

  public function testCanDisableTranslationCapitalization()
  {
    $this->app['config'] = Mockery::mock('Config')
      ->shouldReceive('get')->with('former::live_validation', '')->andReturn(true)
      ->shouldReceive('get')->with('former::translate_from', '')->andReturn(true)
      ->shouldReceive('get')->with('former::automatic_label', '')->andReturn(true)
      ->shouldReceive('get')->with('former::capitalize_translations', '')->andReturn(false)
      ->mock();
    Former\Helpers::setApp($this->app);

    $this->assertEquals('field', Former\Helpers::translate('field'));
  }
}
