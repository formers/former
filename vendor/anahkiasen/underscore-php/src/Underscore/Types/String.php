<?php
namespace Underscore\Types;

use Underscore\Traits\Repository;

/**
 * String repository
 */
class String extends Repository
{

  /**
   * The method used to convert new subjects
   * @var string
   */
  protected $typecaster = 'toString';

}
