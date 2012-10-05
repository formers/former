<?php
use \Former\Former;

class FormTest extends FormerTests
{
  // Helpers ------------------------------------------------------- /

  public function createMatcher($type = 'horizontal', $forFiles = false, $action = '#')
  {
    $forFiles = $forFiles ? 'enctype="multipart/form-data" ' : null;

    return '<form ' .$forFiles. 'class="form-' .$type. '" method="POST" action="' .$action. '" accept-charset="UTF-8">';
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
    $open = Former::open('#');
    $matcher = $this->createMatcher();

    $this->assertEquals($matcher, $open);
  }

  public function testOpenCustom()
  {
    $open = Former::open('#', 'GET', $this->testAttributes);
    $matcher = '<form class="foo form-horizontal" data-foo="bar" method="GET" action="#" accept-charset="UTF-8">';

    $this->assertEquals($matcher, $open);
  }

  // __callStatic tests

  public function testHorizontalOpen()
  {
    $open = Former::horizontal_open('#');
    $matcher = $this->createMatcher();

    $this->assertEquals($matcher, $open);
  }

  public function testVerticalOpen()
  {
    $open = Former::vertical_open('#');
    $matcher = $this->createMatcher('vertical');

    $this->assertEquals($matcher, $open);
  }

  public function testSearchOpen()
  {
    $open = Former::search_open('#');
    $matcher = $this->createMatcher('search');

    $this->assertEquals($matcher, $open);
  }

  public function testInlineOpen()
  {
    $open = Former::inline_open('#');
    $matcher = $this->createMatcher('inline');

    $this->assertEquals($matcher, $open);
  }

  public function testFilesOpen()
  {
    $open = Former::open_for_files('#');
    $matcher = $this->createMatcher('horizontal', true);

    $this->assertEquals($matcher, $open);
  }

  // Combining features

  public function testInlineFilesOpen()
  {
    $open = Former::inline_open_for_files('#');
    $matcher = $this->createMatcher('inline', true);

    $this->assertEquals($matcher, $open);
  }

  public function testSecureInlineFilesOpen()
  {
    $open = Former::inline_secure_open_for_files('#');
    $matcher = $this->createMatcher('inline', true);

    $this->assertEquals($matcher, $open);
  }
}
