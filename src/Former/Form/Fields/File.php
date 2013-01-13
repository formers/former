<?php
/**
 * File
 *
 * Class for file fields
 */
namespace Former\Form\Fields;

use \Former\Traits\Field;
use \Laravel\File as LaravelFile;

class File extends Field
{
  /**
   * The maximum file size
   * @var integer
   */
  private $maxSize;

  /**
   * An array of mime groups to use as shortcuts
   * @var array
   */
  private $mimeGroups = array('audio', 'video', 'image');

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Easier arguments order for hidden fields
   *
   * @param string $type       hidden
   * @param string $name       Field name
   * @param string $value      Its value
   * @param array  $attributes Attributes
   */
  public function __construct($app, $type, $name, $label, $value, $attributes)
  {
    parent::__construct($app, $type, $name, $label, $value, $attributes);

    // Multiple files field
    if ($this->isOfType('files')) {
      $this->multiple();
      $this->type = 'file';
      $this->name = $this->name.'[]';
    }
  }

  /**
   * Prints out the current tag
   *
   * @return string An input file tag
   */
  public function render()
  {
    // Maximum file size
    $hidden = $this->maxSize
      ? $this->app['form']->hidden('MAX_FILE_SIZE', $this->maxSize)
      : null;

    return $this->app['form']->input($this->type, $this->name, $this->value, $this->attributes).$hidden;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// FIELD METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set which types of files are accepted by the file input
   *
   * @param string $mimes* A list of extensions/mimes/groups to accept
   */
  public function accept()
  {
    // Transform all extensions/groups to mime types
    foreach (func_get_args() as $mime) {

      // Shortcuts and extensions
      if(in_array($mime, $this->mimeGroups)) $mime .= '/*';
      $mime = LaravelFile::mime($mime, $mime);

      $mimes[] = $mime;
    }

    // Add accept attribute by concatenating the mimes
    $this->attributes['accept'] = implode('|', $mimes);

    return $this;
  }

  /**
   * Set a maximum size for files
   *
   * @param  integer $size A maximum size in Kb
   */
  public function max($size, $units = 'KB')
  {
    // Bytes or bits ?
    $unit = substr($units, -1);
    $base = 1024;
    if($unit == 'b') $size = $size / 8;

    // Convert
    switch ($units[0]) {
      case 'K':
        $size = $size * $base;
        break;
      case 'M':
        $size = $size * pow($base, 2);
        break;
      case 'G':
        $size = $size * pow($base, 3);
        break;
    }

    $this->maxSize = (int) $size;

    return $this;
  }
}
