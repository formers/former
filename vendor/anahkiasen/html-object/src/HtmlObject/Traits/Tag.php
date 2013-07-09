<?php
namespace HtmlObject\Traits;

use HtmlObject\Element;
use HtmlObject\Text;

/**
 * An abstraction of an HTML element
 */
abstract class Tag extends TreeObject
{

  /**
   * The element name
   *
   * @var string
   */
  protected $element;

  /**
   * The object's value
   *
   * @var string|null|Tag
   */
  protected $value;

  /**
   * The object's attribute
   *
   * @var array
   */
  protected $attributes = array();

  /**
   * Whether the element is self closing
   *
   * @var boolean
   */
  protected $isSelfClosing = false;

  /**
   * Whether the current tag is opened or not
   *
   * @var boolean
   */
  protected $isOpened = false;

  /**
   * A list of class properties to be added to attributes
   *
   * @var array
   */
  protected $injectedProperties = array('value');

  // Configuration options ----------------------------------------- /

  /**
   * The base configuration inherited by classes
   *
   * @var array
   */
  public static $config = array(
    'doctype' => 'html',
  );

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// CORE METHODS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set up a new tag
   *
   * @param string $element    Its element
   * @param string $value      Its value
   * @param array  $attributes Its attributes
   */
  protected function setTag($element, $value = null, $attributes = array())
  {
    $this->setValue($value);
    $this->setElement($element);
    $this->replaceAttributes($attributes);
  }

  /**
   * Wrap the Element in another element
   *
   * @param string|Element $element The element's tag
   *
   * @return Element
   */
  public function wrapWith($element, $name = null)
  {
    if ($element instanceof Tag) {
      return $element->nest($this, $name);
    }

    return Element::create($element)->nest($this, $name);
  }

  /**
   * Render on string conversion
   *
   * @return string
   */
  public function __toString()
  {
    return $this->render();
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////// ELEMENT RENDERING ////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Opens the Tag
   *
   * @return string
   */
  public function open()
  {
    $this->isOpened = true;

    // If self closing, put value as attribute
    foreach ($this->injectProperties() as $attribute => $property) {
      if (!$this->isSelfClosing and $attribute == 'value') continue;
      if (is_null($property) and !is_empty($property))     continue;
      $this->attributes[$attribute] = $property;
    }

    // Invisible tags
    if (!$this->element) {
      return null;
    }

    return '<'.$this->element.Helpers::parseAttributes($this->attributes).$this->getTagCloser();
  }

  /**
   * Open the tag tree on a particular child
   *
   * @param string $onChild The child's key
   *
   * @return string
   */
  public function openOn($onChildren)
  {
    $onChildren = explode('.', $onChildren);
    $element  = $this->open();
    $element .= $this->value;
    $subject  = $this;

    foreach ($onChildren as $onChild) {
     foreach ($subject->getChildren() as $childName => $child) {
        if ($childName != $onChild) $element .= $child;
        else {
          $subject  = $child;
          $element .= $child->open();
          break;
        }
      }
    }

    return $element;
  }

  /**
   * Check if the tag is opened
   *
   * @return boolean
   */
  public function isOpened()
  {
    return $this->isOpened;
  }

  /**
   * Returns the Tag's content
   *
   * @return string
   */
  public function getContent()
  {
    return $this->value.$this->renderChildren();
  }

  /**
   * Close the Tag
   *
   * @return string
   */
  public function close()
  {
    $this->isOpened = false;
    $openedOn       = null;
    $element        = null;

    foreach ($this->children as $childName => $child) {
      if ($child->isOpened) {
        $openedOn = $childName;
        $element .= $child->close();
      } elseif ($openedOn and $child->isAfter($openedOn)) {
        $element .= $child;
      }
    }

    // Invisible tags
    if (!$this->element) {
      return null;
    }

    return $element .= '</'.$this->element.'>';
  }

  /**
   * Default rendering method
   *
   * @return string
   */
  public function render()
  {
    if ($this->isSelfClosing) return $this->open();

    return $this->open().$this->getContent().$this->close();
  }

  /**
   * Get the preferred way to close a tag
   *
   * @return string
   */
  protected function getTagCloser()
  {
    if ($this->isSelfClosing and static::$config['doctype'] == 'xhtml') {
      return ' />';
    }

    return '>';
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// MAGIC METHODS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Dynamically set attributes
   *
   * @param  string $method     An attribute
   * @param  array  $parameters Its value(s)
   */
  public function __call($method, $parameters)
  {
    // Replace underscores
    $method = str_replace('_', '-', $method);

    // Get value and set it
    $value = Helpers::arrayGet($parameters, 0, 'true');
    $this->$method = $value;

    return $this;
  }

  /**
   * Dynamically set an attribute
   *
   * @param string $attribute The attribute
   * @param string $value     Its value
   */
  public function __set($attribute, $value)
  {
    $this->attributes[$attribute] = $value;

    return $this;
  }

  /**
   * Get an attribute or a child
   *
   * @param  string $item The desired child/attribute
   *
   * @return mixed
   */
  public function __get($item)
  {
    if (array_key_exists($item, $this->attributes)) {
      return $this->attributes[$item];
    }

    // Get a child by snake case
    $child = preg_replace_callback('/([A-Z])/', function($match) {
      return '.'.strtolower($match[1]);
    }, $item);
    $child = $this->getChild($child);

    return $child;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// VALUE /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Changes the Tag's element
   *
   * @param string $element
   */
  public function setElement($element)
  {
    $this->element = $element;

    return $this;
  }

  /**
   * Change the object's value
   *
   * @param string $value
   */
  public function setValue($value)
  {
    if (is_array($value)) $this->nestChildren($value);
    else $this->value = $value;

    return $this;
  }

  /**
   * Wrap the value in a tag
   *
   * @param string $tag The tag
   */
  public function wrapValue($tag)
  {
    $this->value = Element::create($tag, $this->value);

    return $this;
  }

  /**
   * Get the value
   *
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }


  /**
   * Get all the children as a string
   *
   * @return string
   */
  protected function renderChildren()
  {
    $children = $this->children;
    foreach ($children as $key => $child) {
      if ($child instanceof Tag) {
        $children[$key] = $child->render();
      }
    }

    return implode($children);
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// ATTRIBUTES ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Return an array of protected properties to bind as attributes
   *
   * @return array
   */
  protected function injectProperties()
  {
    $properties = array();

    foreach ($this->injectedProperties as $property) {
      if (!isset($this->$property)) continue;

      $properties[$property] = $this->$property;
    }

    return $properties;
  }

  /**
   * Set an attribute
   *
   * @param string $attribute An attribute
   * @param string $value     Its value
   */
  public function setAttribute($attribute, $value = null)
  {
    $this->attributes[$attribute] = $value;

    return $this;
  }

  /**
   * Set a bunch of parameters at once
   *
   * @param array $attributes The attributes to add to the existing ones
   *
   * @return Tag
   */
  public function setAttributes($attributes)
  {
    $this->attributes = array_merge($this->attributes, (array) $attributes);

    return $this;
  }

  /**
   * Get all attributes
   *
   * @return array
   */
  public function getAttributes()
  {
    return $this->attributes;
  }

  /**
   * Replace all attributes with the provided array
   *
   * @param array $attributes The attributes to replace with
   *
   * @return Tag
   */
  public function replaceAttributes($attributes)
  {
    $this->attributes = (array) $attributes;

    return $this;
  }

  /**
   * Add one or more classes to the current field
   *
   * @param string $class The class(es) to add
   */
  public function addClass($class)
  {
    if(is_array($class)) $class = implode(' ', $class);

    // Create class attribute if it isn't already
    if (!isset($this->attributes['class'])) {
      $this->attributes['class'] = null;
    }

    // Prevent adding a class twice
    $classes = explode(' ', $this->attributes['class']);
    if (!in_array($class, $classes)) {
      $this->attributes['class'] = trim($this->attributes['class']. ' ' .$class);
    }

    return $this;
  }

  /**
   * Remove one or more classes to the current field
   *
   * @param string $class The class(es) to remove
   */
  public function removeClass($class)
  {
    if (is_array($class)) $class = implode(' ', $class);

    // Cancel if there is no class to begin with
    if (!isset($this->attributes['class'])) {
      return $this;
    }

    $classes = explode(' ', $this->attributes['class']);
    if (in_array($class, $classes)) {
      $this->attributes['class'] = str_replace($class, '', $this->attributes['class']);
      $this->attributes['class'] = trim($this->attributes['class']);
    }

    return $this;
  }

}