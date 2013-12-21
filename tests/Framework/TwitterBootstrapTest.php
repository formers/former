<?php
use Illuminate\Support\Str;

class TwitterBootstrap2Test extends FormerTests
{

  public function setUp()
  {
    parent::setUp();

    $this->former->framework('TwitterBootstrap');
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// MATCHERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function createPrependAppendMatcher($prepend = array(), $append = array())
  {
    foreach($prepend as $k => $p) if(!Str::startsWith($p, '<button')) $prepend[$k] = '<span class="add-on">' .$p. '</span>';
    foreach($append as $k => $a)  if(!Str::startsWith($a, '<button'))  $append[$k] = '<span class="add-on">' .$a. '</span>';

    $class = array();
    if($prepend) $class[] = "input-prepend";
    if($append) $class[] = "input-append";

    return
    '<div class="control-group">' .
      '<label for="foo" class="control-label">Foo</label>' .
      '<div class="controls">' .
        '<div class="' .implode(' ',$class). '">'.
          join(null, $prepend).
          '<input id="foo" type="text" name="foo">' .
          join(null, $append).
          '</div>'.
      '</div>' .
    '</div>';
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testAppendWhiteIcon()
  {
    $control = $this->former->text('foo')->appendIcon('white-something')->__toString();
    $matcher = $this->createPrependAppendMatcher(array(), array('<i class="icon-white  icon-something"></i>'));

    $this->assertEquals($matcher, $control);
  }

}
