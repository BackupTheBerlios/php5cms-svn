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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


using( 'io.archive.lib.Archive' );


/**
 * Archive_GZFile Class
 *
 * To create gzip files:
 *
 * $example = new Archive_GZFile($cwd,$flags); // args optional
 * - current working directory
 * - flags (array):
 *  - overwrite - whether to overwrite existing files or return
 *    an Error object
 *  - defaultperms - default file permissions (like chmod(),
 *    must include 0 in front of value [eg. 0777, 0644])
 *
 * To add files:
 *
 * $example->addFile($data,$filename,$flags);
 * - data - file contents
 * - filename - name of file to be put in archive
 * - flags (all flags are optional)
 * - flags (tar) [array]: -same flags as tarfile()
 * - flags (gzip) [string]: -comment to add to archive
 * - flags (zip) [array] -time - last modification time
 *
 * $example->addFiles($filelist); // tar and zip only
 * - filelist - array of file names relative to CWD
 *
 * $example->addDirectories($dirlist); // tar and zip only
 * - dirlist - array of directory names relative to CWD
 *
 * To output files:
 *
 * $example->getData();
 * - returns file contents
 *
 * $example->download($filename);
 * - filename - the name to give the file that is being sent
 *
 * $example->fileWrite($filename,$perms); // perms optional
 * - filename - the name (including path) of the file to write
 * - perms - permissions to give the file after it is written
 *
 * To extract files:
 *
 * $example->extract($data);
 * - data - data to extract files from
 * - returns an array containing file attributes and contents
 *
 * $example->extractFile($filename);
 * - filename - the name (including path) of the file to use
 * - returns an array containing file attributes and contents
 *
 * @package io_archive_lib
 */
 
class Archive_GZFile extends Archive
{
	/**
	 * @access public
	 */
	var $gzdata = "";

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Archive_GZFile( $flags = array() )
	{
		$this->Archive( $flags );
	}
	

	/**
	 * @access public
	 */
	function addFile( $data, $filename = null, $comment = null ) 
	{
		$flags = bindec( "000" . ( !empty( $comment )? "1" : "0" ) . ( !empty( $filename )? "1" : "0" ) . "000" );
		$this->gzdata .= pack( "C1C1C1C1VC1C1", 0x1f, 0x8b, 8, $flags, time(), 2, 0xFF );

		if ( !empty( $filename ) )
			$this->gzdata .= "$filename\0";

		if ( !empty( $comment ) )
			$this->gzdata .= "$comment\0";

		$this->gzdata .= gzdeflate( $data );
		$this->gzdata .= pack( "VV", crc32( $data ), strlen( $data ) );
	}

	/**
	 * @access public
	 */
	function extract( $data ) 
	{
		$id = unpack( "H2id1/H2id2", substr( $data, 0, 2 ) );
		
		if ( $id['id1'] != "1f" || $id['id2'] != "8b" )
			return PEAR::raiseError( "Not valid gzip data." );

		$temp = unpack( "Cflags", substr( $data, 2, 1 ) );
		$temp = decbin( $temp['flags'] );

		if ( $temp & 0x8 )
			$flags['name'] = 1;
	
		if ( $temp & 0x4 )
			$flags['comment'] = 1;

		$offset   = 10;
		$filename = "";

		while ( !empty( $flags['name'] ) ) 
		{
			$char = substr( $data, $offset, 1 );
			$offset++;

			if ( $char == "\0" )
				break;
				
			$filename .= $char;
		}
		
		if ( $filename == "" )
			$filename = "file";

		$comment = "";
		
		while ( !empty( $flags['comment'] ) ) 
		{
			$char = substr( $data, $offset, 1 );
			$offset++;
			
			if ( $char == "\0" )
				break;
				
			$comment .= $char;
		}

		$temp  = unpack( "Vcrc32/Visize", substr( $data, strlen( $data ) - 8, 8 ) );
		$crc32 = $temp['crc32'];
		$isize = $temp['isize'];
		$data  = gzinflate( substr( $data, $offset, strlen( $data ) - 8 - $offset ) );

		if ( $crc32 != crc32( $data ) )
			return PEAR::raiseError( "Checksum error." );

		return array(
			'filename' => $filename,
			'comment'  => $comment,
			'size'     => $isize,
			'data'     => $data
		);
	}

	/**
	 * @access public
	 */
	function getData() 
	{
		return $this->gzdata;
	}

	/**
	 * @access public
	 */
	function download( $filename ) 
	{
		@header( "Content-type: application/x-gzip" );
		@header( "Content-disposition: attachment; filename=$filename" );

		print( $this->getData() );
	}
} // END OF Archive_GZFile

?>
