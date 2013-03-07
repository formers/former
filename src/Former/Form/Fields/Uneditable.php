<?php
namespace Former\Form\Fields;

use Former\Traits\Field;

/**
 * Uneditable and disabled fields
 */
class Uneditable extends Field
{

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Prints out the current tag
   *
   * @return string An uneditable input tag
   */
  public function render()
  {
    $this->attributes = $this->app['former']->getFramework()->addUneditableClasses($this->attributes);

    return $this->app['former']->getFramework()->createDisabledField($this);
  }
}
