<?php
/**
 * Uneditable
 *
 * Uneditable inputs (which aren't actually fields but you know)
 */
namespace Former\Form\Fields;

use \Former\Traits\Field;

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
    $this->attributes = $this->app['former.framework']->addUneditableClasses($this->attributes);

    return
      '<span'.$this->app['former.helpers']->attributes($this->attributes).'>'.
        $this->app['former.helpers']->entities($this->value).
      '</span>';
  }
}
