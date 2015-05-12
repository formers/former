<?php
/**
 * File
 * Port of Laravel 3's mime detection function
 */
namespace Laravel;

class File
{
	private static $mimes = array(
		'ai'    => 'application/postscript',
		'aif'   => 'audio/x-aiff',
		'aifc'  => 'audio/x-aiff',
		'aiff'  => 'audio/x-aiff',
		'avi'   => 'video/x-msvideo',
		'bin'   => 'application/macbinary',
		'bmp'   => 'image/bmp',
		'class' => 'application/octet-stream',
		'cpt'   => 'application/mac-compactpro',
		'css'   => 'text/css',
		'csv'   => array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream'),
		'dcr'   => 'application/x-director',
		'dir'   => 'application/x-director',
		'dll'   => 'application/octet-stream',
		'dms'   => 'application/octet-stream',
		'doc'   => 'application/msword',
		'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'dvi'   => 'application/x-dvi',
		'dxr'   => 'application/x-director',
		'eml'   => 'message/rfc822',
		'eps'   => 'application/postscript',
		'exe'   => array('application/octet-stream', 'application/x-msdownload'),
		'gif'   => 'image/gif',
		'gtar'  => 'application/x-gtar',
		'gz'    => 'application/x-gzip',
		'hqx'   => 'application/mac-binhex40',
		'htm'   => 'text/html',
		'html'  => 'text/html',
		'jpe'   => array('image/jpeg', 'image/pjpeg'),
		'jpeg'  => array('image/jpeg', 'image/pjpeg'),
		'jpg'   => array('image/jpeg', 'image/pjpeg'),
		'js'    => 'application/x-javascript',
		'json'  => array('application/json', 'text/json'),
		'lha'   => 'application/octet-stream',
		'log'   => array('text/plain', 'text/x-log'),
		'lzh'   => 'application/octet-stream',
		'mid'   => 'audio/midi',
		'midi'  => 'audio/midi',
		'mif'   => 'application/vnd.mif',
		'mov'   => 'video/quicktime',
		'movie' => 'video/x-sgi-movie',
		'mp2'   => 'audio/mpeg',
		'mp3'   => array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
		'mpe'   => 'video/mpeg',
		'mpeg'  => 'video/mpeg',
		'mpg'   => 'video/mpeg',
		'mpga'  => 'audio/mpeg',
		'oda'   => 'application/oda',
		'odp'   => 'application/vnd.oasis.opendocument.presentation',
		'ods'   => 'application/vnd.oasis.opendocument.spreadsheet',
		'odt'   => 'application/vnd.oasis.opendocument.text',
		'pdf'   => array('application/pdf', 'application/x-download'),
		'php'   => array('application/x-httpd-php', 'text/x-php'),
		'php3'  => 'application/x-httpd-php',
		'php4'  => 'application/x-httpd-php',
		'phps'  => 'application/x-httpd-php-source',
		'phtml' => 'application/x-httpd-php',
		'png'   => 'image/png',
		'pps'   => array('application/mspowerpoint', 'application/vnd.ms-powerpoint'),
		'ppsx'  => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
		'ppt'   => array('application/vnd.ms-powerpoint', 'application/powerpoint'),
		'pptx'  => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'ps'    => 'application/postscript',
		'psd'   => 'application/x-photoshop',
		'qt'    => 'video/quicktime',
		'ra'    => 'audio/x-realaudio',
		'ram'   => 'audio/x-pn-realaudio',
		'rm'    => 'audio/x-pn-realaudio',
		'rpm'   => 'audio/x-pn-realaudio-plugin',
		'rtf'   => array('application/rtf', 'text/rtf'),
		'rtx'   => 'text/richtext',
		'rv'    => 'video/vnd.rn-realvideo',
		'sea'   => 'application/octet-stream',
		'shtml' => 'text/html',
		'sit'   => 'application/x-stuffit',
		'smi'   => 'application/smil',
		'smil'  => 'application/smil',
		'so'    => 'application/octet-stream',
		'swf'   => 'application/x-shockwave-flash',
		'tar'   => 'application/x-tar',
		'text'  => 'text/plain',
		'tgz'   => array('application/x-tar', 'application/x-gzip-compressed'),
		'tif'   => 'image/tiff',
		'tiff'  => 'image/tiff',
		'txt'   => 'text/plain',
		'wav'   => 'audio/x-wav',
		'wbxml' => 'application/wbxml',
		'wmlc'  => 'application/wmlc',
		'word'  => array('application/msword', 'application/octet-stream'),
		'xht'   => 'application/xhtml+xml',
		'xhtml' => 'application/xhtml+xml',
		'xl'    => 'application/excel',
		'xls'   => array('application/vnd.ms-excel', 'application/excel', 'application/msexcel'),
		'xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'xml'   => 'text/xml',
		'xsl'   => 'text/xml',
		'zip'   => array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
	);

	/**
	 * Get a file MIME type by extension.
	 * <code>
	 *    // Determine the MIME type for the .tar extension
	 *    $mime = File::mime('tar');
	 *    // Return a default value if the MIME can't be determined
	 *    $mime = File::mime('ext', 'application/octet-stream');
	 * </code>
	 *
	 * @param  string $extension
	 * @param  string $default
	 *
	 * @return string
	 */
	public static function mime($extension, $default = 'application/octet-stream')
	{
		$mimes = self::$mimes;

		if (!array_key_exists($extension, $mimes)) {
			return $default;
		}

		return (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
	}
}
