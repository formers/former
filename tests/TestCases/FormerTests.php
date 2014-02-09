<?php
ini_set('memory_limit', '400M');
date_default_timezone_set('UTC');

/**
 * Base testing class
 */
abstract class FormerTests extends ContainerTestCase
{
  /**
   * Setup the app for testing
   */
  public function setUp()
  {
    parent::setUp();

    // Reset some parameters
    $this->resetLabels();
    $this->former->framework('TwitterBootstrap');
    $this->former->horizontal_open()->__toString();
  }

  /**
   * Tear down the tests
   *
   * @return void
   */
  public function tearDown()
  {
    $this->former->closeGroup();
    $this->former->close();
    Mockery::close();
  }

  /**
   * Reset registered labels
   *
   * @return void
   */
  public function resetLabels()
  {
    $this->former->labels = array();
    $this->former->ids    = array();
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// DUMMIES /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  protected $checkables = array(
    'Foo' => array(
      'data-foo' => 'bar',
      'value'    => 'bar',
      'name'     => 'foo',
    ),
    'Bar' => array(
      'data-foo' => 'bar',
      'value'    => 'bar',
      'name'     => 'foo',
      'id'       => 'bar',
    ),
  );

  protected $testAttributes = array(
    'class'    => 'foo',
    'data-foo' => 'bar',
  );

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// MATCHERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Match a field
   *
   * @param  array  $attributes
   * @param  string $type
   * @param  string $name
   *
   * @return array
   */
  protected function matchField($attributes = array(), $type = 'text', $name = 'foo')
  {
    $attributes = array_merge($attributes, array('type' => $type, 'name' => $name));
    if ($type == 'hidden') {
      return array('tag' => 'input', 'attributes' => $attributes);
    }

    return array(
      'tag'        => 'input',
      'id'         => $name,
      'attributes' => $attributes,
    );
  }

  /**
   * Match a label
   *
   * @param  string  $name
   * @param  string  $field
   * @param  boolean $required
   *
   * @return array
   */
  protected function matchLabel($name = 'foo', $field = 'foo', $required = false)
  {
    $text = str_replace('[]', null, $name);
    if ($required) {
      $text .= '*';
    }

    return array(
      'tag'     => 'label',
      'content' => $text,
      'attributes' => array(
        'for'   => $field,
        'class' => 'control-label',
      ),
    );
  }

  /**
   * Match a control group
   *
   * @return array
   */
  protected function matchControlGroup()
  {
    return array(
      'tag' => 'div',
      'attributes' => array(
        'class' => 'control-group',
      ),
      'child' => array(
        'tag' => 'div',
        'attributes' => array('class' => 'controls'),
      ),
    );
  }

  /**
   * Match a button
   *
   * @param  string $class
   * @param  string $text
   * @param  array  $attributes
   *
   * @return array
   */
  protected function matchButton($class, $text, $attributes = array())
  {
    $matcher = array(
      'tag'        => 'button',
      'content'    => $text,
      'attributes' => array(
        'class' => $class,
      ),
    );

    // Supplementary attributes
    if ($attributes) {
      $matcher['attributes'] = array_merge($matcher['attributes'], $attributes);
    }

    return $matcher;
  }

  /**
   * Match an input-type button
   *
   * @param  string $class
   * @param  string $text
   * @param  string $type
   *
   * @return array
   */
  protected function matchInputButton($class, $text, $type = 'submit')
  {
    return array(
      'tag'        => 'input',
      'attributes' => array(
        'type'  => $type,
        'value' => $text,
        'class' => $class,
      ),
    );
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Build a list of HTML attributes from an array
   *
   * @param  array  $attributes
   * @return string
   */
  public function attributes($attributes)
  {
    $html = array();

    foreach ((array) $attributes as $key => $value) {
      // For numeric keys, we will assume that the key and the value are the
      // same, as this will convert HTML attributes such as "required" that
      // may be specified as required="required", etc.
      if (is_numeric($key)) {
        $key = $value;
      }

      if (!is_null($value)) {
        $html[] = $key.'="'.$value.'"';
      }
    }

    return (count($html) > 0) ? ' '.implode(' ', $html) : '';
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// ASSERTIONS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Asserts that the input is a control group
   *
   * @param  string $input
   *
   * @return boolean
   */
  protected function assertControlGroup($input)
  {
    $this->assertLabel($input);
    $this->assertHTML($this->matchControlGroup(), $input);
  }

  /**
   * Asserts that the input is a label
   *
   * @param  string $input
   *
   * @return boolean
   */
  protected function assertLabel($input, $name = 'foo', $required = false)
  {
    $this->assertHTML($this->matchLabel(ucfirst($name), $name, $required), $input);
  }

  /**
   * Matches a Control Group
   *
   * @param  string $input
   * @param  string $label
   *
   * @return boolean
   */
  protected function controlGroup($input = '<input type="text" name="foo" id="foo">', $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  /**
   * Matches a Form Group
   *
   * @param  string $input
   * @param  string $label
   *
   * @return boolean
   */
  protected function formGroup($input = '<input type="text" name="foo" id="foo">', $label = '<label for="foo" class="control-label col-lg-2 col-sm-4">Foo</label>')
  {
    return '<div class="form-group">'.$label.'<div class="col-lg-10 col-sm-8">'.$input.'</div></div>';
  }

  /**
   * Matches a required Control Group
   *
   * @param  string $input
   * @param  string $label
   *
   * @return boolean
   */
  protected function controlGroupRequired($input, $label = '<label for="foo" class="control-label">Foo</label>')
  {
    return '<div class="control-group required">'.$label.'<div class="controls">'.$input.'</div></div>';
  }

  /**
   * Assert that a piece of HTML matches an array
   *
   * @param  array  $matcher
   * @param  string $input
   *
   * @return boolean
   */
  public function assertHTML($matcher, $input)
  {
    $this->assertTag(
      $matcher,
      $input,
      "Failed asserting that the HTML matches the provided format :\n\t"
        .$input."\n\t"
        .json_encode($matcher));
  }
}
