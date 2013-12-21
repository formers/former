<?php
class TextareaTest extends FormerTests
{

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// MATCHERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Matches a textarea
   *
   * @return array
   */
  public function matchTextarea()
  {
    return array(
      'tag' => 'textarea',
      'content' => 'bar',
      'attributes' => array(
        'class'    => 'foo',
        'cols'     => '50',
        'data-foo' => 'bar',
        'id'       => 'foo',
        'name'     => 'foo',
        'rows'     => '10',
      ),
    );
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testCanCreateArrayFields()
  {
    $this->former->populate(array('foo' => array('fr' => 'bar')));
    $textarea = $this->former->textarea('foo[fr]')->__toString();
    $matcher = '<div class="control-group"><label for="foo[fr]" class="control-label">Foo[fr]</label><div class="controls"><textarea id="foo[fr]" name="foo[fr]">bar</textarea></div></div>';

    $this->assertEquals($matcher, $textarea);
  }

  public function testCanCreateTextareas()
  {
    $attributes = $this->matchTextarea();
    $textarea   = $this->former->textarea('foo')->setAttributes($attributes['attributes'])->value('bar')->__toString();
    $matcher    = $this->matchTextarea();

    $this->assertControlGroup($textarea);
    $this->assertHTML($matcher, $textarea);
  }

  public function testTextareaContentIsProperlyEncoded()
  {
    $value = '</textarea><strong>foo</strong>';
    $attributes = $this->matchTextarea();
    $textarea   = $this->former
      ->textarea('foo')
      ->setAttributes($attributes['attributes'])
      ->value($value)
      ->__toString();
    $matcher    = $this->matchTextarea();
    $matcher['content'] = $value;

    $this->assertControlGroup($textarea);
    $this->assertHTML($matcher, $textarea);
  }
}
