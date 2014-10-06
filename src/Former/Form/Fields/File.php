<?php
namespace Former\Form\Fields;

use Former\Traits\Field;
use HtmlObject\Input as HtmlInput;
use Illuminate\Container\Container;
use Laravel\File as LaravelFile;

/**
 * Class for file fields
 */
class File extends Field
{

	/**
	 * The maximum file size
	 *
	 * @var integer
	 */
	private $maxSize;

	/**
	 * An array of mime groups to use as shortcuts
	 *
	 * @var array
	 */
	private $mimeGroups = array('audio', 'video', 'image');

	/**
	 * A list of properties to be injected in the attributes
	 *
	 * @var array
	 */
	protected $injectedProperties = array('type', 'name');

	////////////////////////////////////////////////////////////////////
	/////////////////////////// CORE METHODS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Easier arguments order for hidden fields
	 *
	 * @param Container $app        The Illuminate Container
	 * @param string    $type       file
	 * @param string    $name       Field name
	 * @param string    $label      Its label
	 * @param string    $value      Its value
	 * @param array     $attributes Attributes
	 */
	public function __construct(Container $app, $type, $name, $label, $value, $attributes)
	{
		// Multiple files field
		if ($type == 'files') {
			$attributes['multiple'] = 'true';
			$type                   = 'file';
			$name                   = $name.'[]';
		}

		parent::__construct($app, $type, $name, $label, $value, $attributes);
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
			? HtmlInput::hidden('MAX_FILE_SIZE', $this->maxSize)
			: null;

		return $hidden.parent::render();
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////// FIELD METHODS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Set which types of files are accepted by the file input

	 */
	public function accept()
	{
		$mimes = array();

		// Transform all extensions/groups to mime types
		foreach (func_get_args() as $mime) {

			// Shortcuts and extensions
			if (in_array($mime, $this->mimeGroups)) {
				$mime .= '/*';
			}
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
	 * @param integer $size  A maximum size
	 * @param string  $units The size's unit
	 */
	public function max($size, $units = 'KB')
	{
		// Bytes or bits ?
		$unit = substr($units, -1);
		$base = 1024;
		if ($unit == 'b') {
			$size = $size / 8;
		}

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
