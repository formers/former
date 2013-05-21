<?php
namespace Former\Interfaces;

use Former\Traits\Field;
use HtmlObject\Element;
use Illuminate\Container\Container;

/**
 * Mandatory methods on all frameworks
 */
interface FrameworkInterface
{

  public function __construct(Container $app);

  // Filter arrays ------------------------------------------------- /

  public function filterButtonClasses($classes);
  public function filterFieldClasses($classes);
  public function filterState($state);

  // Get classes to add to attributes ------------------------------ /

  public function getFieldClasses(Field $field, $classes);
  public function getGroupClasses();
  public function getLabelClasses();
  public function getFormClasses($type);
  public function getUneditableClasses();
  public function getActionClasses();

  // Render blocks ------------------------------------------------- /

  public function createLabelOf(Field $field, Element $label);
  public function createHelp($text, $attributes);
  public function createIcon($icon, $attributes);
  public function createDisabledField(Field $field);

  // Wrap blocks (hooks) ------------------------------------------- /

  public function wrapField($field);

}
