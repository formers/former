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
    $label = $this->app['former']->label('foo');

    $this->assertEquals('<label for="">Foo</label>', $label);
  }

  public function testClose()
  {
    $close = $this->app['former']->close();

    $this->assertEquals('</form>', $close);
  }

  public function testOpen()
  {
    $open = $this->app['former']->open('#')->__toString();
    $matcher = $this->createMatcher();

    $this->assertEquals($matcher, $open);
  }

  public function testOpenCustom()
  {
    $open = $this->app['former']->open('#', 'GET', $this->testAttributes)->__toString();
    $matcher = '<form class="foo form-horizontal" data-foo="bar" method="GET" action="#" accept-charset="UTF-8">';

    $this->assertEquals($matcher, $open);
  }

  // __callStatic tests

  public function testHorizontalOpen()
  {
    $open = $this->app['former']->horizontal_open('#')->__toString();
    $matcher = $this->createMatcher();

    $this->assertEquals($matcher, $open);
  }

  public function testVerticalOpen()
  {
    $open = $this->app['former']->vertical_open('#')->__toString();
    $matcher = $this->createMatcher('vertical');

    $this->assertEquals($matcher, $open);
  }

  public function testSearchOpen()
  {
    $open = $this->app['former']->search_open('#')->__toString();
    $matcher = $this->createMatcher('search');

    $this->assertEquals($matcher, $open);
  }

  public function testInlineOpen()
  {
    $open = $this->app['former']->inline_open('#')->__toString();
    $matcher = $this->createMatcher('inline');

    $this->assertEquals($matcher, $open);
  }

  public function testFilesOpen()
  {
    $open = $this->app['former']->open_for_files('#')->__toString();
    $matcher = $this->createMatcher('horizontal', true);

    $this->assertEquals($matcher, $open);
  }

  // Combining features

  public function testInlineFilesOpen()
  {
    $open = $this->app['former']->inline_open_for_files('#')->__toString();
    $matcher = $this->createMatcher('inline', true);

    $this->assertEquals($matcher, $open);
  }

  public function testSecureInlineFilesOpen()
  {
    $open = $this->app['former']->inline_secure_open_for_files('#')->__toString();
    $matcher = $this->createMatcher('inline', true);

    $this->assertEquals($matcher, $open);
  }

  public function testChainedMethods()
  {
    $open1 = $this->app['former']->open('test')->secure()->addClass('foo')->method('GET')->__toString();
    $open2 = $this->app['former']->horizontal_open('#')->class('form-vertical bar')->__toString();
    $matcher1 = '<form class="form-horizontal foo" method="GET" action="https://test/en/test" accept-charset="UTF-8">';
    $matcher2 = $this->createMatcher('form-vertical bar');

    $this->assertEquals($matcher1, $open1);
    $this->assertEquals($matcher2, $open2);
  }

  public function testCanChainRulesToForm()
  {
    $open = $this->app['former']->open('#')->rules(array())->addClass('foo')->__toString();
    $open .= $this->app['former']->text('foo')->__toString();
    $matcher = $this->createMatcher('form-horizontal foo').$this->cg();

    $this->assertEquals($matcher, $open);
  }

  public function testChainedFormParameters()
  {
    $open = $this->app['former']->open()->method('GET')->id('form')->action('#')->addClass('foo')->__toString();
    $matcher = '<form class="form-horizontal foo" id="form" method="GET" action="#" accept-charset="UTF-8">';

    $this->assertEquals($matcher, $open);
  }

  public function testSingleAction()
  {
    $action = $this->app['former']->actions('<button>Submit</button>');
    $matcher = '<div class="form-actions"><button>Submit</button></div>';

    $this->assertEquals($matcher, $action);
  }

  public function testMultipleStringActions()
  {
    $actions = $this->app['former']->actions('<button>Submit</button>', '<button type="reset">Reset</button>');
    $matcher = '<div class="form-actions"><button>Submit</button> <button type="reset">Reset</button></div>';

    $this->assertEquals($matcher, $actions);
  }

  public function testMultipleObjectActions()
  {
    $actions = $this->app['former']->actions($this->app['former']->submit('submit'), $this->app['former']->reset('reset'));
    $matcher =
      '<div class="form-actions">'.
        '<input class="btn" type="submit" value="Submit"> '.
        '<input class="btn" type="reset" value="Reset">'.
      '</div>';

    $this->assertEquals($matcher, $actions);
  }
}
