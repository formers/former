<?php
namespace Former\Interfaces;

use Former\Traits\Field;

/**
 * Mandatory methods on all frameworks
 */
interface FrameworkInterface
{
  /**
   * Filter buttons classes
   *
   * @param  array $classes An array of classes
   *
   * @return array A filtered array
   */
  public function filterButtonClasses($classes);

  /**
   * Filter field classes
   *
   * @param  array $classes An array of classes
   *
   * @return array A filtered array
   */
  public function filterFieldClasses($classes);

  /**
   * Add classes to a field
   *
   * @param Field $field
   * @param array $classes The possible classes to add
   *
   * @return Field
   */
  public function getFieldClasses(Field $field, $classes);

  /**
   * Add group classes
   *
   * @return string A list of group classes
   */
  public function getGroupClasses();

  /**
   * Add label classes
   *
   * @param  array $attributes An array of attributes
   *
   * @return array An array of attributes with the label class
   */
  public function getLabelClasses();

  /**
   * Add uneditable field classes
   *
   * @param  array $attributes The attributes
   *
   * @return array An array of attributes with the uneditable class
   */
  public function getUneditableClasses();

  /**
   * Add form class
   *
   * @param  array  $attributes The attributes
   * @param  string $type       The type of form to add
   *
   * @return array
   */
  public function getFormClasses($type);

  /**
   * Add actions block class
   *
   * @param  array  $attributes The attributes
   *
   * @return array
   */
  public function getActionClasses();

  /**
   * Render an help text
   *
   * @param string $text
   * @param array  $attributes
   *
   * @return string
   */
  public function createHelp($text, $attributes = array());

  /**
   * Render a disabled field
   *
   * @param Field $field
   *
   * @return string
   */
  public function createDisabledField(Field $field);

  /**
   * Render an icon
   *
   * @param string $icon       The icon name
   * @param array  $attributes Its attributes
   *
   * @return string
   */
  public function createIcon($iconType, $attributes = array());

  /**
   * Wrap an item to be prepended or appended to the current field
   *
   * @param  string $item
   *
   * @return string A wrapped item
   */
  public function placeAround($item);

  /**
   * Wrap a field with prepended and appended items
   *
   * @param  Field $field
   * @param  array $prepend
   * @param  array $append
   *
   * @return string A field concatented with prepended and/or appended items
   */
  public function prependAppend($field, $prepend, $append);

  /**
   * Wrap a field with potential additional tags
   *
   * @param  Field $field
   *
   * @return string A wrapped field
   */
  public function wrapField($field);
}
