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


using( 'util.datetime.DateUtil' );


define( 'FILEUTIL_RECEIVE_OVERWRITE', 1 );
define( 'FILEUTIL_RECEIVE_ASSTRING',  2 );


/**
 * Static helper functions.
 *
 * @package io
 */

class FileUtil
{
	/**
	 * @access public
	 * @static
	 */
	function getFileAttr( $path = '' ) 
	{
		if ( $path == '' ) 
			return false;
			
		if ( !file_exists( $path ) ) 
			return false;

		$fileAttr = array();
		$fileAttr['fileName']      = '';
		$fileAttr['fileExtension'] = '';
		$fileAttr['type']          = filetype(  $path );
		$fileAttr['size']          = filesize(  $path );
		$fileAttr['inode']    	   = fileinode( $path );
		$fileAttr['accessTime']    = fileatime( $path );
		$fileAttr['createdTime']   = filectime( $path );
		$fileAttr['modTime']       = filemtime( $path );
		$fileAttr['groupID']       = filegroup( $path );
		$fileAttr['ownerID']       = fileowner( $path );
		$fileAttr['permissions']   = fileperms( $path );
		$fileAttr['isDirectory']   = is_dir(  $path );
		$fileAttr['isFile']        = is_file( $path );
		$fileAttr['isExecutable']  = is_executable( $path );
		$fileAttr['isReadable']    = is_readable(   $path );
		$fileAttr['isWriteable']   = is_writeable(  $path );
		$fileAttr['path']          = FileUtil::standardizePath( $path );
		$fileAttr['pathStem']      = FileUtil::getPathStem( $path );
		$fileAttr['realPath']      = FileUtil::getRealPath( $path );
		$fileAttr['isLink']        = FileUtil::isLink( $path );
		$fileAttr['sizeString']    = FileUtil::filesizeAsString( $fileAttr['size'] );
		
		if ( $fileAttr['isFile'] ) 
		{
			$fileAttr['fileName']              = FileUtil::getFileName( $path );
			$fileAttr['fileExtension']         = FileUtil::getFileExtension( $path );
			$fileAttr['fileExtensionResolved'] = FileUtil::resolveExtension( $fileAttr['fileExtension'] );
			$fileAttr['mimeTypeSuggestion']    = FileUtil::getMimeType( $fileAttr['fileExtension'] );
		} 
		else if ( $fileAttr['isDirectory'] || $fileAttr['isLink'] ) 
		{
		}

		return $fileAttr;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function standardizePath( $path ) 
	{
		return str_replace( "\\", '/', trim( $path ) );
	}

	/**
	 * @access public
	 * @static
	 */
	function getRealPath( $path = '' ) 
	{
		if ( !$pathHash = FileUtil::getRealPathSplit( $path ) ) 
			return false;
			
		return $pathHash['realPath'];
	}

	/**
	 * @access public
	 * @static
	 */
	function getRealPathSplit( $path = '' ) 
	{
		$pathHash = FileUtil::_realPathSplit( $path );
		
		if ( !file_exists( $pathHash['realPath'] ) ) 
			return false;
		
		if ( @is_dir( $pathHash['realPath'] ) ) 
		{
			if ( !empty( $pathHash['file'] ) ) 
			{
				$pathHash['realPath'] .= '/';
				$pathHash['pathCore'] .= $pathHash['file'] . '/';
				$pathHash['tailDir']   = $pathHash['file'];
				$pathHash['file']      = '';
			}
		}

		return $pathHash;
	}

	/**
	 * @access public
	 * @static
	 */
	function getPathStem( $path ) 
	{
		$ret = FileUtil::standardizePath( $path );
		
		if ( substr( $ret, -1 ) === '/' ) 
			return $ret;
			
		$ret = dirname( $ret );
		
		if ( substr( $ret, -1 ) !== '/' ) 
			$ret .= '/';
			
		return $ret;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function getFileName( $path ) 
	{
		return basename( $path );
	}
	
	/**
	 * Returns the extension of a given filename - ie, anything after the last .
	 *
	 * @access public
	 * @static
	 */
	function getFileExtension( $fullPath ) 
	{
		$fullPath = FileUtil::basename( $fullPath );
		$dotPos   = strrpos( $fullPath, '.' );
		
		if ( $dotPos === false )
			$extention = '';
		else 
			$extention = substr( $fullPath, $dotPos + 1 );
			
		if ( ( $extention == 'lnk' ) && stristr( getenv( "OS" ), "Windows" ) )
			return FileUtil::getFileExtension( substr( $fullPath, 0, -4 ) );

		return $extention;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function basename( $path ) 
	{
		$path = FileUtil::standardizePath( $path );
		
		if ( empty( $path ) ) 
			return '';
			
		if ( substr( $path, -1 ) === '/' ) 
			return '';
			
		$lastPosSlash = strrpos( $path, '/' );
		
		if ( $lastPosSlash === false ) 
			return $path;
			
		return substr( $path, $lastPosSlash + 1 );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function isLink( $fullPath ) 
	{
		if ( stristr( getenv( "OS" ), "Windows" ) ) 
			return ( substr( $fullPath, -4 ) == '.lnk' );
		else 
			return (bool)is_link( $fullPath );
	}
	
    /**
     * Retrieve file contents as a string.
     *
     * <code>
     * $str = FileUtil::getContent( new File( '/etc/passwd' ) );
     * </code>
     *
     * @static
     * @access  public
     * @param   &File file
     * @return  string file contents
     * @throws  Error
     */
    function getContent( &$file ) 
	{
      	$file->open( FILE_MODE_READ );
      	$data = $file->read( $file->size() );
      	$file->close();
      
	  	return $data;
    }
    
    /**
     * Set file contents.
     *
     * <code>
     * $bytes_written = FileUtil::setContents( new File( 'myfile' ), 'Hello world' );
     * </code>
     *
     * @static
     * @access  public
     * @param   &File file
     * @param   string data
     * @return  int filesize
     * @throws  Error
     */
    function setContent( &$file, $data ) 
	{
      	$file->open( FILE_MODE_WRITE );
      	$file->write( $data );
      	$file->close();
      
	  	return $file->size();
    }
	
	/**
	 * @access public
	 * @static
	 */
	function readAll( $fullPath ) 
	{
		if ( $fp = fopen( $fullPath, 'rb' ) ) 
		{
			$fileData = fread( $fp, fileSize( $fullPath ) );
			@fclose( $fp );
			
			return $fileData;
		} 
		else 
		{
			return false;
		}
	}
	
	/**
	 * @access public
	 * @static
	 */
	function onewayWrite( $string, $fullPath ) 
	{
		return FileUtil::_write( $string, $fullPath, 'wb' );
	}

	/**
	 * @access public
	 * @static
	 */
	function onewayAppend( $string, $fullPath ) 
	{
		return FileUtil::_write( $string, $fullPath, 'a' );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function rm( $fullPath ) 
	{
		return (bool)unlink( $fullPath );
	}
	
	/**
	 * Returns a filename without the extension - ie, anything after the last .
	 *
	 * @access public
	 * @static
	 */
	function removeExtension( $name = "" )
	{
		return substr( $name, 0, ( strrpos( $name, "." )? strrpos( $name, "." ) : strlen( $name ) ) );
	}

	/**
	 * Returns the file size as a string.
	 *
	 * @access public
	 * @static
	 */
	function filesizeAsString( $fsize = 0 )
	{
		if ( $fsize >= 1073741824 )
			$fsize = round( $fsize / 1073741824 * 100 ) / 100 . " Gb";
		else if ( $fsize >= 1048576 )
			$fsize = round( $fsize / 1048576 * 100 ) / 100 . " Mb";
		else if ( $fsize >= 1024 )
			$fsize = round( $fsize / 1024 * 100 ) / 100 . " Kb";
		else
			$fsize = $fsize . " bytes";
		
		return $fsize;
	}
	
	/**
	 * Resolve file extension.
	 *
	 * @static
	 * @access  public
	 */
	function resolveExtension( $ext )
	{
		static $extensions = array(
			"txt"  => "Text Document",
			"doc"  => "Microsoft Word Document",
			"dot"  => "Microsoft Word Template",
			"xls"  => "Microsoft Excel Spreadsheet",
			"xlt"  => "Microsoft Excel Template",
			"ppt"  => "Microsoft Powerpoint Presentation",
			"pps"  => "Microsoft Powerpoint Presentation",
			"pot"  => "Microsoft Powerpoint Template",
			"pub"  => "Microsoft Publisher Document",
			"pdf"  => "Adobe Portable Document Format",
			"jpg"  => "JPEG Image",
			"jpeg" => "JPEG Image",
			"gif"  => "GIF Image",
			"tif"  => "TIFF Image",
			"tiff" => "TIFF Image",
			"avi"  => "Windows Video File",
			"mpg"  => "MPEG Video File",
			"mpeg" => "MPEG Video File",
			"mov"  => "Quicktime Video File",
			"fli"  => "Video File",
			"flc"  => "Video File",
			"rm"   => "Real Streaming Media",
			"ram"  => "Real Video File",
			"bmp"  => "Microsoft Windows Bitmap Image",
			"exe"  => "PC Executable Program",
			"wav"  => "Microsoft Sound File",
			"aif"  => "Audio File",
			"aiff" => "Audio File",
			"mp3"  => "MPEG Layer-3 Audio File",
			"htm"  => "Hypertext Web Page",
			"html" => "Hypertext Web Page",
			"zip"  => "Compressed Zip Archive",
			"gz"   => "Compressed GNU/Zip Archive",
			"tgz"  => "Compressed GNU/Zip Tarball",
			"psd"  => "Adobe Photoshop Document",
			"ttf"  => "True Type Font",
			"eps"  => "Encapsulated PostScript",
			"wmf"  => "Windows Meta File",
			"wmv"  => "Windows Media Audio/Video file",
			"asf"  => "Windows Media Audio/Video file",
			"asx"  => "Windows Media Audio/Video file",
			"wma"  => "Windows Media Audio/Video file",
			"wax"  => "Windows Media Audio/Video file",
			"wmv"  => "Windows Media Audio/Video file",
			"wvx"  => "Windows Media Audio/Video file",
			"wm"   => "Windows Media Audio/Video file",
			"wmx"  => "Windows Media Audio/Video file",
			"wmz"  => "Windows Media Audio/Video file",
			"wmd"  => "Windows Media Audio/Video file"
		);
		
		if ( isset( $extensions[$ext] ) )
			return $extensions[$ext];
		else
			return "";
	}
	
	/**
	 * @access public
	 * @static
	 */
	function removeEmptyLines( $fullPath ) 
	{
		$fileContent = @file( $fullPath );
		
		if ( !$fileContent ) 
			return false;
			
		$new = '';
		
		while ( list( $k, $line ) = each( $fileContent ) ) 
		{
			$line = str_replace( "\r", "", $line );
			$line = str_replace( "\n", "", $line );
			
			if ( !empty( $line ) ) 
				$new .= $line . "\n";
		}
		
		$fp = fopen( $fullPath, 'w' );
		fputs( $fp, $new );
		
		return true;
	}

	/**
	 * @access public
	 * @static
	 */
	function countLinesFile( $fullPath ) 
	{
		$t = file( $fullPath );
		return sizeof($t);
	}
	
	/**
	 * @access public
	 * @static
	 */
	function getMimeType( $param ) 
	{
		if ( !isset( $mimeTypes ) ) 
		{
			static $mimeTypes = array();
			$mimeTypes['ez']      = 'application/andrew-inset';
			$mimeTypes['hqx']     = 'application/mac-binhex40';
			$mimeTypes['cpt']     = 'application/mac-compactpro';
			$mimeTypes['doc']     = 'application/msword';
			$mimeTypes['bin']     = 'application/octet-stream';
			$mimeTypes['dms']     = 'application/octet-stream';
			$mimeTypes['lha']     = 'application/octet-stream';
			$mimeTypes['lzh']     = 'application/octet-stream';
			$mimeTypes['exe']     = 'application/octet-stream';
			$mimeTypes['class']   = 'application/octet-stream';
			$mimeTypes['oda']     = 'application/oda';
			$mimeTypes['pdf']     = 'application/pdf';
			$mimeTypes['ai']      = 'application/postscript';
			$mimeTypes['eps']     = 'application/postscript';
			$mimeTypes['ps']      = 'application/postscript';
			$mimeTypes['smi']     = 'application/smil';
			$mimeTypes['smil']    = 'application/smil';
			$mimeTypes['mif']     = 'application/vnd.mif';
			$mimeTypes['xls']     = 'application/vnd.ms-excel';
			$mimeTypes['ppt']     = 'application/vnd.ms-powerpoint';
			$mimeTypes['wbxml']   = 'application/vnd.wap.wbxml';
			$mimeTypes['wmlc']    = 'application/vnd.wap.wmlc';
			$mimeTypes['wmlsc']   = 'application/vnd.wap.wmlscriptc';
			$mimeTypes['bcpio']   = 'application/x-bcpio';
			$mimeTypes['vcd']     = 'application/x-cdlink';
			$mimeTypes['pgn']     = 'application/x-chess-pgn';
			$mimeTypes['cpio']    = 'application/x-cpio';
			$mimeTypes['csh']     = 'application/x-csh';
			$mimeTypes['dcr']     = 'application/x-director';
			$mimeTypes['dir']     = 'application/x-director';
			$mimeTypes['dxr']     = 'application/x-director';
			$mimeTypes['dvi']     = 'application/x-dvi';
			$mimeTypes['spl']     = 'application/x-futuresplash';
			$mimeTypes['gtar']    = 'application/x-gtar';
			$mimeTypes['hdf']     = 'application/x-hdf';
			$mimeTypes['js']      = 'application/x-javascript';
			$mimeTypes['skp']     = 'application/x-koan';
			$mimeTypes['skd']     = 'application/x-koan';
			$mimeTypes['skt']     = 'application/x-koan';
			$mimeTypes['skm']     = 'application/x-koan';
			$mimeTypes['latex']   = 'application/x-latex';
			$mimeTypes['nc']      = 'application/x-netcdf';
			$mimeTypes['cdf']     = 'application/x-netcdf';
			$mimeTypes['sh']      = 'application/x-sh';
			$mimeTypes['shar']    = 'application/x-shar';
			$mimeTypes['swf']     = 'application/x-shockwave-flash';
			$mimeTypes['sit']     = 'application/x-stuffit';
			$mimeTypes['sv4cpio'] = 'application/x-sv4cpio';
			$mimeTypes['sv4crc']  = 'application/x-sv4crc';
			$mimeTypes['tar']     = 'application/x-tar';
			$mimeTypes['tcl']     = 'application/x-tcl';
			$mimeTypes['tex']     = 'application/x-tex';
			$mimeTypes['texinfo'] = 'application/x-texinfo';
			$mimeTypes['texi']    = 'application/x-texinfo';
			$mimeTypes['t']       = 'application/x-troff';
			$mimeTypes['tr']      = 'application/x-troff';
			$mimeTypes['roff']    = 'application/x-troff';
			$mimeTypes['man']     = 'application/x-troff-man';
			$mimeTypes['me']      = 'application/x-troff-me';
			$mimeTypes['ms']      = 'application/x-troff-ms';
			$mimeTypes['ustar']   = 'application/x-ustar';
			$mimeTypes['src']     = 'application/x-wais-source';
			$mimeTypes['zip']     = 'application/zip';
			$mimeTypes['au']      = 'audio/basic';
			$mimeTypes['snd']     = 'audio/basic';
			$mimeTypes['mid']     = 'audio/midi';
			$mimeTypes['midi']    = 'audio/midi';
			$mimeTypes['kar']     = 'audio/midi';
			$mimeTypes['mpga']    = 'audio/mpeg';
			$mimeTypes['mp2']     = 'audio/mpeg';
			$mimeTypes['mp3']     = 'audio/mpeg';
			$mimeTypes['aif']     = 'audio/x-aiff';
			$mimeTypes['aiff']    = 'audio/x-aiff';
			$mimeTypes['aifc']    = 'audio/x-aiff';
			$mimeTypes['ram']     = 'audio/x-pn-realaudio';
			$mimeTypes['rm']      = 'audio/x-pn-realaudio';
			$mimeTypes['rpm']     = 'audio/x-pn-realaudio-plugin';
			$mimeTypes['ra']      = 'audio/x-realaudio';
			$mimeTypes['wav']     = 'audio/x-wav';
			$mimeTypes['pdb']     = 'chemical/x-pdb';
			$mimeTypes['xyz']     = 'chemical/x-xyz';
			$mimeTypes['bmp']     = 'image/bmp';
			$mimeTypes['gif']     = 'image/gif';
			$mimeTypes['ief']     = 'image/ief';
			$mimeTypes['jpeg']    = 'image/jpeg';
			$mimeTypes['jpg']     = 'image/jpeg';
			$mimeTypes['jpe']     = 'image/jpeg';
			$mimeTypes['png']     = 'image/png';
			$mimeTypes['tiff']    = 'image/tiff';
			$mimeTypes['tif']     = 'image/tiff';
			$mimeTypes['wbmp']    = 'image/vnd.wap.wbmp';
			$mimeTypes['ras']     = 'image/x-cmu-raster';
			$mimeTypes['pnm']     = 'image/x-portable-anymap';
			$mimeTypes['pbm']     = 'image/x-portable-bitmap';
			$mimeTypes['pgm']     = 'image/x-portable-graymap';
			$mimeTypes['ppm']     = 'image/x-portable-pixmap';
			$mimeTypes['rgb']     = 'image/x-rgb';
			$mimeTypes['xbm']     = 'image/x-xbitmap';
			$mimeTypes['xpm']     = 'image/x-xpixmap';
			$mimeTypes['xwd']     = 'image/x-xwindowdump';
			$mimeTypes['igs']     = 'model/iges';
			$mimeTypes['iges']    = 'model/iges';
			$mimeTypes['msh']     = 'model/mesh';
			$mimeTypes['mesh']    = 'model/mesh';
			$mimeTypes['silo']    = 'model/mesh';
			$mimeTypes['wrl']     = 'model/vrml';
			$mimeTypes['vrml']    = 'model/vrml';
			$mimeTypes['css']     = 'text/css';
			$mimeTypes['html']    = 'text/html';
			$mimeTypes['htm']     = 'text/html';
			$mimeTypes['asc']     = 'text/plain';
			$mimeTypes['txt']     = 'text/plain';
			$mimeTypes['rtx']     = 'text/richtext';
			$mimeTypes['rtf']     = 'text/rtf';
			$mimeTypes['sgml']    = 'text/sgml';
			$mimeTypes['sgm']     = 'text/sgml';
			$mimeTypes['tsv']     = 'text/tab-separated-values';
			$mimeTypes['wml']     = 'text/vnd.wap.wml';
			$mimeTypes['wmls']    = 'text/vnd.wap.wmlscript';
			$mimeTypes['etx']     = 'text/x-setext';
			$mimeTypes['xml']     = 'text/xml';
			$mimeTypes['mpeg']    = 'video/mpeg';
			$mimeTypes['mpg']     = 'video/mpeg';
			$mimeTypes['mpe']     = 'video/mpeg';
			$mimeTypes['qt']      = 'video/quicktime';
			$mimeTypes['mov']     = 'video/quicktime';
			$mimeTypes['avi']     = 'video/x-msvideo';
			$mimeTypes['movie']   = 'video/x-sgi-movie';
			$mimeTypes['ice']     = 'x-conference/x-cooltalk';
		}

		if ( isset( $mimeTypes[$param] ) ) 
			return $mimeTypes[$param];
		
		// return default value
		return "application/octet-stream";
	}
	
	/**
	 * This is a simple class that will return the created, modified and
	 * accessed times of a file (unix timestamp), and a slightly more fuzzy
	 * time string, such as "today at...", "yesterday at...".  It only includes
	 * a small amount of 'fuzziness' right now, but could easily be built on.
	 *
	 * @access public
	 * @static
	 */
	function getFileDates( $file = "" )
	{
		$file_data = array();
		$file_data[ctime] = filectime( $file );
		$file_data[mtime] = filemtime( $file );
		$file_data[atime] = fileatime( $file );

		$file_data[fuzzy_ctime] = DateUtil::fuzzyTime( $file_data[ctime] );
		$file_data[fuzzy_mtime] = DateUtil::fuzzyTime( $file_data[mtime] );
		$file_data[fuzzy_atime] = DateUtil::fuzzyTime( $file_data[atime] );
		
		return $file_data;
	}

	/** 
	 * Note: The array with the filetype and pattern, separate with a semicolon.  
	 * Each pair in the pattern represents a single byte in the input file. 
	 * The question mark is used to match a single digit but should be used on 
	 * both digits in the byte pair. Originally made to find the filetype of 
	 * an input file uploaded using the POST method, which explains the "none" 
	 * comparison.
	 *
	 * @param  string $filename
	 * @access public
	 */ 	
	function findFileType( $filename )
	{ 
		$types = array( 
        	"zip;$504B", 
        	"lha;$????2D6C68", 
        	"gif;$47494638??", 
        	"jpg;$????????????4A464946", 
        	"exe;$4D5A", 
        	"bmp;$424D" 
    	); 
	
    	$len   = 0; 
    	$match = 0; 
    	$ext   = ""; 
	
    	if ( $filename == "none" )
			return( $ext ); 

    	$fh = fopen( $filename, "r" ); 

    	if ( $fh )
		{ 
        	$tmpBuf = fread( $fh, 250 ); 

        	if ( strlen( $tmpBuf ) == 250 )
			{ 
            	for ( $iOffset = 0; $types[$iOffset]; $iOffset += 1 )
				{ 
                	list ( $ext, $pattern, $junk ) = explode( ";", $types[$iOffset] ); 
                	$len = strlen( $pattern ); 

	                if ( $pattern[0] == '$' )
					{ 
        	            for ( $n = 1; $n < $len; $n += 2 )
						{ 
                	        $lowval  = 0;
							$highval = 0; 

	                        if ( $pattern[$n] == '?' || $pattern[$n + 1] == '?' ) 
    	                        continue; 

	                        $highval = ord( $pattern[$n] )  - 48; 
                        
							if ( $highval > 9 )
								$highval -= 7; 
                        
            	            $lowval = ord( $pattern[$n + 1] ) - 48; 

	                        if ( $lowval > 9 )
								$lowval -= 7;

          	              	if ( ord( $tmpBuf[( $n - 1 ) >> 1] ) == ( ( $highval << 4 ) + $lowval ) )
							{ 
                	            $match = 1; 
                        	} 
                        	else
							{ 
                            	$match = 0; 
                            	break; 
                        	} 
                    	}
					
                    	if ( $match )
							break;
                	} 
            	}
        	}
		
        	if ( !$match )
				$ext = ""; 
        
        	fclose( $fh ); 
    	}
	
    	return ( $ext ); 
	}
	
	/**
	 * @access public
	 * @static
	 */
	function magic( $filename )
	{
		// return the magic portion from the "file" command
		if ( !is_file( $filename ) )
			return PEAR::raiseError( "File does not exist." );
 
	 	$hash = exec( "file \"$filename\"" );
		$hash_array = explode( $filename . ":", $hash );

		return trim ( $hash_array[1] );
	}

	/**
	 * @access public
	 * @static
	 */
	function getSizeStat( $filename )
	{
		if ( !is_file( $filename ) )
			return PEAR::raiseError( "File does not exist." );

		// prevent PHP from choking
		clearstatcache();
		$file_stat = lstat( $filename );

		return $file_stat[7];
	}

	/**
	 * @access public
	 * @static
	 */
	function getTransferTime( $filename, $linespeed )
	{
		// linespeed is 14.4 for 14400, etc... (kbps)
		// returns transfer time in seconds
		$file_size = FileUtil::getSizeStat( $filename );
		$bps = ( $linespeed * 1000 ) / 8;

		return ceil( ( $file_size / $bps ) );
	}

	/**
	 * @access public
	 * @static
	 */
	function getFileWidget( $varname )
	{
		$buffer = "<INPUT TYPE=FILE NAME=\"" . prepare( $varname ) . "\">\n";
		return $buffer;
	}

	/**
	 * @access public
	 * @static
	 */
	function receive( $varname, $destination, $options = 0 )
	{
		global $$varname;
		global ${$varname . "_name"};
		global ${$varname . "_size"};
		global ${$varname . "_type"};

		// exit if no file is received
		if ( $$varname == "none" )
			return false;

		// set local filename to base name of returned value
		$filename = basename( ${$varname . "_name"} );

		// get upload path
		// $upload_path = get_cfg_var( "upload_tmp_dir" ) . "/" . $filename;
		$upload_path = ap_ini_get( "path_tmp_os", "path" ) . $filename;
		
		// handle FILE_RECEIVED_ASSTRING
		if ( $options & FILE_RECEIVED_ASSTRING )
		{
   		 	$file_handle   = fopen( $upload_path, "r" );
   	 		$file_contents = fread( $file_handle, filesize( $upload_path ) );
			
    		fclose ( $upload_path );
			return $file_contents;
		}

		// if the file is already there and no overwrite, return false
		if ( file_exists( $upload_path ) && !( $options & FILEUTIL_RECEIVE_OVERWRITE ) )
			return false;

		// actual copy
		copy( "$upload_path", $destination ); 
		return true;
	}
	
	/**
	 * This function calculates the CRC checksum of the given file and returns it in chars of lower 16 bits.
	 *
	 * @access public
	 * @static
	 */ 	
	function checkCRC( $fname ) 
	{ 
    	// Check sum table 
    	$crctab16 = array( 
        	0x0000,  0x1021,  0x2042,  0x3063,  0x4084,  0x50a5,  0x60c6,  0x70e7, 
        	0x8108,  0x9129,  0xa14a,  0xb16b,  0xc18c,  0xd1ad,  0xe1ce,  0xf1ef, 
        	0x1231,  0x0210,  0x3273,  0x2252,  0x52b5,  0x4294,  0x72f7,  0x62d6, 
        	0x9339,  0x8318,  0xb37b,  0xa35a,  0xd3bd,  0xc39c,  0xf3ff,  0xe3de, 
			0x2462,  0x3443,  0x0420,  0x1401,  0x64e6,  0x74c7,  0x44a4,  0x5485, 
        	0xa56a,  0xb54b,  0x8528,  0x9509,  0xe5ee,  0xf5cf,  0xc5ac,  0xd58d, 
        	0x3653,  0x2672,  0x1611,  0x0630,  0x76d7,  0x66f6,  0x5695,  0x46b4, 
        	0xb75b,  0xa77a,  0x9719,  0x8738,  0xf7df,  0xe7fe,  0xd79d,  0xc7bc, 
        	0x48c4,  0x58e5,  0x6886,  0x78a7,  0x0840,  0x1861,  0x2802,  0x3823, 
        	0xc9cc,  0xd9ed,  0xe98e,  0xf9af,  0x8948,  0x9969,  0xa90a,  0xb92b, 
        	0x5af5,  0x4ad4,  0x7ab7,  0x6a96,  0x1a71,  0x0a50,  0x3a33,  0x2a12, 
        	0xdbfd,  0xcbdc,  0xfbbf,  0xeb9e,  0x9b79,  0x8b58,  0xbb3b,  0xab1a, 
        	0x6ca6,  0x7c87,  0x4ce4,  0x5cc5,  0x2c22,  0x3c03,  0x0c60,  0x1c41, 
        	0xedae,  0xfd8f,  0xcdec,  0xddcd,  0xad2a,  0xbd0b,  0x8d68,  0x9d49, 
        	0x7e97,  0x6eb6,  0x5ed5,  0x4ef4,  0x3e13,  0x2e32,  0x1e51,  0x0e70, 
        	0xff9f,  0xefbe,  0xdfdd,  0xcffc,  0xbf1b,  0xaf3a,  0x9f59,  0x8f78, 
        	0x9188,  0x81a9,  0xb1ca,  0xa1eb,  0xd10c,  0xc12d,  0xf14e,  0xe16f, 
        	0x1080,  0x00a1,  0x30c2,  0x20e3,  0x5004,  0x4025,  0x7046,  0x6067, 
        	0x83b9,  0x9398,  0xa3fb,  0xb3da,  0xc33d,  0xd31c,  0xe37f,  0xf35e, 
        	0x02b1,  0x1290,  0x22f3,  0x32d2,  0x4235,  0x5214,  0x6277,  0x7256, 
        	0xb5ea,  0xa5cb,  0x95a8,  0x8589,  0xf56e,  0xe54f,  0xd52c,  0xc50d, 
        	0x34e2,  0x24c3,  0x14a0,  0x0481,  0x7466,  0x6447,  0x5424,  0x4405, 
        	0xa7db,  0xb7fa,  0x8799,  0x97b8,  0xe75f,  0xf77e,  0xc71d,  0xd73c, 
        	0x26d3,  0x36f2,  0x0691,  0x16b0,  0x6657,  0x7676,  0x4615,  0x5634, 
        	0xd94c,  0xc96d,  0xf90e,  0xe92f,  0x99c8,  0x89e9,  0xb98a,  0xa9ab, 
        	0x5844,  0x4865,  0x7806,  0x6827,  0x18c0,  0x08e1,  0x3882,  0x28a3, 
        	0xcb7d,  0xdb5c,  0xeb3f,  0xfb1e,  0x8bf9,  0x9bd8,  0xabbb,  0xbb9a, 
        	0x4a75,  0x5a54,  0x6a37,  0x7a16,  0x0af1,  0x1ad0,  0x2ab3,  0x3a92, 
        	0xfd2e,  0xed0f,  0xdd6c,  0xcd4d,  0xbdaa,  0xad8b,  0x9de8,  0x8dc9, 
        	0x7c26,  0x6c07,  0x5c64,  0x4c45,  0x3ca2,  0x2c83,  0x1ce0,  0x0cc1, 
        	0xef1f,  0xff3e,  0xcf5d,  0xdf7c,  0xaf9b,  0xbfba,  0x8fd9,  0x9ff8, 
        	0x6e17,  0x7e36,  0x4e55,  0x5e74,  0x2e93,  0x3eb2,  0x0ed1,  0x1ef0
		); 

		// calculate the checksum of the file 
		$fp  = fopen( $fname, "r" ); 
		$crc = 0xffff; 

		while ( 1 )
		{ 
        	$a = fgetc( $fp );  
			
			if ( feof( $fp ) )
				break;
				 
			$crc = $crctab16[( $crc >> 8 ^  ord( $a ) ) & 0xFF ] ^ ( ( $crc << 8 ) & 0xffff ); 
    	} 
    
		fclose( $fp ); 
    	$h = sprintf( "%04x", $crc ); 
    	list( $a, $b ) = array( substr( $h, 0, 2 ), substr( $h, 2, 2 ) ); 

    	return chr( intval( $a, 16 ) ) . chr( intval( $b, 16 ) ); 
	} 

	/**
	 * @access public
	 */
	function exclusiveWrite( $string, $fullPath ) 
	{
		do 
		{
			$openParam = file_exists( $fullPath )? "rb+" : "wb";
			
			if ( ( $fp = @fopen( $fullPath, $openParam ) ) === false ) 
				return PEAR::raiseError( "Failed to open cache-file '{$fullPath}'." );

			$lockTry = 0; 
			$lockOk  = true;
			
			while ( ( $lockOk = flock( $fp, LOCK_EX + LOCK_NB ) ) == false ) 
			{
				if ( $lockTry++ > 3 ) 
					break;
				
				sleep( 1 );
			}

			if ( !$lockOk ) 
				return PEAR::raiseError( "Failed to open lock-file '{$fullPath}' even after '{$lockTry}' tries." );

			@ftruncate( $fp, 0 );
			
			if ( !@fwrite( $fp, $string, strlen( $string ) ) ) 
				return PEAR::raiseError( "Failed to write to file '{$fullPath}' after successfully locking it." );
		} while ( false );
		
		if ( !empty( $fp ) ) 
			@fclose( $fp );

		return true;
	}
	
	/**
	 * Check sanity.
	 *
	 * @access public
	 * @static
	 */
	function isSane( $filename )
	{
		if ( !( is_readable( $filename ) ) )
			return PEAR::raiseError( "File is not readable." );
		
		if ( !( is_writeable( $filename ) ) )
			return PEAR::raiseError( "File is not writeable." );
		
		if ( is_dir( $filename ) )
			return PEAR::raiseError( "File is a directory." );
		
		if ( is_link( $filename ) )
			return PEAR::raiseError( "File is a symlink." );

		return true;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 * @static
	 */
	function _realPathSplit( $path ) 
	{
		$path = trim( $path );
		
		$ret = array(
			'pathRoot' => '', 
			'pathCore' => '', 
			'file'     => '',
			'tailDir'  => '', 
			'realPath' => ''
		);
		
		do 
		{
			if ( empty( $path ) )  
				break;
				
			if ( ( $pos = strpos( $path, ':' ) ) !== false ) 
			{
				$ret['pathRoot'] = substr( $path, 0, $pos + 1 );
				$path = substr( $path, $pos + 1 );
			}

			if ( strpos( $path, '//' ) === 0 ) 
				$ret['pathRoot'] .= '//';
			else if ( $path[0]==='/' ) 
				$ret['pathRoot'] .= '/';
			
			$path  = preg_replace( 
				array(
					';/\./;', 
					';[/\\\\]+;', 
					';^(?:\.)/;', 
					';/\.$;'
				), 
				array(
					'/',
					'/',
					'',
					'/'
				), 
				$path
			);
			
			$newPathlets   = array();
			$newPathletPos = 0;
			$pathlets      = explode( '/', $path );
			$pathletSize   = sizeOf( $pathlets );
			
			for ( $i = 0; $i < $pathletSize; $i++ ) 
			{
				switch ( $pathlets[$i] ) 
				{
					case '..':
						$newPathletPos--;
						
						if ( $newPathletPos < 0 ) 
						{
							$newPathletPos  = 0;
							$newPathlets[0] = '';
						}
						
						$pathlets[$i] = '';
						break;
					
					case '.':

					case '':
						$pathlets[$i] = '';
						break;
						
					default:
						$newPathlets[$newPathletPos] = $pathlets[$i];
						$newPathletPos++;
				}
			}

			if ( $newPathletPos == 0 ) 
			{
				$ret['file'] = $pathlets[$pathletSize-1];
				break;
			}
			
			if ( !empty( $pathlets[$pathletSize-1] ) ) 
			{
				$ret['file'] = $pathlets[$pathletSize - 1];
				$newPathletPos--;
			}

			$pathCore = '';
			
			for ( $i = 0; $i < $newPathletPos; $i++ ) 
			{
				$pathCore .= $newPathlets[$i] . '/';
				
				if ( $i == ( $newPathletPos - 1 ) ) 
					$ret['tailDir'] = $newPathlets[$i];
			}

			$ret['pathCore'] = $pathCore;
		} while ( false );
		
		$ret['realPath'] = $ret['pathRoot'] . $ret['pathCore'] . $ret['file'];
		return $ret;
	}	

	/**
	 * @access private
	 * @static
	 */
	function _write( $string, $fullPath, $mode ) 
	{
		$fp = @fopen( $fullPath, $mode );
		
		if ( !$fp ) 
			return false;
		
		$status = @fwrite( $fp, $string );
		@fclose( $fp );
		
		if ( $status ) 
			return true;
			
		return false;
	}
} // END OF FileUtil

?>
