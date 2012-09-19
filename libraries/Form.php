<?php
/**
 *
 * Form
 *
 * Construct and maanges the form wrapping all fields
 */
namespace Former;

class Form
{
  /**
   * The Form type
   * @var string
   */
  private $type = 'form-horizontal';

  /**
   * The available form types
   * @var array
   */
  private $availableTypes = array('horizontal', 'vertical', 'inline', 'search');

  public function open($formType, $parameters)
  {
    $method     = 'POST';
    $secure     = false;
    $type       = 'vertical';
    $action     = array_get($parameters, 0);
    $attributes = array_get($parameters, 1);

    // Look for HTTPS form
    if(str_contains($formType, 'secure')) $secure = true;

    // Look for file form
    if(str_contains($formType, 'for_files')) $attributes['enctype'] = 'multipart/form-data';

    // Look for a file type
    foreach ($this->availableTypes as $class) {
      if (str_contains($formType, $class)) {
        $type = $class;
        break;
      }
    }
    $attributes = Helpers::addClass($attributes, 'form-'.$class);

    // Store current form's type
    $this->type = $class;

    // Open the form
    return \Form::open($action, $method, $attributes, $secure);
  }

  /**
   * Closes a Form
   * @return string A closing <form> tag
   */
  public function close()
  {
    return '</form>';
  }
}