<?php
namespace Underscore\Types;

use stdClass;
use Underscore\Traits\Repository;

/**
 * Object repository
 */
class Object extends Repository
{

  /**
   * The method used to convert new subjects
   * @var string
   */
  protected $typecaster = 'toObject';

  /**
   * Get a default value for a new repository
   *
   * @return mixed
   */
  protected function getDefault()
  {
    return new stdClass;
  }

}
