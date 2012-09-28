<?php
use \Former\Former;

class ValidationTest extends FormerTests
{
  private function field($attributes = array(), $type = 'text', $name = 'foo')
  {
    return '<input' .\HTML::attributes($attributes). ' type="' .$type. '" name="' .$name. '" id="' .$name. '">';
  }

  public function testMultipleRulesArray()
  {
    Former::withRules(array('foo' => 'required'), array('bar' => 'email'));

    // First field
    $input = Former::text('foo')->__toString();
    $matcher = $this->cgr(
      $this->field(array('required' => 'true')),
      '<label for="foo" class="control-label">Foo</label>'
    );

    // Second field
    $email = Former::text('bar')->__toString();
    $emailMatcher = $this->cg(
      $this->field(null, 'email', 'bar'),
      '<label for="bar" class="control-label">Bar</label>');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($emailMatcher, $email);
  }

  public function testRequired()
  {
    Former::withRules(array('foo' => 'required'));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cgr(
      $this->field(array('required' => 'true')),
      '<label for="foo" class="control-label">Foo</label>'
    );

    $this->assertEquals($matcher, $input);
  }

  public function testEmail()
  {
    Former::withRules(array('foo' => 'email'));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cg($this->field(null, 'email'));

    $this->assertEquals($matcher, $input);
  }

  public function testMax()
  {
    Former::withRules(array('foo' => 'max:42'));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cg($this->field(array('maxlength' => '42')));

    $this->assertEquals($matcher, $input);
  }

  public function testMaxNumber()
  {
    Former::withRules(array('foo' => 'max:42'));

    $input = Former::number('foo')->__toString();
    $matcher = $this->cg($this->field(array('max' => '42'), 'number'));

    $this->assertEquals($matcher, $input);
  }

  public function testMin()
  {
    Former::withRules(array('foo' => 'min:42'));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cg($this->field(array('minlength' => '42')));

    $this->assertEquals($matcher, $input);
  }

  public function testMinNumber()
  {
    Former::withRules(array('foo' => 'min:42'));

    $input = Former::number('foo')->__toString();
    $matcher = $this->cg($this->field(array('min' => '42'), 'number'));

    $this->assertEquals($matcher, $input);
  }

  public function testNumeric()
  {
    Former::withRules(array('foo' => 'numeric'));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cg($this->field(array('pattern' => '\d+')));

    $this->assertEquals($matcher, $input);
  }

  public function testBetween()
  {
    Former::withRules(array('foo' => 'between:1,10'));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cg($this->field(array('minlength' => '1', 'maxlength' => '10')));

    $this->assertEquals($matcher, $input);
  }

  public function testBetweenNumber()
  {
    Former::withRules(array('foo' => 'between:1,10'));

    $input = Former::number('foo')->__toString();
    $matcher = $this->cg($this->field(array('min' => '1', 'max' => '10'), 'number'));

    $this->assertEquals($matcher, $input);
  }

  public function testIn()
  {
    Former::withRules(array('foo' => 'in:foo'));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cg($this->field(array('pattern' => '^foo$')));

    $this->assertEquals($matcher, $input);
  }

  public function testInMultiple()
  {
    Former::withRules(array('foo' => 'in:foo,bar'));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cg($this->field(array('pattern' => '^(foo|bar)$')));

    $this->assertEquals($matcher, $input);
  }

  public function testMatch()
  {
    Former::withRules(array('foo' => 'match:/[a-z]+/'));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cg($this->field(array('pattern' => '[a-z]+')));

    $this->assertEquals($matcher, $input);
  }

  public function testDisablingValidation()
  {
    Former::config('live_validation', false);
    Former::withRules(array('foo' => 'required'));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cg(
      $this->field(),
      '<label for="foo" class="control-label">Foo</label>'
    );

    $this->assertEquals($matcher, $input);
  }
}
