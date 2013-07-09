<?php
use HtmlObject\Lists;

class ListsTest extends HtmlObjectTests
{
  public function testCanCreateList()
  {
    $list = new Lists('ul');

    $this->assertHTML($this->getMatcher('ul', null), $list);
  }

  public function testCanCreateListWithChildren()
  {
    $list = Lists::ul(array(
      'foo', 'bar',
    ));

    $this->assertEquals('<ul><li>foo</li><li>bar</li></ul>', $list->render());
  }

  public function testCanSetCustomElementsOnChildren()
  {
    $list = Lists::ul(array(
      'a' => 'foo', 'bar',
    ));

    $this->assertEquals('<ul><a>foo</a><li>bar</li></ul>', $list->render());
  }
}