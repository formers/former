<?php
use Underscore\Types\String;

class GroupTest extends FormerTests
{
  public function createButton($text)
  {
    $button = Mockery::mock('Button');
    $button->shouldReceive('__toString')->andReturn('<button type="button" class="btn">' .$text. '</button>');

    return $button;
  }

  // Data providers ------------------------------------------------ /

  public function provideStates()
  {
    return array(
      array('error'),
      array('info'),
      array('success'),
      array('warning'),
      array('foo'),
    );
  }

  // Helpers ------------------------------------------------------- /

  public function createMatcher($state = null, $inlineHelp = null, $blockHelp = null)
  {
    $state = ($state and $state != 'foo') ? ' ' .$state : null;
    if($inlineHelp) $inlineHelp = '<span class="help-inline">' .ucfirst($inlineHelp). '</span>';
    if($blockHelp)  $blockHelp  = '<p class="help-block">' .ucfirst($blockHelp). '</p>';

    return
    '<div class="control-group' .$state. '">' .
      '<label for="foo" class="control-label">Foo</label>' .
      '<div class="controls">' .
        '<input type="text" name="foo" id="foo" />' .
        $inlineHelp .
        $blockHelp .
      '</div>' .
    '</div>';
  }

  public function createPrependAppendMatcher($prepend = array(), $append = array())
  {
    foreach($prepend as $k => $p) if(!String::startsWith($p, '<button')) $prepend[$k] = '<span class="add-on">' .$p. '</span>';
    foreach($append as $k => $a)  if(!String::startsWith($a, '<button'))  $append[$k] = '<span class="add-on">' .$a. '</span>';

    $class = null;
    if($prepend) $class = "input-prepend";
    if($append) $class .= " input-append";

    return
    '<div class="control-group">' .
      '<label for="foo" class="control-label">Foo</label>' .
      '<div class="controls">' .
        '<div class="' .$class. '">'.
          join(null, $prepend).
          '<input type="text" name="foo" id="foo" />' .
          join(null, $append).
          '</div>'.
      '</div>' .
    '</div>';
  }

  // Tests --------------------------------------------------------- /

  public function testOpen()
  {
    $control = $this->former->text('foo')->__toString();
    $matcher = $this->createMatcher();

    $this->assertEquals($matcher, $control);
  }

  public function testCanOpenGroupManually()
  {
    $group = $this->former->group('foo')->__toString();
    $matcher = '<div class="control-group"><label for="foo" class="control-label">foo</label>';

    $this->assertEquals($matcher, $group);
  }

  /**
   * @dataProvider provideStates
   */
  public function testChangeState($state)
  {
    $control = $this->former->text('foo')->state($state)->__toString();
    $matcher = $this->createMatcher($state);

    $this->assertEquals($matcher, $control);
  }

  public function testHelp()
  {
    $control = $this->former->text('foo')->help('foo')->__toString();
    $matcher = $this->createMatcher(null, 'foo');

    $this->assertEquals($matcher, $control);
  }

  public function testInlineHelp()
  {
    $control = $this->former->text('foo')->inlineHelp('foo')->__toString();
    $matcher = $this->createMatcher(null, 'foo');

    $this->assertEquals($matcher, $control);
  }

  public function testEmptyInlineHelp()
  {
    $control = $this->former->text('foo')->inlineHelp(null)->__toString();
    $matcher = $this->createMatcher();

    $this->assertEquals($matcher, $control);
  }

  public function testBlockHelp()
  {
    $control = $this->former->text('foo')->blockHelp('foo')->__toString();
    $matcher = $this->createMatcher(null, null, 'foo');

    $this->assertEquals($matcher, $control);
  }

  public function testEmptyBlockHelp()
  {
    $control = $this->former->text('foo')->blockHelp(null)->__toString();
    $matcher = $this->createMatcher();

    $this->assertEquals($matcher, $control);
  }

  public function testBothHelps()
  {
    $control = $this->former->text('foo')->inlineHelp('foo')->blockHelp('foo')->__toString();
    $matcher = $this->createMatcher(null, 'foo', 'foo');

    $this->assertEquals($matcher, $control);
  }

  public function testPrepend()
  {
    $control = $this->former->text('foo')->prepend('@')->__toString();
    $matcher = $this->createPrependAppendMatcher(array('@'));

    $this->assertEquals($matcher, $control);
  }

  public function testPrependMultiple()
  {
    $control = $this->former->text('foo')->prepend('@', '$')->__toString();
    $matcher = $this->createPrependAppendMatcher(array('@', '$'));

    $this->assertEquals($matcher, $control);
  }

  public function testAppend()
  {
    $control = $this->former->text('foo')->append('@')->__toString();
    $matcher = $this->createPrependAppendMatcher(array(), array('@'));

    $this->assertEquals($matcher, $control);
  }

  public function testPrependAppend()
  {
    $control = $this->former->text('foo')->prepend('@')->append('@')->__toString();
    $matcher = $this->createPrependAppendMatcher(array('@'), array('@'));

    $this->assertEquals($matcher, $control);
  }

  public function testPrependAppendMix()
  {
    $control = $this->former->text('foo')
      ->prepend('@', $this->createButton('foo'))
      ->append('@', $this->createButton('foo'))
      ->__toString();
    $matcher = $this->createPrependAppendMatcher(
      array('@', '<button type="button" class="btn">foo</button>'),
      array('@', '<button type="button" class="btn">foo</button>'));

    $this->assertEquals($matcher, $control);
  }

  public function testPrependButton()
  {
    $control1 = $this->former->text('foo')->prepend($this->createButton('Submit'))->__toString();
    $control2 = $this->former->text('foo')->prepend('<button type="button" class="btn">Submit</button>')->__toString();
    $matcher = $this->createPrependAppendMatcher(array('<button type="button" class="btn">Submit</button>'));

    $this->assertEquals($matcher, $control1);
    $this->assertEquals($matcher, $control2);
  }

  public function testPrependRawIcon()
  {
    $control = $this->former->text('foo')->prepend('<i class="icon-enveloppe"></i>')->__toString();
    $matcher = $this->createPrependAppendMatcher(array('<i class="icon-enveloppe"></i>'));

    $this->assertEquals($matcher, $control);
  }

  public function testPrependIcon()
  {
    $control = $this->former->text('foo')->prependIcon('enveloppe')->__toString();
    $matcher = $this->createPrependAppendMatcher(array('<i class="icon-enveloppe"></i>'));

    $this->assertEquals($matcher, $control);
  }

  public function testAppendWhiteIcon()
  {
    $control = $this->former->text('foo')->appendIcon('white-something')->__toString();
    $matcher = $this->createPrependAppendMatcher(array(), array('<i class="icon-white icon-something"></i>'));

    $this->assertEquals($matcher, $control);
  }

  public function testAllTheThings()
  {
    $control = $this->former->text('foo')
      ->state('error')
      ->inlineHelp('foo')
      ->blockHelp('bar')
      ->prepend('@', '$', $this->createButton('foo'))
      ->append('@', '$', $this->createButton('foo'))
      ->__toString();
    $matcher =
    '<div class="control-group error">'.
      '<label for="foo" class="control-label">Foo</label>'.
      '<div class="controls">'.
        '<div class="input-prepend input-append">'.
            '<span class="add-on">@</span>'.
            '<span class="add-on">$</span>'.
            '<button type="button" class="btn">foo</button>'.
            '<input type="text" name="foo" id="foo" />'.
            '<span class="add-on">@</span>'.
            '<span class="add-on">$</span>'.
            '<button type="button" class="btn">foo</button>'.
          '</div>'.
        '<span class="help-inline">Foo</span>'.
        '<p class="help-block">Bar</p>'.
      '</div>'.
    '</div>';

    $this->assertEquals($matcher, $control);
  }

  public function testCanInlineASingleField()
  {
    $input = $this->former->text('foo')->raw()->__toString();
    $matcher = $this->matchField();
    unset($matcher['id']);

    $this->assertHTML($matcher, $input);
  }

  public function testCanCreateRawGroups()
  {
    $group = $this->former->group()->contents('This be <b>HTML</b> content');
    $matcher = '<div class="control-group"><div class="controls">This be <b>HTML</b> content</div></div>';

    $this->assertEquals($matcher, $group);
  }

  public function testCanCreateRawLabelessGroups()
  {
    $group = $this->former->group('MyField')->contents('This be <b>HTML</b> content');
    $matcher = '<div class="control-group">'.
      '<label for="MyField" class="control-label">MyField</label>'.
      '<div class="controls">This be <b>HTML</b> content</div>'.
    '</div>';

    $this->assertEquals($matcher, $group);
  }

}
