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


/**
 * GZip Compression Class
 * Official ZIP file format: http://www.pkware.com/appnote.txt
 *
 * @package io_compression
 */

class GZip extends PEAR
{
	/**
	 * @access private
	 */
	var $_datasec = array();
	
	
	/**
	 * Adds "file content" to archive
	 *
	 * public  void
	 * @param  string $data  file contents
	 * @param  string $name  name of the file in the archive (may contains the path)
	 * @access public
	 */
	function add( $data, $name )
	{
		$unc_len = strlen( $data );
		$crc     = crc32( $data );
		$zdata   = gzdeflate( $data, 9 );
		$c_len   = strlen( $zdata );

    	$fr =
      		"\x1f" .						// ID1                              1
      		"\x8B" .						// ID2                              1
      		"\x08" .						// Compression Method "deflate"     1
      		"\x08" .						// FLaGs "FNAME"                    1
      		"\x00\x00\x00\x00" .			// last mod time & date             4
      		"\x00" .						// eXtra FLags "2"-max "4"-fast     1
      		"\x00" .						// OS "\x00" - FAT                  1
      		$name  .						// orig. file name                var
      		"\x00" .						// zero term.                       1
      		$zdata .
      		$this->_pack( $crc, 4 ) .		// crc32                            4
      		$this->_pack( $unc_len, 4 );	// uncompressed filesize            4


		$this->_datasec[] = $fr;
	}

	/**
	 * @access public
	 */
	function extract( $name )
	{
    	if ( !file_exists( $name ) )
			return null;
    
		$fd = fopen( $name, 'rb' );
		
    	if ( !$content = fread( $fd, filesize( $name ) ) ) 
			return null;
    
		@fclose( $fd );

    	$ret = new stdClass;
    	$ret->part = array();

    	$pointer  = 0;
    	$fpointer = 0;
    	$ret->part[$fpointer]->head = array();

    	if ( "\x1f\x8b" != substr( $content, $pointer,2 ) )
      		return PEAR::raiseError( "No valid gzip format." );
    
    	$pointer += 2;

    	if ( "\x08" != substr( $content, $pointer, 1 ) )
      		return PEAR::raiseError( "Compression method must be 'deflate'." );
    
    	$pointer++;

		// This flag byte is divided into individual bits as follows: 
		// bit 0   FTEXT
		// bit 1   FHCRC
		// bit 2   FEXTRA
		// bit 3   FNAME
		// bit 4   FCOMMENT
		switch ( substr( $content, $pointer, 1 ) )
		{
      		// FNAME
      		case "\x08":
        		$pointer++;

	        	// modification time
       		 	$ret->part[$fpointer]->head['mod_time'] = $this->_unpack( substr($content, $pointer, 2 ) );
        		$pointer += 2;

        		// modification date
        		$ret->part[$fpointer]->head['mod_date'] = $this->_unpack( substr($content, $pointer,2 ) );
        		$pointer += 2;

				// eXtra FLags
				// 2 - compressor used maximum compression, slowest algorithm
				// 4 - compressor used fastest algorithm
				$ret->part[$fpointer]->head['xfl'] = $this->_unpack( substr($content, $pointer,1 ) );
        		$pointer++;

				// Operating System
				// 0   - FAT filesystem (MS-DOS, OS/2, NT/Win32)
				// 3   - Unix
				// 7   - Macintosh
				// 11  - NTFS filesystem (NT)
				// 255 - unknown
        		$ret->part[$fpointer]->head['os'] = $this->_unpack( substr($content, $pointer,1 ) );
        		$pointer++;

				// file name
				for ( $ret->part[$fpointer]->head['file_name'] = ""; substr( $content, $pointer,1 ) != "\x00"; $pointer++ )
          			$ret->part[$fpointer]->head['file_name'] .= substr( $content, $pointer,1 );
       
	    		$pointer++;

        		// compressed blocks...
        		$zdata   = substr( $content, $pointer, -8 );
        		$pointer = strlen( $content ) - 8;

        		// cyclic redundancy check
        		$ret->part[$fpointer]->head['crc32'] = $this->_unpack( substr($content, $pointer,4 ) );
        		$pointer+=4;

        		// size of the original (uncompressed) input data modulo 2^32
        		$ret->part[$fpointer]->head['uncompressed_filesize'] = $this->_unpack( substr( $content, $pointer,4 ) );
        		$pointer+=4;

        		// decompress data and store it at array
        		$ret->part[$fpointer]->body = gzinflate( $zdata );

        		break;

      		default:
        		return null;
    	}

		return $ret;
  	}

	/**
	 * @access public
	 */
	function file()
	{
    	$data = implode( '', $this->_datasec );
    	return $data;
  	}

	/**
	 * @access public
	 */
  	function add_file( $name, $binary = false )
	{
    	if ( !file_exists( $name ) )
			return false;
    
		$fd = $binary? fopen( $name, 'rb' ) : fopen( $name, 'r' );
    
		if ( !$content = fread( $fd, filesize( $name ) ) )
			return false;
    
		fclose( $fd );

    	$this->add( $content, $name );
    	return true;
  	}

	/**
	 * @access public
	 */
  	function write_file( $name )
	{
    	$size = -1;
    
		if ( $fd = fopen( $name, 'wb' ) )
		{
      		$size = fwrite( $fd, $this->file() );
      		fclose( $fd );
    	}
    
		return $size;
  	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _pack( $val, $bytes = 2 )
	{
    	for ( $ret = '', $i = 0; $i < $bytes; $i++, $val = floor( $val / 256 ) )
      		$ret .= chr( $val % 256 );
    
		return $ret;
  	}

	/**
	 * @access private
	 */
  	function _unpack( $val )
	{
    	for ( $len = strlen( $val ), $ret = 0, $i = 0; $i < $len; $i++ )
      		$ret += (int)ord( substr( $val, $i, 1 ) ) * pow( 2, 8 * $i );
    
		return $ret;
  	}
} // END OF GZip

?>
