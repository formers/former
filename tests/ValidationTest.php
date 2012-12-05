<?php
use \Former\Former;

class ValidationTest extends FormerTests
{

  // Matchers ------------------------------------------------------ /

  private function field($attributes = array(), $type = 'text', $name = 'foo')
  {
    return '<input' .\HTML::attributes($attributes). ' type="' .$type. '" name="' .$name. '" id="' .$name. '">';
  }

  // Data providers ------------------------------------------------ /

  public function patterns()
  {
    foreach(array(
      'alpha'          => '[a-zA-Z]+',
      'alpha_dash'     => '[a-zA-Z0-9_\-]+',
      'alpha_num'      => '[a-zA-Z0-9]+',
      'in:foo'         => '^foo$',
      'in:foo,bar'     => '^(foo|bar)$',
      'integer'        => '\d+',
      'match:/[a-z]+/' => '[a-z]+',
      'not_in:foo,bar' => '(?:(?!^foo$|^bar$).)*',
      'not_numeric'    => '\D+',
      'numeric'        => '[+-]?\d*\.?\d+',
    ) as $type => $pattern) $patterns[] = array($type, $pattern);

    return $patterns;
  }

  public function types()
  {
    return array(
      array('email'),
      array('url'),
    );
  }

  // Tests --------------------------------------------------------- /

  public function testMultipleRulesArray()
  {
    Former::withRules(array('foo' => 'required'), array('bar' => 'email'));

    // First field
    $input = Former::text('foo')->__toString();
    $matcher = $this->cgr(
      $this->field(array('required' => 'true')),
      '<label for="foo" class="control-label">Foo<sup>*</sup></label>'
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
      '<label for="foo" class="control-label">Foo<sup>*</sup></label>'
    );

    $this->assertEquals($matcher, $input);
  }

  public function testCanAddRequiredTextToPlainFields()
  {
    Former::close();
    Former::withRules(array('foo' => 'required'));

    $input = Former::text('foo')->__toString();
    $matcher = '<label for="foo">Foo<sup>*</sup></label><input required="true" type="text" name="foo" id="foo">';

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

  /**
   * @dataProvider patterns
   */
  public function testPatterns($type, $pattern)
  {
    Former::withRules(array('foo' => $type));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cg($this->field(array('pattern' => $pattern)));

    $this->assertEquals($matcher, $input);
  }

  public function testNumericWithNumberField()
  {
    Former::withRules(array('foo' => 'numeric'));

    $input = Former::number('foo')->__toString();
    $matcher = $this->cg($this->field(array('step' => 'any'), 'number'));

    $this->assertEquals($matcher, $input);
  }

  public function testBefore()
  {
    Former::withRules(array('foo' => 'before:2012-03-03'));

    $input = Former::date('foo')->__toString();
    $matcher = $this->cg($this->field(array('max' => '2012-03-03'), 'date'));

    $this->assertEquals($matcher, $input);
  }

  public function testAfter()
  {
    Former::withRules(array('foo' => 'after:2012-03-03'));

    $input = Former::date('foo')->__toString();
    $matcher = $this->cg($this->field(array('min' => '2012-03-03'), 'date'));

    $this->assertEquals($matcher, $input);
  }

  public function testMimes()
  {
    Former::withRules(array('foo' => 'mimes:jpg,gif'));

    $input = Former::file('foo')->__toString();
    $matcher = $this->cg($this->field(array('accept' => 'image/jpeg,image/gif'), 'file'));

    $this->assertEquals($matcher, $input);
  }

  public function testImage()
  {
    Former::withRules(array('foo' => 'image'));

    $input = Former::file('foo')->__toString();
    $matcher = $this->cg($this->field(array('accept' => 'image/jpeg,image/png,image/gif,image/bmp'), 'file'));

    $this->assertEquals($matcher, $input);
  }

  /**
   * @dataProvider types
   */
  public function testTypeChangers($type)
  {
    Former::withRules(array('foo' => $type));

    $input = Former::text('foo')->__toString();
    $matcher = $this->cg($this->field(null, $type));

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
