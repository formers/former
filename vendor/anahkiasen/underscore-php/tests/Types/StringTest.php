<?php
use Underscore\Types\String;
use Underscore\Underscore;

class StringTest extends UnderscoreWrapper
{
  public $remove = 'foo foo bar foo kal ter son';

  public function provideAccord()
  {
    return array(
      array(10, '10 things'),
      array(1,  'one thing'),
      array(0,  'nothing'),
    );
  }

  public function provideFind()
  {
    return array(

      // Simple cases
      array(false, 'foo', 'bar'),
      array(true, 'foo', 'foo'),
      array(true, 'FOO', 'foo', false),
      array(false, 'FOO', 'foo', true),

      // Many needles, one haystack
      array(true, array('foo', 'bar'), $this->remove),
      array(false, array('vlu', 'bla'), $this->remove),
      array(true, array('foo', 'vlu'), $this->remove, false, false),
      array(false, array('foo', 'vlu'), $this->remove, false, true),

      // Many haystacks, one needle
      array(true, 'foo', array('foo', 'bar')),
      array(true, 'bar', array('foo', 'bar')),
      array(false, 'foo', array('bar', 'kal')),
      array(true, 'foo', array('foo', 'foo'), false, false),
      array(false, 'foo', array('foo', 'bar'), false, true),
    );
  }

  // Tests --------------------------------------------------------- /

  public function testCanCreateString()
  {
    $string = String::create();

    $this->assertEquals('', $string->obtain());
  }

  public function testHasAccessToStrMethods()
  {
    $string1 = String::limit('foo', 1);
    $string2 = Underscore::from('foo')->limit(1)->obtain();

    $this->assertEquals('f...', $string1);
    $this->assertEquals('f...', $string2);
  }

  public function testCanRemoveTextFromString()
  {
    $return = String::remove($this->remove, 'bar');

    $this->assertEquals('foo foo  foo kal ter son', $return);
  }

  public function testCanRemoveMultipleTextsFromString()
  {
    $return = String::remove($this->remove, array('foo', 'son'));

    $this->assertEquals('bar  kal ter', $return);
  }

  public function testCanToggleBetweenTwoStrings()
  {
    $toggle = String::toggle('foo', 'foo', 'bar');
    $this->assertEquals('bar', $toggle);
  }

  public function testCannotLooselyToggleBetweenStrings()
  {
    $toggle = String::toggle('dei', 'foo', 'bar');
    $this->assertEquals('dei', $toggle);
  }

  public function testCanLooselyToggleBetweenStrings()
  {
    $toggle = String::toggle('dei', 'foo', 'bar', true);
    $this->assertEquals('foo', $toggle);
  }

  public function testCanRepeatString()
  {
    $string = String::from('foo')->repeat(3)->obtain();

    $this->assertEquals('foofoofoo', $string);
  }

  /**
   * @dataProvider provideFind
   */
  public function testCanFindStringsInStrings($expect, $needle, $haystack, $caseSensitive = false, $absoluteFinding = false)
  {
    $result = String::find($haystack, $needle, $caseSensitive, $absoluteFinding);

    $this->assertEquals($expect, $result);
  }

  public function testCanAssertAStringStartsWith()
  {
    $this->assertTrue(String::startsWith('foobar', 'foo'));
    $this->assertFalse(String::startsWith('barfoo', 'foo'));
  }

  public function testCanAssertAStringEndsWith()
  {
    $this->assertTrue(String::endsWith('foobar', 'bar'));
    $this->assertFalse(String::endsWith('barfoo', 'bar'));
  }

  public function testStringsCanBeSlugged()
  {
    $this->assertEquals('my-new-post', String::slugify('My_nEw\\\/  @ post!!!'));
    $this->assertEquals('my_new_post', String::slugify('My nEw post!!!', '_'));
  }

  public function testRandomStringsCanBeGenerated()
  {
    $this->assertEquals(40, strlen(String::random(40)));
  }

  /**
   * @dataProvider provideAccord
   */
  public function testCanAccordAStringToItsNumeral($number, $expect)
  {
    $result = String::accord($number, '%d things', 'one thing', 'nothing');

    $this->assertEquals($expect, $result);
  }

  public function testCanSliceFromAString()
  {
    $string = String::sliceFrom('abcdef', 'c');

    return $this->assertEquals('cdef', $string);
  }

  public function testCanSliceToAString()
  {
    $string = String::sliceTo('abcdef', 'c');

    return $this->assertEquals('ab', $string);
  }

  public function testCanSliceAString()
  {
    $string = String::slice('abcdef', 'c');

    return $this->assertEquals(array('ab', 'cdef'), $string);
  }

  public function testCanUseCorrectOrderForStrReplace()
  {
    $string = String::replace('foo', 'foo', 'bar');

    $this->assertEquals('bar', $string);
  }

  public function testCanExplodeString()
  {
    $string = String::explode('foo bar foo', ' ');
    $this->assertEquals(array('foo', 'bar', 'foo'), $string);

    $string = String::explode('foo bar foo', ' ' , -1);
    $this->assertEquals(array('foo', 'bar'), $string);
  }

  public function testCanGenerateRandomWords()
  {
    $string = String::randomStrings($words = 5, $size = 5);

    $result = ($words * $size) + ($words * 1) - 1;
    $this->assertEquals($result, strlen($string));
  }

  public function testCanConvertToSnakeCase()
  {
    $string = String::toSnakeCase('thisIsAString');

    $this->assertEquals('this_is_a_string', $string);
  }

  public function testCanConvertToCamelCase()
  {
    $string = String::toCamelCase('this_is_a_string');

    $this->assertEquals('thisIsAString', $string);
  }

  public function testCanConvertToPascalCase()
  {
    $string = String::toPascalCase('this_is_a_string');

    $this->assertEquals('ThisIsAString', $string);
  }

  public function testCanGetStringLength()
  {
    $this->assertEquals(6, String::length('Taylor'));
    $this->assertEquals(15, String::length('ラドクリフ'));
  }

  public function testCanConvertToLowercase()
  {
    $this->assertEquals('taylor', String::lower('TAYLOR'));
    $this->assertEquals('άχιστη', String::lower('ΆΧΙΣΤΗ'));
  }

  public function testCanConvertToUppercase()
  {
    $this->assertEquals('TAYLOR', String::upper('taylor'));
    $this->assertEquals('ΆΧΙΣΤΗ', String::upper('άχιστη'));
  }

  public function testCanConvertToTitleCase()
  {
    $this->assertEquals('Taylor', String::title('taylor'));
    $this->assertEquals('Άχιστη', String::title('άχιστη'));
  }

  public function testCanLimitStringsByCharacters()
  {
    $this->assertEquals('Tay...', String::limit('Taylor', 3));
    $this->assertEquals('Taylor', String::limit('Taylor', 6));
    $this->assertEquals('Tay___', String::limit('Taylor', 3, '___'));
  }

  public function testCanLimitByWords()
  {
    $this->assertEquals('Taylor...', String::words('Taylor Otwell', 1));
    $this->assertEquals('Taylor___', String::words('Taylor Otwell', 1, '___'));
    $this->assertEquals('Taylor Otwell', String::words('Taylor Otwell', 3));
  }

  public function testCanCheckIfIsIp()
  {
    $this->assertTrue(String::isIp('192.168.1.1'));
    $this->assertFalse(String::isIp('foobar'));
  }

  public function testCanCheckIfIsEmail()
  {
    $this->assertTrue(String::isEmail('foo@bar.com'));
    $this->assertFalse(String::isEmail('foobar'));
  }

  public function testCanCheckIfIsUrl()
  {
    $this->assertTrue(String::isUrl('http://www.foo.com/'));
    $this->assertFalse(String::isUrl('foobar'));
  }

  public function testCanPrependString()
  {
    $this->assertEquals('foobar', String::prepend('bar', 'foo'));
  }

  public function testCanAppendString()
  {
    $this->assertEquals('foobar', String::append('foo', 'bar'));
  }

  public function testCanGetBaseClass()
  {
    $this->assertEquals('Baz', String::baseClass('Foo\Bar\Baz'));
  }
}
