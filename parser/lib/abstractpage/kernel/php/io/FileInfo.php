<?php

/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


define( 'FILEINFO_SIZE_BYTE', 1 );
define( 'FILEINFO_SIZE_KB',   2 );
define( 'FILEINFO_SIZE_MB',   3 );
define( 'FILEINFO_SIZE_GB',   4 );
define( 'FILEINFO_SIZE_TB',   5 );


/**
 * The FileInfo class allows you to quickly get infos about a given file:
 * name, basename, pathname, dirname, extension, mime type,
 * file type, dimension, atime, mtime
 *
 * Usage:
 *
 * $file = '/home/programma03-04.doc';
 * 
 * $f = new FileInfo( $file );
 * echo $f->getName()      . "\n";
 * echo $f->getExtension() . "\n";
 * echo $f->getBasename()  . "\n";
 * echo $f->getPath()      . "\n";
 * echo $f->getDirname()   . "\n";
 * echo $f->getSize( FILE_INFO_SIZE_KB, 3 ) . "\n";
 * echo $f->getSize()      . "\n";
 * echo $f->getMime()      . "\n";
 * echo $f->getType()      . "\n";
 * echo $f->getAtime()     . "\n";
 * echo $f->getMtime()     . "\n";
 *
 * @todo adding other file informations
 * @package io
 */
 
class FileInfo extends PEAR
{
	/**
	 * File path - given by the constructor
	 *
	 * @var string
	 * @access private
	 */
	var $_path;
	
	/**
	 * File basename - grabbed from path
	 *
	 * @var string
	 * @access private
	 */
	var $_basename;
	
	/**
	 * File name - grabbed from basename
	 *
	 * @var string
	 * @access private
	 */
	var $_name;
	 
	/**
	 * File extension - grabbed from basename
	 *
	 * @var string
	 * @access private
	 */
	var $_extension;
	
	/**
	 * File dirname - grabbed from path
	 *
	 * @var string
	 * @access private
	 */
	var $_dirname;
	
	/**
	 * File type - derived from extension
	 *
	 * @var string
	 * @access private
	 */
	var $_type;
	
	/**
	 * File atime - last access time
	 *
	 * @var string
	 * @access private
	 */
	var $_atime;
	
	/**
	 * File mtime - last modify time
	 *
	 * @var string
	 * @access private
	 */
	var $_mtime;
	
	/**
	 * File size, in byte
	 *
	 * @var integer
	 * @access private
	 */
	var $_size;
	

	/**
	 * Constructor
	 *
	 * @access public
	 * @param string $file (Required)
	 * @return void
	 */
	function FileInfo( $file )
	{
		$this->_path = $file;
		$tmpinfo = pathinfo($this->_path);
		
		$this->_basename  = $tmpinfo['basename'];
		$this->_dirname   = $tmpinfo['dirname'];
		$this->_extension = $tmpinfo['extension'];
		$this->_name      = basename( $this->_path, '.' . $this->_extension );
		$this->_type      = $this->_setType();
		$this->_mime      = $this->_setMime();
		$this->_atime     = fileatime( $this->_path );
		$this->_mtime     = filemtime( $this->_path );
		$this->_size      = filesize( $this->_path );

		clearstatcache();
	}

	
	/**
	 * Returns the file name.
	 *
	 * @access public
	 * @return string
	 */
	function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Returns the file basename.
	 *
	 * @access public
	 * @return string
	 */
	function getBasename()
	{
		return $this->_basename;
	}
	
	/**
	 * Returns the file extension.
	 *
	 * @access public
	 * @return string
	 */
	function getExtension()
	{
		return $this->_extension;
	}
	
	/**
	 * Returns the file dirname.
	 *
	 * @access public
	 * @return string
	 */
	function getDirname()
	{
		return $this->_dirname;
	}
	
	/**
	 * Returns the file full path.
	 *
	 * @access public
	 * @return string
	 */
	function getPath()
	{
		return $this->_path;
	}
	
	/**
	 * Returns the file mime type.
	 *
	 * @access public
	 * @return string
	 */
	function getMime()
	{
		return $this->_mime;
	}
	
	/**
	 * Returns the file type.
	 *
	 * @access public
	 * @return string
	 */
	function getType()
	{
		return $this->_type;
	}
	
	/**
	 * Returns the file atime in unix timestamp format.
	 *
	 * @access public
	 * @param integer $time (Optional)
	 * @return string
	 */
	function getAtime()
	{
		return $this->_atime;
	}
	
	/**
	 * Returns the file mtime in unix timestamp format.
	 *
	 * @access public
	 * @return string
	 */
	function getMtime()
	{
		return $this->_mtime;
	}
	
	/**
	 * Returns the file size.
	 *
	 * @access public
	 * @param integer $dim (Optional)
	 * @param integer $round (Optional)
	 * @return float
	 */
	function getSize( $dim = FILEINFO_SIZE_BYTE, $round = 2 )
	{
		switch ( $dim ) 
		{
			case FILEINFO_SIZE_KB:
				return round( ( $this->_size / 1024 ), $round );
			
			case FILEINFO_SIZE_MB:
				return round( ( $this->_size / 1024 / 1024 ), $round );
			
			case FILEINFO_SIZE_GB:
				return round( ( $this->_size / 1024 / 1024 / 1024 ), $round );
			
			case FILEINFO_SIZE_TB:
				return round( ( $this->_size / 1024 / 1024 / 1024 / 1024 ), $round );
			
			case FILEINFO_SIZE_BYTE:
			
			default:
				return $this->_size;
		}
	}	
	
	
	// private methods
	
	/**
	 * Returns a filetype string due to known extensions.
	 *
	 * @access private
	 * @return string
	 */
	function _setType()
	{
		switch ( strtolower( $this->_extension ) ) 
		{
			case 'doc':
				$type = 'MS Word document';
				break;

			case 'bin':
		
			case 'dms':
		
			case 'lha':
		
			case 'lzh':
			
			case 'exe':
			
			case 'class':
			
			case 'so':
			
			case 'dll':
				$type = 'Application';
				break;

			case 'pdf':
				$type = 'PDF document';
				break;

			case 'ai':
			
			case 'eps':
			
			case 'ps':
				$type = 'PostScript document';
				break;

			case 'smi':
			
			case 'smil':
				$type = 'SMIL document';
				break;

			case 'xls':
				$type = 'MS Excel document';
				break;

			case 'ppt':
				$type = 'MS Powerpoint document';
				break;

			case 'dvi':
				$type = 'DVI document';
				break;

			case 'gtar':
				$type = 'GTAR archive';
				break;

			case 'gz':
				$type = 'GZIP archive';
				break;

			case 'php':
			
			case 'php3':
			
			case 'php4':
			
			case 'phtml':
				$type = 'PHP source';
				break;
				
			case 'js':
				$type = 'Javascript source';
				break;

			case 'latex':
				$type = 'LaTeX document';
				break;

			case 'sh':
				$type = 'Shell script';
				break;

			case 'swf':
				$type = 'ShockWave Flash';
				break;

			case 'tar':
				$type = 'TAR archive';
				break;

			case 'tcl':
				$type = 'TCL source';
				break;

			case 'tex':
				$type = 'TeX document';
				break;

			case 'texinfo':
			
			case 'texi':
				$type = 'TeXinfo document';
				break;

			case 't':
			
			case 'tr':
			
			case 'roff':
				$type = 'troff document';
				break;

			case 'man':
				$type = 'troff-man document';
				break;

			case 'xhtml':
			
			case 'xht':
				$type = 'XHTML-XML document';
				break;

			case 'xml':
				$type = 'XML document';
				break;

			case 'zip':
				$type = 'ZIP archive';
				break;

			case 'au':
			case 'snd':
				$type = 'File audio';
				break;

			case 'mid':
			
			case 'midi':
			
			case 'kar':
				$type = 'File audio MIDI';
				break;

			case 'mpga':
			
			case 'mp2':
			
			case 'mp3':
				$type = 'File audio MPEG';
				break;

			case 'aif':
			
			case 'aiff':
			
			case 'aifc':
				$type = 'Fle audio AIFF';
				break;

			case 'm3u':
				$type = 'File audio MPEGURL';
				break;

			case 'ram':
			
			case 'rm':
			
			case 'ra':
				$type = 'File audio RealAudio';
				break;

			case 'rpm':
				$type = 'File audio RealAudio plug-in';
				break;

			case 'wav':
				$type = 'File audio WAVE';
				break;

			case 'bmp':
				$type = 'Bitmap image';
				break;

			case 'gif':
				$type = 'GIF image';
				break;

			case 'ief':
				$type = 'IEF image';
				break;

			case 'jpeg':
			
			case 'jpg':
			
			case 'jpe':
				$type = 'JPEG image';
				break;

			case 'png':
				$type = 'PNG image';
				break;

			case 'tiff':
			
			case 'tif':
				$type = 'TIFF image';
				break;

			case 'pnm':
				$type = 'portable-anymap image';
				break;

			case 'pbm':
				$type = 'portable-bitmap image';
				break;

			case 'pgm':
				$type = 'portable-graymap image';
				break;

			case 'ppm':
				$type = 'portable-pixmap image';
				break;

			case 'rgb':
				$type = 'RGB image';
				break;

			case 'xbm':
				$type = 'xbitmap image';
				break;

			case 'xpm':
				$type = 'xpixmap image';
				break;

			case 'xwd':
				$type = 'xwindowdump image';
				break;

			case 'igs':
			
			case 'iges':
				$type = 'iges model';
				break;

			case 'msh':
			
			case 'mesh':
			
			case 'silo':
				$type = 'mesh model';
				break;

			case 'wrl':
			
			case 'vrml':
				$type = 'VRML model';
				break;

			case 'css':
				$type = 'CSS document';
				break;

			case 'htm':
			
			case 'html':
				$type = 'HTML document';
				break;

			case 'asc':
			
			case 'txt':
				$type = 'Plain text document';
				break;

			case 'rtx':
				$type = 'RichText document';
				break;

			case 'rtf':
				$type = 'RichTextFormat document';
				break;

			case 'sgml':
			
			case 'sgm':
				$type = 'SGML document';
				break;

			case 'wml':
				$type = 'wap-wml document';
				break;

			case 'wmls':
				$type = 'wap-wmlscript document';
				break;

			case 'xml':
				$type = 'XML document';
				break;

			case 'xsl':
				$type = 'XSL document';
				break;

			case 'mpeg':
			
			case 'mpg':
			
			case 'mpe':
				$type = 'File video MPEG';
				break;

			case 'qt':
			
			case 'mov':
				$type = 'File video quicktime';
				break;

			case 'mxu':
				$type = 'File video mpegurl';
				break;

			case 'avi':
				$type = 'File video AVI';
				break;
		
			default:
				$type = 'File ' . strtoupper($this->_extension);
		}
		
		return $type;
	}
	
	/**
	 * Returns a mimetype string due to known extensions.
	 *
	 * @access private
	 * @return string
	 */
	function _setMime()
	{
		switch ( strtolower( $this->_extension ) ) 
		{
			case 'ez':
				$mime = 'application/andrew-inset';
				break;

			case 'cpt':
				$mime =  'application/mac-compactpro';
				break;

			case 'doc':
				$mime = 'application/msword';
				break;

			case 'oda':
				$mime = 'application/oda';
				break;		
			
			case 'bin':
			
			case 'dms':
			
			case 'lha':
			
			case 'lzh':
			
			case 'exe':
			
			case 'class':
			
			case 'so':
			
			case 'dll':
				$mime = 'application/octet-stream';
				break;

			case 'pdf':
				$mime = 'application/pdf';
				break;

			case 'ai':
			
			case 'eps':
			
			case 'ps':
				$mime = 'application/postscript';
				break;

			case 'smi':
			
			case 'smil':
				$mime = 'application/smil';
				break;

			case 'mif':
				$mime = 'application/vnd.mif';
				break;

			case 'xls':
				$mime = 'application/vnd.ms-excel';
				break;

			case 'ppt':
				$mime = 'application/vnd.ms-powerpoint';
				break;

			case 'wbxml':
				$mime = 'application/vnd.wap.wbxml';
				break;

			case 'wmlc':
				$mime = 'application/vnd.wap.wmlc';
				break;

			case 'wmlsc':
				$mime = 'application/vnd.wap.wmlscriptc';
				break;

			case 'bcpio':
				$mime = 'application/x-bcpio';
				break;

			case 'vcd':
				$mime = 'application/x-cdlink';
				break;

			case 'pgn':
				$mime = 'application/x-chess-pgn';
				break;

			case 'cpio':
				$mime = 'application/x-cpio';
				break;

			case 'csh':
				$mime = 'application/x-csh';
				break;

			case 'dcr':
				$mime = 'application/x-director';
				break;

			case 'dir':
				$mime = 'application/x-director';
				break;

			case 'dxr':
				$mime = 'application/x-director';
				break;

			case 'dvi':
				$mime = 'application/x-dvi';
				break;

			case 'spl':
				$mime = 'application/x-futuresplash';
				break;

			case 'gtar':
				$mime = 'application/x-gtar';
				break;

			case 'gz':
				$mime = 'application/x-gzip';
				break;

			case 'hdf':
				$mime = 'application/x-hdf';
				break;

			case 'php':
			
			case 'php3':
			
			case 'php4':
			
			case 'phtml':
				$mime = 'application/x-httpd-php';
				break;
				
			case 'js':
				$mime = 'application/x-javascript';
				break;

			case 'skp':
			
			case 'skd':
			
			case 'skt':
			
			case 'skm':
				$mime = 'application/x-koan';
				break;

			case 'latex':
				$mime = 'application/x-latex';
				break;

			case 'nc':
			
			case 'cdf':
				$mime = 'application/x-netcdf';
				break;

			case 'sh':
				$mime = 'application/x-sh';
				break;

			case 'shar':
				$mime = 'application/x-shar';
				break;

			case 'swf':
				$mime = 'application/x-shockwave-flash';
				break;

			case 'sit':
				$mime = 'application/x-stuffit';
				break;

			case 'sv4cpio':
				$mime = 'application/x-sv4cpio';
				break;

			case 'sv4crc':
				$mime = 'application/x-sv4crc';
				break;

			case 'tar':
				$mime = 'application/x-tar';
				break;

			case 'tcl':
				$mime = 'application/x-tcl';
				break;

			case 'tex':
				$mime = 'application/x-tex';
				break;

			case 'texinfo':
			
			case 'texi':
				$mime = 'application/x-texinfo';
				break;

			case 't':
			
			case 'tr':
			
			case 'roff':
				$mime = 'application/x-troff';
				break;

			case 'man':
				$mime = 'application/x-troff-man';
				break;

			case 'me':
				$mime = 'application/x-troff-me';
				break;

			case 'ms':
				$mime = 'application/x-troff-ms';
				break;

			case 'ustar':
				$mime = 'application/x-ustar';
				break;

			case 'src':
				$mime = 'application/x-wais-source';
				break;

			case 'xhtml':
			
			case 'xht':
				$mime = 'application/xhtml+xml';
				break;

			case 'xml':
				$mime = 'application/xml';
				break;

			case 'zip':
				$mime = 'application/zip';
				break;

			case 'au':
			
			case 'snd':
				$mime = 'audio/basic';
				break;

			case 'mid':
			
			case 'midi':
			
			case 'kar':
				$mime = 'audio/midi';
				break;

			case 'mpga':
			
			case 'mp2':
			
			case 'mp3':
				$mime = 'audio/mpeg';
				break;

			case 'aif':
			
			case 'aiff':
			
			case 'aifc':
				$mime = 'audio/x-aiff';
				break;

			case 'm3u':
				$mime = 'audio/x-mpegurl';
				break;

			case 'ram':
			
			case 'rm':
				$mime = 'audio/x-pn-realaudio';
				break;

			case 'rpm':
				$mime = 'audio/x-pn-realaudio-plugin';
				break;

			case 'ra':
				$mime = 'audio/x-realaudio';
				break;

			case 'wav':
				$mime = 'audio/x-wav';
				break;

			case 'pdb':
				$mime = 'chemical/x-pdb';
				break;

			case 'xyz':
				$mime = 'chemical/x-xyz	';
				break;

			case 'bmp':
				$mime = 'image/bmp';
				break;

			case 'gif':
				$mime = 'image/gif';
				break;

			case 'ief':
				$mime = 'image/ief';
				break;

			case 'jpeg':
			
			case 'jpg':
			
			case 'jpe':
				$mime = 'image/jpeg';
				break;

			case 'png':
				$mime = 'image/png';
				break;

			case 'tiff':
			
			case 'tif':
				$mime = 'image/tiff';
				break;

			case 'djvu':
			
			case 'djv':
				$mime = 'image/vnd.djvu';
				break;

			case 'wbmp':
				$mime = 'image/vnd.wap.wbmp';
				break;

			case 'ras':
				$mime = 'image/x-cmu-raster';
				break;

			case 'pnm':
				$mime = 'image/x-portable-anymap';
				break;

			case 'pbm':
				$mime = 'image/x-portable-bitmap';
				break;

			case 'pgm':
				$mime = 'image/x-portable-graymap';
				break;

			case 'ppm':
				$mime = 'image/x-portable-pixmap';
				break;

			case 'rgb':
				$mime = 'image/x-rgb';
				break;

			case 'xbm':
				$mime = 'image/x-xbitmap';
				break;

			case 'xpm':
				$mime = 'image/x-xpixmap';
				break;

			case 'xwd':
				$mime = 'image/x-xwindowdump';
				break;

			case 'igs':
			
			case 'iges':
				$mime = 'model/iges';
				break;

			case 'msh':
			
			case 'mesh':
			
			case 'silo':
				$mime = 'model/mesh';
				break;

			case 'wrl':
			
			case 'vrml':
				$mime = 'model/vrml';
				break;

			case 'css':
				$mime = 'text/css';
				break;

			case 'htm':
			
			case 'html':
				$mime = 'text/html';
				break;

			case 'asc':
			
			case 'txt':
				$mime = 'text/plain';
				break;

			case 'rtx':
				$mime = 'text/richtext';
				break;

			case 'rtf':
				$mime = 'text/rtf';
				break;

			case 'sgml':
			
			case 'sgm':
				$mime = 'text/sgml';
				break;

			case 'tsv':
				$mime = 'text/tab-separated-values';
				break;

			case 'wml':
				$mime = 'text/vnd.wap.wml';
				break;

			case 'wmls':
				$mime = 'text/vnd.wap.wmlscript';
				break;

			case 'ext':
				$mime = 'text/x-setext';
				break;

			case 'xml':
				$mime = 'text/xml';
				break;

			case 'xsl':
				$mime = 'text/xsl';
				break;

			case 'mpeg':
			
			case 'mpg':
			
			case 'mpe':
				$mime = 'video/mpeg';
				break;

			case 'qt':
			
			case 'mov':
				$mime = 'video/quicktime';
				break;

			case 'mxu':
				$mime = 'video/vnd.mpegurl	';
				break;

			case 'avi':
				$mime = 'video/x-msvideo';
				break;

			case 'ice':
				$mime = 'x-conference/x-cooltalk';
				break;
				
			default:
				$mime = 'application/octet-stream';
		}
		
		return $mime;
	}
} // END OF FileInfo

?>
