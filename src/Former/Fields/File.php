<?php
/**
 * File
 *
 * Class for file fields
 */
namespace Former\Fields;

use \Form;

class File extends \Former\Field
{
  /**
   * The maximum file size
   * @var integer
   */
  private $maxSize;

  /**
   * Easier arguments order for hidden fields
   *
   * @param string $type       hidden
   * @param string $name       Field name
   * @param string $value      Its value
   * @param array  $attributes Attributes
   */
  public function __construct($type, $name, $label, $value, $attributes)
  {
    parent::__construct($type, $name, $label, $value, $attributes);

    // Multiple files field
    if ($this->type == 'files') {
      $this->multiple();
      $this->type = 'file';
      $this->name = $this->name.'[]';
    }
  }

  /**
   * Set which types of files are accepted by the file input
   */
  public function accept()
  {
    $shortcuts = array('audio', 'video', 'image');
    foreach (func_get_args() as $mime) {

      // Shortcuts and extensions
      if(in_array($mime, $shortcuts)) $mime .= '/*';
      $mime = \File::mime($mime, $mime);

      $mimes[] = $mime;
    }

    $this->attributes['accept'] = implode('|', $mimes);
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
  }

  /**
   * Prints out the current tag
   *
   * @return string An input file tag
   */
  public function __toString()
  {
    // Maximum file size
    $hidden = $this->maxSize
      ? Form::hidden('MAX_FILE_SIZE', $this->maxSize)
      : null;

    return Form::input($this->type, $this->name, $this->value, $this->attributes).$hidden;
  }
}
