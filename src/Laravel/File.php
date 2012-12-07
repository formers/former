<?php namespace Laravel; use FilesystemIterator as fIterator;

class File {

	private $mimes = array(
    'hqx'   => 'application/mac-binhex40',
    'cpt'   => 'application/mac-compactpro',
    'csv'   => array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream'),
    'bin'   => 'application/macbinary',
    'dms'   => 'application/octet-stream',
    'lha'   => 'application/octet-stream',
    'lzh'   => 'application/octet-stream',
    'exe'   => array('application/octet-stream', 'application/x-msdownload'),
    'class' => 'application/octet-stream',
    'psd'   => 'application/x-photoshop',
    'so'    => 'application/octet-stream',
    'sea'   => 'application/octet-stream',
    'dll'   => 'application/octet-stream',
    'oda'   => 'application/oda',
    'pdf'   => array('application/pdf', 'application/x-download'),
    'ai'    => 'application/postscript',
    'eps'   => 'application/postscript',
    'ps'    => 'application/postscript',
    'smi'   => 'application/smil',
    'smil'  => 'application/smil',
    'mif'   => 'application/vnd.mif',
    'xls'   => array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
    'ppt'   => array('application/powerpoint', 'application/vnd.ms-powerpoint'),
    'wbxml' => 'application/wbxml',
    'wmlc'  => 'application/wmlc',
    'dcr'   => 'application/x-director',
    'dir'   => 'application/x-director',
    'dxr'   => 'application/x-director',
    'dvi'   => 'application/x-dvi',
    'gtar'  => 'application/x-gtar',
    'gz'    => 'application/x-gzip',
    'php'   => array('application/x-httpd-php', 'text/x-php'),
    'php4'  => 'application/x-httpd-php',
    'php3'  => 'application/x-httpd-php',
    'phtml' => 'application/x-httpd-php',
    'phps'  => 'application/x-httpd-php-source',
    'js'    => 'application/x-javascript',
    'swf'   => 'application/x-shockwave-flash',
    'sit'   => 'application/x-stuffit',
    'tar'   => 'application/x-tar',
    'tgz'   => array('application/x-tar', 'application/x-gzip-compressed'),
    'xhtml' => 'application/xhtml+xml',
    'xht'   => 'application/xhtml+xml',
    'zip'   => array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
    'mid'   => 'audio/midi',
    'midi'  => 'audio/midi',
    'mpga'  => 'audio/mpeg',
    'mp2'   => 'audio/mpeg',
    'mp3'   => array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
    'aif'   => 'audio/x-aiff',
    'aiff'  => 'audio/x-aiff',
    'aifc'  => 'audio/x-aiff',
    'ram'   => 'audio/x-pn-realaudio',
    'rm'    => 'audio/x-pn-realaudio',
    'rpm'   => 'audio/x-pn-realaudio-plugin',
    'ra'    => 'audio/x-realaudio',
    'rv'    => 'video/vnd.rn-realvideo',
    'wav'   => 'audio/x-wav',
    'bmp'   => 'image/bmp',
    'gif'   => 'image/gif',
    'jpeg'  => array('image/jpeg', 'image/pjpeg'),
    'jpg'   => array('image/jpeg', 'image/pjpeg'),
    'jpe'   => array('image/jpeg', 'image/pjpeg'),
    'png'   => 'image/png',
    'tiff'  => 'image/tiff',
    'tif'   => 'image/tiff',
    'css'   => 'text/css',
    'html'  => 'text/html',
    'htm'   => 'text/html',
    'shtml' => 'text/html',
    'txt'   => 'text/plain',
    'text'  => 'text/plain',
    'log'   => array('text/plain', 'text/x-log'),
    'rtx'   => 'text/richtext',
    'rtf'   => 'text/rtf',
    'xml'   => 'text/xml',
    'xsl'   => 'text/xml',
    'mpeg'  => 'video/mpeg',
    'mpg'   => 'video/mpeg',
    'mpe'   => 'video/mpeg',
    'qt'    => 'video/quicktime',
    'mov'   => 'video/quicktime',
    'avi'   => 'video/x-msvideo',
    'movie' => 'video/x-sgi-movie',
    'doc'   => 'application/msword',
    'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'word'  => array('application/msword', 'application/octet-stream'),
    'xl'    => 'application/excel',
    'eml'   => 'message/rfc822',
    'json'  => array('application/json', 'text/json'),
	);

	/**
	 * Determine if a file exists.
	 *
	 * @param  string  $path
	 * @return bool
	 */
	public function exists($path)
	{
		return file_exists($path);
	}

	/**
	 * Get the contents of a file.
	 *
	 * <code>
	 *		// Get the contents of a file
	 *		$contents = File::get(path('app').'routes'.EXT);
	 *
	 *		// Get the contents of a file or return a default value if it doesn't exist
	 *		$contents = File::get(path('app').'routes'.EXT, 'Default Value');
	 * </code>
	 *
	 * @param  string  $path
	 * @param  mixed   $default
	 * @return string
	 */
	public function get($path, $default = null)
	{
		return (file_exists($path)) ? file_get_contents($path) : value($default);
	}

	/**
	 * Write to a file.
	 *
	 * @param  string  $path
	 * @param  string  $data
	 * @return int
	 */
	public function put($path, $data)
	{
		return file_put_contents($path, $data, LOCK_EX);
	}

	/**
	 * Append to a file.
	 *
	 * @param  string  $path
	 * @param  string  $data
	 * @return int
	 */
	public function append($path, $data)
	{
		return file_put_contents($path, $data, LOCK_EX | FILE_APPEND);
	}

	/**
	 * Delete a file.
	 *
	 * @param  string  $path
	 * @return bool
	 */
	public function delete($path)
	{
		if (static::exists($path)) return @unlink($path);
	}

	/**
	 * Move a file to a new location.
	 *
	 * @param  string  $path
	 * @param  string  $target
	 * @return void
	 */
	public function move($path, $target)
	{
		return rename($path, $target);
	}

	/**
	 * Copy a file to a new location.
	 *
	 * @param  string  $path
	 * @param  string  $target
	 * @return void
	 */
	public function copy($path, $target)
	{
		return copy($path, $target);
	}

	/**
	 * Extract the file extension from a file path.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * Get the file type of a given file.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function type($path)
	{
		return filetype($path);
	}

	/**
	 * Get the file size of a given file.
	 *
	 * @param  string  $path
	 * @return int
	 */
	public function size($path)
	{
		return filesize($path);
	}

	/**
	 * Get the file's last modification time.
	 *
	 * @param  string  $path
	 * @return int
	 */
	public function modified($path)
	{
		return filemtime($path);
	}

	/**
	 * Get a file MIME type by extension.
	 *
	 * <code>
	 *		// Determine the MIME type for the .tar extension
	 *		$mime = File::mime('tar');
	 *
	 *		// Return a default value if the MIME can't be determined
	 *		$mime = File::mime('ext', 'application/octet-stream');
	 * </code>
	 *
	 * @param  string  $extension
	 * @param  string  $default
	 * @return string
	 */
	public function mime($extension, $default = 'application/octet-stream')
	{
		$mimes = $this->mimes;

		if ( ! array_key_exists($extension, $mimes)) return $default;

		return (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
	}

	/**
	 * Determine if a file is of a given type.
	 *
	 * The Fileinfo PHP extension is used to determine the file's MIME type.
	 *
	 * <code>
	 *		// Determine if a file is a JPG image
	 *		$jpg = File::is('jpg', 'path/to/file.jpg');
	 *
	 *		// Determine if a file is one of a given list of types
	 *		$image = File::is(array('jpg', 'png', 'gif'), 'path/to/file');
	 * </code>
	 *
	 * @param  array|string  $extensions
	 * @param  string        $path
	 * @return bool
	 */
	public function is($extensions, $path)
	{
		$mimes = $this->mimes;

		$mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);

		// The MIME configuration file contains an array of file extensions and
		// their associated MIME types. We will loop through each extension the
		// developer wants to check and look for the MIME type.
		foreach ((array) $extensions as $extension)
		{
			if (isset($mimes[$extension]) and in_array($mime, (array) $mimes[$extension]))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Create a new directory.
	 *
	 * @param  string  $path
	 * @param  int     $chmod
	 * @return void
	 */
	public function mkdir($path, $chmod = 0777)
	{
		return ( ! is_dir($path)) ? mkdir($path, $chmod, true) : true;
	}

	/**
	 * Move a directory from one location to another.
	 *
	 * @param  string  $source
	 * @param  string  $destination
	 * @param  int     $options
	 * @return void
	 */
	public function mvdir($source, $destination, $options = fIterator::SKIP_DOTS)
	{
		return static::cpdir($source, $destination, true, $options);
	}

	/**
	 * Recursively copy directory contents to another directory.
	 *
	 * @param  string  $source
	 * @param  string  $destination
	 * @param  bool    $delete
	 * @param  int     $options
	 * @return void
	 */
	public function cpdir($source, $destination, $delete = false, $options = fIterator::SKIP_DOTS)
	{
		if ( ! is_dir($source)) return false;

		// First we need to create the destination directory if it doesn't
		// already exists. This directory hosts all of the assets we copy
		// from the installed bundle's source directory.
		if ( ! is_dir($destination))
		{
			mkdir($destination, 0777, true);
		}

		$items = new fIterator($source, $options);

		foreach ($items as $item)
		{
			$location = $destination.DS.$item->getBasename();

			// If the file system item is a directory, we will recurse the
			// function, passing in the item directory. To get the proper
			// destination path, we'll add the basename of the source to
			// to the destination directory.
			if ($item->isDir())
			{
				$path = $item->getRealPath();

				if (! static::cpdir($path, $location, $delete, $options)) return false;

				if ($delete) @rmdir($item->getRealPath());
			}
			// If the file system item is an actual file, we can copy the
			// file from the bundle asset directory to the public asset
			// directory. The "copy" method will overwrite any existing
			// files with the same name.
			else
			{
				if(! copy($item->getRealPath(), $location)) return false;

				if ($delete) @unlink($item->getRealPath());
			}
		}

		unset($items);
		if ($delete) @rmdir($source);

		return true;
	}

	/**
	 * Recursively delete a directory.
	 *
	 * @param  string  $directory
	 * @param  bool    $preserve
	 * @return void
	 */
	public function rmdir($directory, $preserve = false)
	{
		if ( ! is_dir($directory)) return;

		$items = new fIterator($directory);

		foreach ($items as $item)
		{
			// If the item is a directory, we can just recurse into the
			// function and delete that sub-directory, otherwise we'll
			// just delete the file and keep going!
			if ($item->isDir())
			{
				static::rmdir($item->getRealPath());
			}
			else
			{
				@unlink($item->getRealPath());
			}
		}

		unset($items);
		if ( ! $preserve) @rmdir($directory);
	}

	/**
	 * Empty the specified directory of all files and folders.
	 *
	 * @param  string  $directory
	 * @return void
	 */
	public function cleandir($directory)
	{
		return static::rmdir($directory, true);
	}

	/**
	 * Get the most recently modified file in a directory.
	 *
	 * @param  string       $directory
	 * @param  int          $options
	 * @return SplFileInfo
	 */
	public function latest($directory, $options = fIterator::SKIP_DOTS)
	{
		$latest = null;

		$time = 0;

		$items = new fIterator($directory, $options);

		// To get the latest created file, we'll simply loop through the
		// directory, setting the latest file if we encounter a file
		// with a UNIX timestamp greater than the latest one.
		foreach ($items as $item)
		{
			if ($item->getMTime() > $time)
			{
				$latest = $item;
				$time = $item->getMTime();
			}
		}

		return $latest;
	}

}