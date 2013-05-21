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
    $this->addClass($this->former->getFramework()->getUneditableClasses());

    $this->setId();

    return $this->former->getFramework()->createDisabledField($this);
  }

}
