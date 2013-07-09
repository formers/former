<?php
namespace HtmlObject\Traits;

use HtmlObject\Element;
use HtmlObject\Text;

/**
 * An abstract class to create and manage trees of objects
 */
abstract class TreeObject
{

  /**
   * Parent of the object
   *
   * @var TreeObject
   */
  protected $parent;

  /**
   * The name of the child for the parent
   *
   * @var string
   */
  public $parentIndex;

  /**
   * Children of the object
   *
   * @var array
   */
  protected $children = array();

  // Defaults ------------------------------------------------------ /

  /**
   * Default element for nested children
   *
   * @var string
   */
  protected $defaultChild;

  ////////////////////////////////////////////////////////////////////
  /////////////////////////////// PARENT /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the Element's parent
   *
   * @param integer $levels The number of levels to go back up
   *
   * @return Element
   */
  public function getParent($levels = null)
  {
    if (!$levels) return $this->parent;

    $subject = $this;
    for ($i = 0; $i <= $levels; $i++) {
      $subject = $subject->getParent();
    }

    return $subject;
  }

  /**
   * Set the parent of the element
   *
   * @param TreeObject $parent
   *
   * @return TreeObject
   */
  public function setParent(TreeObject $parent)
  {
    $this->parent = $parent;

    return $this;
  }

  /**
   * Check if an object has a parent
   *
   * @return boolean
   */
  public function hasParent()
  {
    return (bool) $this->parent;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// CHILDREN ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  // Get ----------------------------------------------------------- /

  /**
   * Get a specific child of the element
   *
   * @param string $name The Element's name
   *
   * @return Element
   */
  public function getChild($name)
  {
    // Direct fetching
    $name = explode('.', $name);
    if (sizeof($name) == 1) {
      return Helpers::arrayGet($this->getChildren(), $name[0]);
    }

    // Recursive fetching
    $subject = $this;
    foreach ($name as $child) {
      $subject = $subject->getChild($child);
    }

    return $subject;
  }

  /**
   * Check if an Element has a Child
   *
   * @param string $name The child's name
   *
   * @return boolean
   */
  public function hasChild($name)
  {
    return (bool) $this->getChild($name);
  }

  /**
   * Get all children
   *
   * @return array
   */
  public function getChildren()
  {
    return $this->children;
  }

  /**
   * Check if the object has children
   *
   * @return boolean
   */
  public function hasChildren()
  {
    return !is_null($this->children) and !empty($this->children);
  }

  /**
   * Check if a given element is after another sibling
   *
   * @param integer|string $sibling The sibling
   *
   * @return boolean
   */
  public function isAfter($sibling)
  {
    $children = array_keys($this->getParent()->getChildren());
    $child    = array_search($this->parentIndex, $children);
    $sibling  = array_search($sibling, $children);

    return $child > $sibling;
  }

  // Set ----------------------------------------------------------- /

  /**
   * Nests an object withing the current object
   *
   * @param Tag|string $element    An element name or an Tag
   * @param string     $value      The Tag's alias or the element's content
   * @param array      $attributes
   *
   * @return Tag
   */
  public function nest($element, $value = null, $attributes = array())
  {
    // Alias for nestChildren
    if (is_array($element)) {
      return $this->nestChildren($element);
    }

    // Transform the element
    if (!($element instanceof TreeObject)) {
      $element = $this->createTagFromString($element, $value, $attributes);
    }

    // If we seek to nest into a child, get the child and nest
    if($this->hasChild($value)) {
      $element = $this->getChild($value)->nest($element);
    }

    return $this->setChild($element, $value);
  }

  /**
   * Nest an array of objects/values
   *
   * @param array $children
   */
  public function nestChildren($children)
  {
    if (!is_array($children)) return $this;

    foreach ($children as $element => $value) {
      if (is_numeric($element)) {
        if($value instanceof TreeObject) $this->setChild($value);
        elseif($this->defaultChild) $this->nest($this->defaultChild, $value);
      } else {
        if($value instanceof TreeObject) $this->setChild($value, $element);
        else $this->nest($element, $value);
      }
    }

    return $this;
  }

  /**
   * Add an object to the current object
   *
   * @param string|TreeObject  $child The child
   * @param string             $name  Its name
   *
   * @return TreeObject
   */
  public function setChild($child, $name = null)
  {
    if (!$name) $name = sizeof($this->children);

    // Get subject of the setChild
    $subject = explode('.', $name);
    $name    = array_pop($subject);
    $subject = implode('.', $subject);
    $subject = $subject ? $this->getChild($subject) : $this;

    // Bind parent to child
    if ($child instanceof TreeObject) {
      $child->setParent($subject);
    }

    // Add object to children
    $child->parentIndex = $name;
    $subject->children[$name] = $child;

    return $this;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Creates an Element or a TextNode from an element/value combo
   *
   * @param string $element    The element/string
   * @param string $value      The element's content
   * @param array  $attributes
   *
   * @return TreeObject
   */
  protected function createTagFromString($element, $value = null, $attributes = array())
  {
    // If it's an element/value, create the element
    if (strpos($element, '<') === false and !$this->hasChild($value)) {
      return new Element($element, $value, $attributes);
    }

    return new Text($element);
  }

}
