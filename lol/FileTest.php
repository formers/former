<?php
use \Former\Former;

class FileTest extends FormerTests
{
  public function testFile()
  {
    $file = $this->app['former']->file('foo')->__toString();
    $matcher = $this->cg('<input type="file" name="foo" id="foo">');

    $this->assertEquals($matcher, $file);
  }

  public function testFiles()
  {
    $file = $this->app['former']->files('foo')->__toString();
    $matcher = $this->cg(
      '<input multiple="true" type="file" name="foo[]" id="foo[]">',
      '<label for="foo[]" class="control-label">Foo</label>');

    $this->assertEquals($matcher, $file);
  }

  public function testAccept()
  {
    $file = $this->app['former']->file('foo')->accept('video', 'image', 'audio', 'jpeg', 'image/gif')->__toString();
    $matcher = $this->cg('<input accept="video/*|image/*|audio/*|image/jpeg|image/gif" type="file" name="foo" id="foo">');

    $this->assertEquals($matcher, $file);
  }

  public function testMaxSizeBytes()
  {
    $file = $this->app['former']->file('foo')->max(1, 'KB')->__toString();
    $matcher = $this->cg('<input type="file" name="foo" id="foo"><input type="hidden" name="MAX_FILE_SIZE" value="1024">');

    $this->assertEquals($matcher, $file);
  }

  public function testMaxSizeMegatoSingle()
  {
    $file = $this->app['former']->file('foo')->max(2, 'MB')->__toString();
    $matcher = $this->cg('<input type="file" name="foo" id="foo"><input type="hidden" name="MAX_FILE_SIZE" value="2097152">');

    $this->assertEquals($matcher, $file);
  }

  public function testMaxSizeBits()
  {
    $file = $this->app['former']->file('foo')->max(1, 'Mb')->__toString();
    $matcher = $this->cg('<input type="file" name="foo" id="foo"><input type="hidden" name="MAX_FILE_SIZE" value="131072">');

    $this->assertEquals($matcher, $file);
  }

  public function testMaxSizeOctets()
  {
    $file = $this->app['former']->file('foo')->max(2, 'Mo')->__toString();
    $matcher = $this->cg('<input type="file" name="foo" id="foo"><input type="hidden" name="MAX_FILE_SIZE" value="2097152">');

    $this->assertEquals($matcher, $file);
  }
}
