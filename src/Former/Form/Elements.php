<?php
/**
 * Elements
 *
 * The different parts of a form that are neither fields nor groups
 * not buttons and stuff
 */
namespace Former\Form;

use \Former\Helpers;

class Elements
{
  public function __construct($app)
  {
    $this->app = $app;
  }

  /**
   * Generate a hidden field containing the current CSRF token.
   *
   * @return string
   */
  public function token()
  {
    $csrf = $this->app['session']->getToken();

    return $this->app['former']->hidden($csrf, $csrf)->__toString();
  }

  /**
   * Creates a label tag
   *
   * @param  string $label      The label content
   * @param  string $name       The field the label's for
   * @param  array  $attributes The label's attributes
   * @return string             A <label> tag
   */
  public function label($label, $name = null, $attributes = array())
  {
    $label = Helpers::translate($label);

    return $this->app['form']->label($name, $label, $attributes);
  }

  /**
   * Creates a form legend
   *
   * @param  string $legend     The text
   * @param  array  $attributes Its attributes
   * @return string             A <legend> tag
   */
  public function legend($legend, $attributes = array())
  {
    $legend = Helpers::translate($legend);

    return '<legend'.$this->app['html']->attributes($attributes).'>' .$legend. '</legend>';
  }

  /**
   * Close a field group
   *
   * @return string
   */
  public function closeGroup()
  {
    return '</div>';
  }
}
