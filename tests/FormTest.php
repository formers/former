<?php
class FormTest extends FormerTests
{
  // Helpers ------------------------------------------------------- /

  public function matchForm($class = 'horizontal', $files = false, $action = '#')
  {
    if(in_array($class, array('horizontal', 'inline', 'vertical', 'search'))) $class = 'form-'.$class;

    $matcher = array(
      'tag' => 'form',
      'attributes' => array(
        'class' => $class,
        'method' => 'POST',
        'accept-charset' => 'utf-8',
        'action' => $action,
      ),
    );

    if ($files) $matcher['attributes']['enctype'] = 'multipart/form-data';
    return $matcher;
  }

  public function createMatcher($class = 'horizontal', $forFiles = false, $action = '#')
  {
    if(in_array($class, array('horizontal', 'inline', 'vertical', 'search'))) $class = 'form-'.$class;
    $forFiles = $forFiles ? 'enctype="multipart/form-data" ' : null;

    return '<form ' .$forFiles. 'class="' .$class. '" method="POST" action="' .$action. '" accept-charset="UTF-8">';
  }

  // Tests --------------------------------------------------------- /

  public function testCanOpenAClassicForm()
  {
    $open = $this->former->open('#')->__toString();
    $matcher = $this->matchForm();

    $this->assertHTML($matcher, $open);
  }

  public function testCanCloseAForm()
  {
    $close = $this->former->close();

    $this->assertEquals('</form>', $close);
  }

  public function testCanCreateCustomFormOpener()
  {
    $open = $this->former->open('#', 'GET', $this->testAttributes)->__toString();
    $matcher = $this->matchForm('foo form-horizontal');
    $matcher['attributes']['method'] = 'GET';
    $matcher['attributes']['data-foo'] = 'bar';

    $this->assertHTML($matcher, $open);
  }

  public function testCanCreateHorizontalForm()
  {
    $open = $this->former->horizontal_open('#')->__toString();
    $matcher = $this->matchForm();

    $this->assertHTML($matcher, $open);
  }

  public function testCanCreateVerticalForm()
  {
    $open = $this->former->vertical_open('#')->__toString();
    $matcher = $this->matchForm('vertical');

    $this->assertHTML($matcher, $open);
  }

  public function testCanCreateSearchForm()
  {
    $open = $this->former->search_open('#')->__toString();
    $matcher = $this->matchForm('search');

    $this->assertHTML($matcher, $open);
  }

  public function testCanCreateInlineForm()
  {
    $open = $this->former->inline_open('#')->__toString();
    $matcher = $this->matchForm('inline');

    $this->assertHTML($matcher, $open);
  }

  public function testCanCreateFilesForm()
  {
    $open = $this->former->open_for_files('#')->__toString();
    $matcher = $this->matchForm('horizontal', true);

    $this->assertHTML($matcher, $open);
  }

  // Combining features

  public function testCanCreateAnInlineFilesForm()
  {
    $open = $this->former->inline_open_for_files('#')->__toString();
    $matcher = $this->matchForm('inline', true);

    $this->assertHTML($matcher, $open);
  }

  public function testCanCreateAnInlineSecureFilesForm()
  {
    $open = $this->former->inline_secure_open_for_files('#')->__toString();
    $matcher = $this->matchForm('inline', true);

    $this->assertHTML($matcher, $open);
  }

  public function testCanChainMethods()
  {
    $open1 = $this->former->open('test')->secure()->addClass('foo')->method('GET')->__toString();
    $open2 = $this->former->horizontal_open('#')->class('form-vertical bar')->__toString();

    $matcher1 = $this->matchForm('form-horizontal foo', false, 'https://test/en/test');
    $matcher1['attributes']['method'] = 'GET';
    $matcher2 = $this->matchForm('form-vertical bar');

    $this->assertHTML($matcher1, $open1);
    $this->assertHTML($matcher2, $open2);
  }

  public function testCanDirectlyAddRulesToAForm()
  {
    // Check form opener
    $open = $this->former->open('#')->rules(array('foo' => 'required'))->addClass('foo')->__toString();
    $matcher = $this->matchForm('form-horizontal foo');
    $this->assertHTML($matcher, $open);

    // Check field
    $input = $this->former->text('foo')->__toString();
    $label = $this->matchLabel('foo', true);

    $this->assertHTML($this->matchField(array('required' => 'true')), $input);
    $this->assertHTML($label, $input);
  }

  public function testCanChainAttributes()
  {
    $open = $this->former->open()->method('GET')->id('form')->action('#')->addClass('foo')->__toString();
    $matcher = $this->matchForm('form-horizontal foo');
    $matcher['id'] = 'form';
    $matcher['attributes']['method'] = 'GET';

    $this->assertHTML($matcher, $open);
  }
}
