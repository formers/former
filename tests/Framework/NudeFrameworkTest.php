<?php
class NudeFrameworkTest extends FormerTests
{

  public function setUp()
  {
    parent::setUp();

    $this->former->framework('Nude');
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

    public function testCanDisplayErrorMessages()
    {
    // Create field
    $this->former->withErrors($this->validator);
    $required = $this->former->text('required')->wrapAndRender();

    // Matcher
    $matcher =
        '<label for="required">Required</label>'.
        '<input id="required" type="text" name="required">'.
        '<span class="help">The required field is required.</span>';

    $this->assertEquals($matcher, $required);
    }

}
