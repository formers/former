<?php
use HtmlObject\Element;
use HtmlObject\Image;
use HtmlObject\Link;
use HtmlObject\Traits\Tag;

class HtmlObjectTests extends PHPUnit_Framework_TestCase
{

  /**
   * Reset some attributes on each test
   */
  public function startUp()
  {
    Tag::$config['doctype'] = 'html';
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// MATCHERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Create a basic matcher for a tag
   *
   * @param  string $tag
   * @param  string $content
   * @param  array  $attributes
   *
   * @return array
   */
  protected function getMatcher($tag = 'p', $content = 'foo', $attributes = array())
  {
    $tag = array('tag' => $tag);
    if ($content) $tag['content'] = $content;
    if (!empty($attributes)) $tag['attributes'] = $attributes;

    return $tag;
  }

  /**
   * Create a matcher for an input field
   *
   * @param  string $type
   * @param  string $name
   * @param  string $value
   * @param  array  $attributes
   *
   * @return array
   */
  protected function getInputMatcher($type, $name, $value = null, $attributes = array())
  {
    $input = $this->getMatcher('input', null, array(
      'name'  => $name,
      'value' => $value,
      'type'  => $type,
    ));

    return $input;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// HELPERS //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Enhanced version of assertTag
   *
   * @param array  $matcher The tag matcher
   * @param string $html    The HTML
   */
  protected function assertHTML($matcher, $html)
  {
    return $this->assertTag(
      $matcher,
      $html,
      "Failed asserting that the HTML matches the provided format :\n\t"
        .$html."\n\t"
        .json_encode($matcher));
  }

}
