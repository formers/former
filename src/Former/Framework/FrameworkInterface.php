<?php
namespace Former\Framework;

use \Former\Field;

interface FrameworkInterface
{
  public function __construct(\Illuminate\Container $app);

  // Filter arrays
  public function filterButtonClasses($classes);
  public function filterFieldClasses($classes);
  public function filterState($state);

  // Add classes to attributes
  public function addGroupClasses($attributes);
  public function addLabelClasses($attributes);

  // Render blocks
  public function createLabelOf(Field $field, $label);
  public function createHelp($text, $attributes);
  public function createIcon($icon, $attributes);

  // Wrap blocks (hooks)
  public function wrapField($field);
}
