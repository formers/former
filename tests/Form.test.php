<?php
class FormTest extends FormerTests
{
  public function createMatcher($type = 'horizontal', $forFiles = false)
  {
    $forFiles = $forFiles ? 'enctype="multipart/form-data" ' : null;

    return '<form ' .$forFiles. 'class="form-' .$type. '" method="POST" action="#" accept-charset="UTF-8">';
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
}