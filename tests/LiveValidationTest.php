<?php
class LiveValidationTest extends FormerTests
{

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// DATA PROVIDERS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function providePatterns()
  {
    foreach(array(
      'alpha'          => '[a-zA-Z]+',
      'alpha_dash'     => '[a-zA-Z0-9_\-]+',
      'alpha_num'      => '[a-zA-Z0-9]+',
      'in:foo'         => '^foo$',
      'in:foo,bar'     => '^(foo|bar)$',
      'integer'        => '\d+',
      'regex:/[a-z]+/' => '[a-z]+',
      'match:/[a-z]+/' => '[a-z]+',
      'not_in:foo,bar' => '(?:(?!^foo$|^bar$).)*',
      'not_numeric'    => '\D+',
      'numeric'        => '[+-]?\d*\.?\d+',
    ) as $type => $pattern) $patterns[] = array($type, $pattern);

    return $patterns;
  }

  public function provideTypes()
  {
    return array(
      array('email'),
      array('url'),
    );
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testCanSkipUnexistingHtmlEquivalents()
  {
    $this->former->withRules(array('foo' => 'unique:users'));
    $input = $this->former->text('foo')->render();

    $this->assertHTML($this->matchField(), $input);
  }

  public function testCanHaveSpacesInRulesForSomeReason()
  {
    $this->former->withRules(array('foo' => 'required | email'));
    $input = $this->former->text('foo')->render();

    $this->assertHTML($this->matchField(array('required' => true), 'email'), $input);
  }

  public function testCanUseMultipleRulesArray()
  {
    $this->former->withRules(array('foo' => 'required'), array('bar' => 'email'));

    // First field
    $input = $this->former->text('foo')->__toString();

    $this->assertLabel($input, 'foo', true);
    $this->assertHTML($this->matchControlGroup(), $input);
    $this->assertHTML($this->matchField(array('required' => 'true')), $input);

    // Second field
    $email = $this->former->text('bar')->__toString();

    $this->assertLabel($email, 'bar');
    $this->assertHTML($this->matchControlGroup(), $email);
    $this->assertHTML($this->matchField(array(), 'email', 'bar'), $email);
  }

  public function testCanUseMultipleRulesArrayAsArray()
  {
    $this->former->withRules(array('foo' => array('required')), array('bar' => array('email')));

    // First field
    $input = $this->former->text('foo')->__toString();

    $this->assertLabel($input, 'foo', true);
    $this->assertHTML($this->matchControlGroup(), $input);
    $this->assertHTML($this->matchField(array('required' => 'true')), $input);

    // Second field
    $email = $this->former->text('bar')->__toString();

    $this->assertLabel($email, 'bar');
    $this->assertHTML($this->matchControlGroup(), $email);
    $this->assertHTML($this->matchField(array(), 'email', 'bar'), $email);
  }

  public function testCanSetFieldAsRequired()
  {
    $this->former->withRules(array('foo' => 'required'));
    $input = $this->former->text('foo')->__toString();

    $this->assertHTML($this->matchField(array('required' => 'true')), $input);
    $this->assertLabel($input, 'foo', true);
    $this->assertHTML($this->matchControlGroup(), $input);
  }

  public function testCanSetFieldAsRequiredAsArray()
  {
    $this->former->withRules(array('foo' => array('required')));
    $input = $this->former->text('foo')->__toString();

    $this->assertHTML($this->matchField(array('required' => 'true')), $input);
    $this->assertLabel($input, 'foo', true);
    $this->assertHTML($this->matchControlGroup(), $input);
  }

  public function testCanAddRequiredTextToPlainFields()
  {
    $this->former->close();
    $this->former->withRules(array('foo' => 'required'));

    $input = $this->former->text('foo')->__toString();
    $label = $this->matchLabel('Foo', 'foo', true);
    unset($label['attributes']['class']);

    $this->assertHTML($this->matchField(array('required' => 'true')), $input);
    $this->assertHTML($label, $input);
  }

  public function testCanAddRequiredTextToPlainFieldsAsArray()
  {
    $this->former->close();
    $this->former->withRules(array('foo' => array('required')));

    $input = $this->former->text('foo')->__toString();
    $label = $this->matchLabel('Foo', 'foo', true);
    unset($label['attributes']['class']);

    $this->assertHTML($this->matchField(array('required' => 'true')), $input);
    $this->assertHTML($label, $input);
  }

  public function testCanSetMaxToText()
  {
    $this->former->withRules(array('foo' => 'max:42'));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField(array('maxlength' => '42'));

    $this->assertHTML($matcher, $input);
    $this->assertControlGroup($input);
  }

  public function testCanSetMaxToTextAsArray()
  {
    $this->former->withRules(array('foo' => array('max:42')));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField(array('maxlength' => '42'));

    $this->assertHTML($matcher, $input);
    $this->assertControlGroup($input);
  }

  public function testCanSetMaxToNumber()
  {
    $this->former->withRules(array('foo' => 'max:42'));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->matchField(array('max' => '42'), 'number');

    $this->assertHTML($matcher, $input);
    $this->assertControlGroup($input);
  }

  public function testCanSetMaxToNumberAsArray()
  {
    $this->former->withRules(array('foo' => array('max:42')));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->matchField(array('max' => '42'), 'number');

    $this->assertHTML($matcher, $input);
    $this->assertControlGroup($input);
  }

  public function testCanSetMinToText()
  {
    $this->former->withRules(array('foo' => 'min:42'));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField(array('pattern' => '.{42,}'));

    $this->assertHTML($matcher, $input);
    $this->assertControlGroup($input);
  }

  public function testCanSetMinToTextAsArray()
  {
    $this->former->withRules(array('foo' => array('min:42')));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField(array('pattern' => '.{42,}'));

    $this->assertHTML($matcher, $input);
    $this->assertControlGroup($input);
  }

  public function testCanSetMinToNumber()
  {
    $this->former->withRules(array('foo' => 'min:42'));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->matchField(array('min' => '42'), 'number');

    $this->assertHTML($matcher, $input);
    $this->assertControlGroup($input);
  }

  public function testCanSetMinToNumberAsArray()
  {
    $this->former->withRules(array('foo' => array('min:42')));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->matchField(array('min' => '42'), 'number');

    $this->assertHTML($matcher, $input);
    $this->assertControlGroup($input);
  }

  /**
   * @dataProvider providePatterns
   */
  public function testCanApplyPatternsToFields($type, $pattern)
  {
    $this->former->withRules(array('foo' => $type));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField(array('pattern' => $pattern));

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  /**
   * @dataProvider providePatterns
   */
  public function testCanApplyPatternsToFieldsAsArray($type, $pattern)
  {
    $type = explode('|', $type);

    $this->former->withRules(array('foo' => $type));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField(array('pattern' => $pattern));

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanSetNumberFieldAsNumeric()
  {
    $this->former->withRules(array('foo' => 'numeric'));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->matchField(array('step' => 'any'), 'number');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanSetNumberFieldAsNumericAsArray()
  {
    $this->former->withRules(array('foo' => array('numeric')));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->matchField(array('step' => 'any'), 'number');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanSetDateAsBeforeSomething()
  {
    $this->former->withRules(array('foo' => 'before:2012-03-03'));

    $input = $this->former->date('foo')->__toString();
    $matcher = $this->matchField(array('max' => '2012-03-03'), 'date');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanSetDatetimeAsBeforeSomething()
  {
    $this->former->withRules(array('foo' => 'before:2012-03-03 10:00:00'));

    $input = $this->former->datetime('foo')->__toString();
    $matcher = $this->matchField(array('max' => '2012-03-03T10:00:00'), 'datetime');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanSetDateAsBeforeSomethingAsArray()
  {
    $this->former->withRules(array('foo' => array('before:2012-03-03')));

    $input = $this->former->date('foo')->__toString();
    $matcher = $this->matchField(array('max' => '2012-03-03'), 'date');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanSetDateAsAfterSomething()
  {
    $this->former->withRules(array('foo' => 'after:2012-03-03'));

    $input = $this->former->date('foo')->__toString();
    $matcher = $this->matchField(array('min' => '2012-03-03'), 'date');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanSetDateAsAfterSomethingAsArray()
  {
    $this->former->withRules(array('foo' => array('after:2012-03-03')));

    $input = $this->former->date('foo')->__toString();
    $matcher = $this->matchField(array('min' => '2012-03-03'), 'date');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanRestrictMimeTypes()
  {
    $this->former->withRules(array('foo' => 'mimes:jpg,gif'));

    $input = $this->former->file('foo')->__toString();
    $matcher = $this->matchField(array('accept' => 'image/jpeg,image/gif'), 'file');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanRestrictMimeTypesAsArray()
  {
    $this->former->withRules(array('foo' => array('mimes:jpg,gif')));

    $input = $this->former->file('foo')->__toString();
    $matcher = $this->matchField(array('accept' => 'image/jpeg,image/gif'), 'file');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCantUseAcceptOnNonFilesFields()
  {
    $this->former->withRules(array('foo' => array('mimes:jpg,gif')));

    $input = $this->former->text('foo')->render();
    $matcher = $this->matchField();

    $this->assertHTML($matcher, $input);
  }

  public function testCanForceFieldToImage()
  {
    $this->former->withRules(array('foo' => 'image'));

    $input = $this->former->file('foo')->__toString();
    $matcher = $this->matchField(array('accept' => 'image/jpeg,image/png,image/gif,image/bmp'), 'file');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanForceFieldToImageAsArray()
  {
    $this->former->withRules(array('foo' => array('image')));

    $input = $this->former->file('foo')->__toString();
    $matcher = $this->matchField(array('accept' => 'image/jpeg,image/png,image/gif,image/bmp'), 'file');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  /**
   * @dataProvider provideTypes
   */
  public function testCanSwitchTypesAccordingToRules($type)
  {
    $this->former->withRules(array('foo' => $type));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField(array(), $type);

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  /**
   * @dataProvider provideTypes
   */
  public function testCanSwitchTypesAccordingToRulesAsArray($type)
  {
    $type = explode('|', $type);

    $this->former->withRules(array('foo' => $type));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField(array(), $type[0]);

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanSetBoundariesToText()
  {
    $this->former->withRules(array('foo' => 'between:1,10'));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField(array('pattern' => '.{1,10}', 'maxlength' => '10'));

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanSetBoundariesToTextAsArray()
  {
    $this->former->withRules(array('foo' => array('between:1,10')));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField(array('pattern' => '.{1,10}', 'maxlength' => '10'));

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanSetBoundariestoNumber()
  {
    $this->former->withRules(array('foo' => 'between:1,10'));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->matchField(array('min' => '1', 'max' => '10'), 'number');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanSetBoundariestoNumberAsArray()
  {
    $this->former->withRules(array('foo' => array('between:1,10')));

    $input = $this->former->number('foo')->__toString();
    $matcher = $this->matchField(array('min' => '1', 'max' => '10'), 'number');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanDisableLiveValidation()
  {
    // Change config
    $this->mockConfig(array('live_validation' => false));
    $this->former->withRules(array('foo' => 'required'));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField();

    $this->assertHTML($matcher, $input);
    $this->assertControlGroup($input);
  }

  public function testCanDisableLiveValidationAsArray()
  {
    // Change config
    $this->mockConfig(array('live_validation' => false));
    $this->former->withRules(array('foo' => array('required')));

    $input = $this->former->text('foo')->__toString();
    $matcher = $this->matchField();

    $this->assertHTML($matcher, $input);
    $this->assertControlGroup($input);
  }

  public function testCanApplyRulesByChaining()
  {
    $input = $this->former->number('foo')->rule('max', 10)->__toString();
    $matcher = $this->matchField(array('max' => 10), 'number');

    $this->assertControlGroup($input);
    $this->assertHTML($matcher, $input);
  }

  public function testCanCreateMaxFileSizeForFiles()
  {
    $input = $this->former->file('foo')->rule('max', 10)->__toString();
    $this->assertHTML($this->matchField(array('value' => 10240), 'hidden', 'MAX_FILE_SIZE'), $input);
  }
}
