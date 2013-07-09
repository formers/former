<?php
use HtmlObject\Element;
use HtmlObject\Input;
use HtmlObject\Traits\Tag;

class Icon extends HtmlObject\Traits\Tag
{
  protected $bar = 'bar';

  public function __construct($icon)
  {
    $this->setTag('i', null, array('class' => 'icon-'.$icon));
  }

  public function injectProperties()
  {
    return array(
      'foo' => $this->bar,
    );
  }
}

class TagTest extends HtmlObjectTests
{
  public function setUp()
  {
    $this->object = new Element('p', 'foo');
  }

  public function testCanCreateCustomElementClasses()
  {
    $icon = new Icon('bookmark');

    $this->assertEquals('<i class="icon-bookmark" foo="bar"></i>', $icon->render());
  }

  public function testCanCreateHtmlObject()
  {
    $this->assertHTML($this->getMatcher(), $this->object);
  }

  public function testCanCreateDefaultElement()
  {
    $this->assertHTML($this->getMatcher(), Element::create()->setValue('foo'));
  }

  public function testCanUseXhtmlStandards()
  {
    Tag::$config['doctype'] = 'xhtml';
    $field = Input::hidden('foo', 'bar');

    $this->assertContains(' />', $field->render());
  }

  public function testCanSetAnAttribute()
  {
    $this->object->setAttribute('data-foo', 'bar');
    $matcher = $this->getMatcher();
    $matcher['attributes']['data-foo'] = 'bar';

    $this->assertHTML($matcher, $this->object);
  }

  public function testCanGetAttributes()
  {
    $this->object->setAttribute('data-foo', 'bar');

    $this->assertEquals(array('data-foo' => 'bar'), $this->object->getAttributes());
  }

  public function testCanDynamicallySetAttributes()
  {
    $this->object->data_foo('bar');
    $this->object->foo = 'bar';

    $matcher = $this->getMatcher();
    $matcher['attributes']['data-foo'] = 'bar';
    $matcher['attributes']['foo'] = 'bar';

    $this->assertHTML($matcher, $this->object);
  }

  public function testCanDynamicallyGetChild()
  {
    $two  = Element::p('foo');
    $one  = Element::div()->setChild($two, 'two');
    $zero = Element::div()->setChild($one, 'one');

    $this->assertEquals('foo', $zero->oneTwo->getValue());
  }

  public function testCanReplaceAttributes()
  {
    $this->object->setAttribute('data-foo', 'bar');
    $this->object->replaceAttributes(array('foo' => 'bar'));

    $matcher = $this->getMatcher();
    $matcher['attributes']['foo'] = 'bar';


    $this->assertHTML($matcher, $this->object);
  }

  public function testCanMergeAttributes()
  {
    $this->object->setAttribute('data-foo', 'bar');
    $this->object->setAttributes(array('foo' => 'bar'));

    $matcher = $this->getMatcher();
    $matcher['attributes']['data-foo'] = 'bar';
    $matcher['attributes']['foo'] = 'bar';

    $this->assertHTML($matcher, $this->object);
  }

  public function testCanAppendClass()
  {
    $this->object->setAttribute('class', 'foo');
    $this->object->addClass('foo');
    $this->object->addClass('bar');

    $matcher = $this->getMatcher();
    $matcher['attributes']['class'] = 'foo bar';

    $this->assertHTML($matcher, $this->object);
  }

  public function testCanFetchAttributes()
  {
    $this->object->foo('bar');

    $this->assertEquals('bar', $this->object->foo);
  }

  public function testCanChangeElement()
  {
    $this->object->setElement('strong');

    $this->assertHTML($this->getMatcher('strong', 'foo'), $this->object);
  }

  public function testCanChangeValue()
  {
    $this->object->setValue('bar');

    $this->assertHTML($this->getMatcher('p', 'bar'), $this->object);
  }

  public function testCanGetValue()
  {
    $this->assertEquals('foo', $this->object->getValue());
  }

  public function testSimilarClassesStillGetAdded()
  {
    $this->object->addClass('alert-success');
    $this->object->addClass('alert');

    $this->assertEquals('<p class="alert-success alert">foo</p>', $this->object->render());
  }

  public function testCanRemoveClasses()
  {
    $this->object->addClass('foo');
    $this->object->addClass('bar');
    $this->object->removeClass('foo');

    $this->assertEquals('<p class="bar">foo</p>', $this->object->render());
  }

  public function testCanManuallyOpenElement()
  {
    $element = $this->object->open().'foobar'.$this->object->close();

    $this->assertEquals('<p>foobar</p>', $element);
  }

  public function testCanWrapValue()
  {
    $this->object->wrapValue('strong');

    $this->assertEquals('<p><strong>foo</strong></p>', $this->object->render());
  }

  public function testCanWrapItself()
  {
    $object = $this->object->wrapWith('div');

    $this->assertEquals('<div><p>foo</p></div>', $object->render());
  }

  public function testCanManuallyOpenComplexStructures()
  {
    $object = Element::div(array(
      'title'  => Element::div('foo')->class('title'),
      'body'   => Element::div()->class('body'),
      'footer' => Element::div('footer'),
    ));
    $object = $object->openOn('body').'CONTENT'.$object->close();

    $this->assertEquals('<div><div class="title">foo</div><div class="body">CONTENT</div><div>footer</div></div>', $object);
  }

  public function testCanManipulateComplexStructures()
  {
    $object = Element::div(array(
      'title' => Element::div('foo')->class('title'),
      'body'  => Element::div()->class('body'),
    ));

    $wrapper = HtmlObject\Link::create('#', '');
    $object = $object->wrapWith($wrapper, 'complex');
    $render = $object->openOn('complex.body').'foo'.$object->close();

    $this->assertEquals('<a href="#"><div><div class="title">foo</div><div class="body">foo</div></div></a>', $render);
  }

  public function testCanCheckIfTagIsOpened()
  {
    $this->object->open();

    $this->assertTrue($this->object->isOpened());
  }

  public function testCanCreateShadowDom()
  {
    $tag = Element::div('foo')->foo('bar')->element('');

    $this->assertEquals('foo', $tag->render());
  }
}