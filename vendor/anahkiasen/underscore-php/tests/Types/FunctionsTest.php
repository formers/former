<?php
use Underscore\Types\Functions;

class FunctionsTest extends UnderscoreWrapper
{
  public function testCanCallFunctionOnlyOnce()
  {
    $number = 0;
    $function = Functions::once(function() use (&$number) {
      $number++;
    });

    $function();
    $function();

    $this->assertEquals(1, $number);
  }

  public function testCanCallFunctionOnlyXTimes()
  {
    $number = 0;
    $function = Functions::only(function() use (&$number) {
      $number++;
    }, 3);

    $function();
    $function();
    $function();
    $function();
    $function();

    $this->assertEquals(3, $number);
  }

  public function testCanCallFunctionAfterXTimes()
  {
    $number = 0;
    $function = Functions::after(function() use (&$number) {
      $number++;
    }, 3);

    $function();
    $function();
    $function();
    $function();
    $function();

    $this->assertEquals(2, $number);
  }

  public function testCanCacheFunctionResults()
  {
    $function = Functions::cache(function($string) {
      return microtime();
    });

    $result = $function('foobar');

    $this->assertEquals($result, $function('foobar'));
    $this->assertNotEquals($result, $function('barfoo'));
  }

  public function testCanThrottleFunctions()
  {
    $number = 0;
    $function = Functions::throttle(function() use (&$number) {
      $number++;
    }, 1);

    $function();
    $function();
    sleep(1);
    $function();

    $this->assertEquals(2, $number);
  }
}