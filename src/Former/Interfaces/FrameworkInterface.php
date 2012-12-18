<?php
/**
 * FrameworkInterface
 *
 * Obligatory methods on all frameworks
 */
namespace Former\Interfaces;

use \Former\Traits\Field;
use \Illuminate\Container;

interface FrameworkInterface
{
  public function __construct(Container $app);

  // Filter arrays
  public function filterButtonClasses($classes);
  public function filterFieldClasses($classes);
  public function filterState($state);

  // Add classes to attributes
  public function addFieldClasses(Field $field, $classes);
  public function addGroupClasses($attributes);
  public function addLabelClasses($attributes);

  // Render blocks
  public function createLabelOf(Field $field, $label);
  public function createHelp($text, $attributes);
  public function createIcon($icon, $attributes);
  public function createDisabledField(Field $field);

  // Wrap blocks (hooks)
  public function wrapField($field);
}
