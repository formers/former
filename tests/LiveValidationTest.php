<?php
use \Former\Former;

class ValidationTest extends FormerTests
{

  // Matchers ------------------------------------------------------ /

  private function field($attributes = array(), $type = 'text', $name = 'foo')
  {
    return '<input' .$this->app->app['former.helpers']->attributes($attributes). ' type="' .$type. '" name="' .$name. '" id="' .$name. '">';
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
    $this->former->withRules(array('foo' => 'required'), array('bar' => 'email'));

    // First field
    $input = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroupRequired(
      $this->field(array('required' => 'true')),
      '<label for="foo" class="control-label">Foo<sup>*</sup></label>'
    );

    // Second field
    $email = $this->former->text('bar')->__toString();
    $emailMatcher = $this->controlGroup(
      $this->field(null, 'email', 'bar'),
      '<label for="bar" class="control-label">Bar</label>');

    $this->assertEquals($matcher, $input);
    $this->assertEquals($emailMatcher, $email);
  }

  public function testRequired()
  {
    $this->former->withRules(array('foo' => 'required'));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroupRequired(
      $this->field(array('required' => 'true')),
      '<label for="foo" class="control-label">Foo<sup>*</sup></label>'
    );

    $this->assertEquals($matcher, $input);
  }

  public function testCanAddRequiredTextToPlainFields()
  {
    $this->former->close();
    $this->former->withRules(array('foo' => 'required'));

    $input = $this->former->text('foo')->__toString();
    $matcher = '<label for="foo">Foo<sup>*</sup></label>'.$this->field(array('required' => 'true'));

    $this->assertEquals($matcher, $input);
  }

  public function testMax()
  {
    $this->former->withRules(array('foo' => 'max:42'));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('maxlength' => '42')));

    $this->assertEquals($matcher, $input);
  }

  public function testMaxNumber()
  {
    $this->former->withRules(array('foo' => 'max:42'));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('max' => '42'), 'number'));

    $this->assertEquals($matcher, $input);
  }

  public function testMin()
  {
    $this->former->withRules(array('foo' => 'min:42'));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('minlength' => '42')));

    $this->assertEquals($matcher, $input);
  }

  public function testMinNumber()
  {
    $this->former->withRules(array('foo' => 'min:42'));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('min' => '42'), 'number'));

    $this->assertEquals($matcher, $input);
  }

  /**
   * @dataProvider patterns
   */
  public function testPatterns($type, $pattern)
  {
    $this->former->withRules(array('foo' => $type));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('pattern' => $pattern)));

    $this->assertEquals($matcher, $input);
  }

  public function testNumericWithNumberField()
  {
    $this->former->withRules(array('foo' => 'numeric'));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('step' => 'any'), 'number'));

    $this->assertEquals($matcher, $input);
  }

  public function testBefore()
  {
    $this->former->withRules(array('foo' => 'before:2012-03-03'));

    $input = $this->former->date('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('max' => '2012-03-03'), 'date'));

    $this->assertEquals($matcher, $input);
  }

  public function testAfter()
  {
    $this->former->withRules(array('foo' => 'after:2012-03-03'));

    $input = $this->former->date('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('min' => '2012-03-03'), 'date'));

    $this->assertEquals($matcher, $input);
  }

  public function testMimes()
  {
    $this->former->withRules(array('foo' => 'mimes:jpg,gif'));

    $input = $this->former->file('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('accept' => 'image/jpeg,image/gif'), 'file'));

    $this->assertEquals($matcher, $input);
  }

  public function testImage()
  {
    $this->former->withRules(array('foo' => 'image'));

    $input = $this->former->file('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('accept' => 'image/jpeg,image/png,image/gif,image/bmp'), 'file'));

    $this->assertEquals($matcher, $input);
  }

  /**
   * @dataProvider types
   */
  public function testTypeChangers($type)
  {
    $this->former->withRules(array('foo' => $type));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroup($this->field(null, $type));

    $this->assertEquals($matcher, $input);
  }

  public function testBetween()
  {
    $this->former->withRules(array('foo' => 'between:1,10'));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('minlength' => '1', 'maxlength' => '10')));

    $this->assertEquals($matcher, $input);
  }

  public function testBetweenNumber()
  {
    $this->former->withRules(array('foo' => 'between:1,10'));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->controlGroup($this->field(array('min' => '1', 'max' => '10'), 'number'));

    $this->assertEquals($matcher, $input);
  }

  public function testDisablingValidation()
  {
    $this->app->app['config'] = $this->app->getConfig(false);
    $this->former->withRules(array('foo' => 'required'));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->controlGroup(
      $this->field(),
      '<label for="foo" class="control-label">Foo</label>'
    );

    $this->assertEquals($matcher, $input);
  }

  public function testCanApplyRulesByChaining()
  {
    $text = $this->former->number('foo')->rule('max', 10)->__toString();
    $matcher = $this->controlGroup($this->field(array('max' => 10), 'number'));

    $this->assertEquals($matcher, $text);
  }
}
