<?php
use Underscore\Parse;

class ParseTest extends UnderscoreWrapper
{
  ////////////////////////////////////////////////////////////////////
  ////////////////////////// DATA PROVIDERS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function provideSwitchers()
  {
    return array(
      array('toArray', NULL, array()),
      array('toArray', 15, array(15)),
      array('toArray', 'foobar', array('foobar')),
      array('toArray', (object) $this->array, $this->array),
      array('toArray', new DummyDefault, array('foo', 'bar')),

      array('toString', 15, '15'),
      array('toString', array('foo', 'bar'), '["foo","bar"]'),

      array('toInteger', 'foo', 3),
      array('toInteger', '', 0),
      array('toInteger', '15', 15),
      array('toInteger', array(1, 2, 3), 3),
      array('toInteger', array(), 0),

      array('toObject', $this->array, (object) $this->array),

      array('toBoolean', '', false),
      array('toBoolean', 'foo', true),
      array('toBoolean', 15, true),
      array('toBoolean', 0, false),
      array('toBoolean', array(), false),
    );
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// TESTS ///////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testCanCreateCsvFiles()
  {
    $csv = Parse::toCSV($this->arrayMulti);
    $matcher = '"bar";"ter"' . PHP_EOL . '"bar";"ter"' .PHP_EOL. '"foo";"ter"';

    $this->assertEquals($matcher, $csv);
  }

  public function testCanUseCustomCsvDelimiter()
  {
    $csv = Parse::toCSV($this->arrayMulti, ',');
    $matcher = '"bar","ter"' . PHP_EOL . '"bar","ter"' .PHP_EOL. '"foo","ter"';

    $this->assertEquals($matcher, $csv);
  }

  public function testCanOutputCsvHeaders()
  {
    $csv = Parse::toCSV($this->arrayMulti, ',', true);
    $matcher = 'foo,bis' . PHP_EOL . '"bar","ter"' . PHP_EOL . '"bar","ter"' .PHP_EOL. '"foo","ter"';

    $this->assertEquals($matcher, $csv);
  }

  public function testCanConvertToJson()
  {
    $json = Parse::toJSON($this->arrayMulti);
    $matcher = '[{"foo":"bar","bis":"ter"},{"foo":"bar","bis":"ter"},{"bar":"foo","bis":"ter"}]';

    $this->assertEquals($matcher, $json);
  }

  public function testCanParseJson()
  {
    $json = Parse::toJSON($this->arrayMulti);
    $array = Parse::fromJSON($json);

    $this->assertEquals($this->arrayMulti, $array);
  }

  public function testCanParseXML()
  {
    $array = Parse::fromXML('<article><name>foo</name><content>bar</content></article>');
    $matcher = array('name' => 'foo', 'content' => 'bar');

    $this->assertEquals($matcher, $array);
  }

  public function testCanParseCSV()
  {
    $array = Parse::fromCSV("foo;bar;bis\nbar\tfoo\tter");
    $results = array(array('foo', 'bar', 'bis'), array('bar', 'foo', 'ter'));

    $this->assertEquals($results, $array);
  }

  public function testCanParseCSVWithHeaders($value='')
  {
    $array = Parse::fromCSV("foo;bar;bis" .PHP_EOL. "bar\tfoo\tter", true);
    $results = array(array('foo' => 'bar', 'bar' => 'foo', 'bis' => 'ter'));

    $this->assertEquals($results, $array);
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////// TYPES SWITCHERS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * @dataProvider provideSwitchers
   */
  public function testCanSwitchTypes($method, $from, $to)
  {
    $from = Parse::$method($from);

    $this->assertEquals($to, $from);
  }
}
