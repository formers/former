<?php
/**
 * Elements
 *
 * The different parts of a form that are neither fields nor groups
 * not buttons and stuff
 */
namespace Former\Form;

use Former\Former;
use Former\Helpers;
use HtmlObject\Element;

class Elements
{
  /**
   * Build a new Element
   *
   * @param Container $app
   */
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

    return (string) $this->app['former']->hidden($csrf, $csrf);
  }

  /**
   * Creates a label tag
   *
   * @param  string $name       The field the label's for
   * @param  string $label      The label content
   * @param  array  $attributes The label's attributes
   * @return string             A <label> tag
   */
  public function label($label, $name = null, $attributes = array())
  {
    $label = Helpers::translate($label);

    $attributes['for'] = $name;
    Former::$labels[] = $name;

    return Element::create('label', $label, $attributes);
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

    return Element::legend($legend, $attributes);
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
