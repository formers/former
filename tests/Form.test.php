<?php
use \Former\Former;

class FormTest extends FormerTests
{
  // Helpers ------------------------------------------------------- /

  public function createMatcher($class = 'horizontal', $forFiles = false, $action = '#')
  {
    $forFiles = $forFiles ? 'enctype="multipart/form-data" ' : null;
    if(in_array($class, array('horizontal', 'inline', 'vertical', 'search'))) $class = 'form-'.$class;

    return '<form ' .$forFiles. 'class="' .$class. '" method="POST" action="' .$action. '" accept-charset="UTF-8">';
  }

  // Tests --------------------------------------------------------- /

  // Basic tests

  public function testLabel()
  {
    $label = Former::label('foo');

    $this->assertEquals('<label for="">Foo</label>', $label);
  }

  public function testClose()
  {
    $close = Former::close();

    $this->assertEquals('</form>', $close);
  }

  public function testOpen()
  {
    $open = Former::open('#')->__toString();
    $matcher = $this->createMatcher();

    $this->assertEquals($matcher, $open);
  }

  public function testOpenCustom()
  {
    $open = Former::open('#', 'GET', $this->testAttributes)->__toString();
    $matcher = '<form class="foo form-horizontal" data-foo="bar" method="GET" action="#" accept-charset="UTF-8">';

    $this->assertEquals($matcher, $open);
  }

  // __callStatic tests

  public function testHorizontalOpen()
  {
    $open = Former::horizontal_open('#')->__toString();
    $matcher = $this->createMatcher();

    $this->assertEquals($matcher, $open);
  }

  public function testVerticalOpen()
  {
    $open = Former::vertical_open('#')->__toString();
    $matcher = $this->createMatcher('vertical');

    $this->assertEquals($matcher, $open);
  }

  public function testSearchOpen()
  {
    $open = Former::search_open('#')->__toString();
    $matcher = $this->createMatcher('search');

    $this->assertEquals($matcher, $open);
  }

  public function testInlineOpen()
  {
    $open = Former::inline_open('#')->__toString();
    $matcher = $this->createMatcher('inline');

    $this->assertEquals($matcher, $open);
  }

  public function testFilesOpen()
  {
    $open = Former::open_for_files('#')->__toString();
    $matcher = $this->createMatcher('horizontal', true);

    $this->assertEquals($matcher, $open);
  }

  // Combining features

  public function testInlineFilesOpen()
  {
    $open = Former::inline_open_for_files('#')->__toString();
    $matcher = $this->createMatcher('inline', true);

    $this->assertEquals($matcher, $open);
  }

  public function testSecureInlineFilesOpen()
  {
    $open = Former::inline_secure_open_for_files('#')->__toString();
    $matcher = $this->createMatcher('inline', true);

    $this->assertEquals($matcher, $open);
  }

  public function testChainedMethods()
  {
    $open1 = Former::open('test')->secure()->addClass('foo')->method('GET')->__toString();
    $open2 = Former::horizontal_open('#')->class('form-vertical bar')->__toString();
    $matcher1 = '<form class="form-horizontal foo" method="GET" action="https://test/en/test" accept-charset="UTF-8">';
    $matcher2 = $this->createMatcher('form-vertical bar');

    $this->assertEquals($matcher1, $open1);
    $this->assertEquals($matcher2, $open2);
  }

  public function testCanChainRulesToForm()
  {
    $open = Former::open('#')->rules(array())->addClass('foo')->__toString();
    $open .= Former::text('foo')->__toString();
    $matcher = $this->createMatcher('form-horizontal foo').$this->cg();

    $this->assertEquals($matcher, $open);
  }

  public function testChainedFormParameters()
  {
    $open = Former::open()->method('GET')->id('form')->action('#')->addClass('foo')->__toString();
    $matcher = '<form class="form-horizontal foo" id="form" method="GET" action="#" accept-charset="UTF-8">';

    $this->assertEquals($matcher, $open);
  }

  public function testSingleAction()
  {
    $action = Former::actions('<button>Submit</button>');
    $matcher = '<div class="form-actions"><button>Submit</button></div>';

    $this->assertEquals($matcher, $action);
  }

  public function testMultipleStringActions()
  {
    $actions = Former::actions('<button>Submit</button>', '<button type="reset">Reset</button>');
    $matcher = '<div class="form-actions"><button>Submit</button> <button type="reset">Reset</button></div>';

    $this->assertEquals($matcher, $actions);
  }

  public function testMultipleObjectActions()
  {
    $actions = Former::actions(Former::submit('submit'), Former::reset('reset'));
    $matcher =
      '<div class="form-actions">'.
        '<input class="btn" type="submit" value="Submit"> '.
        '<input class="btn" type="reset" value="Reset">'.
      '</div>';

    $this->assertEquals($matcher, $actions);
  }
}
