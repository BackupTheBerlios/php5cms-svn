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
 * Archive_ZipFile Class
 *
 * To create zip files:
 *
 * $example = new Archive_ZipFile(<b></b>$cwd,$flags); // args optional
 * - current working directory
 * - flags (array):
 *  - overwrite - whether to overwrite existing files or return
 *    an Error object
 *  - defaultperms - default file permissions (like chmod(),
 *    must include 0 in front of value [eg. 0777, 0644])
 *  - time - timestamp to use to replace the mtime from files
 *  - recursesd[1,0] - whether or not to include subdirs
 *  - storepath[1,0] - whether or not to store relative paths
 *  - level[0-9] - compression level (0 = none, 9 = max)
 *  - comment - comment to add to the archive
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
 * @package io_archive_lib
 */
 
class Archive_ZipFile extends Archive 
{
	/**
	 * @access public
	 */
	var $cwd = "./";
	
	/**
	 * @access public
	 */
	var $comment = "";
	
	/**
	 * @access public
	 */
	var $level = 9;
	
	/**
	 * @access public
	 */
	var $offset = 0;
	
	/**
	 * @access public
	 */
	var $recursesd = 1;
	
	/**
	 * @access public
	 */
	var $storepath = 1;
	
	/**
	 * @access public
	 */
	var $replacetime = 0;
	
	/**
	 * @access public
	 */
	var $central = array();
	
	/**
	 * @access public
	 */
	var $zipdata = array();

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Archive_ZipFile( $cwd = "./", $flags = array() ) 
	{
		$this->cwd = $cwd;
		
		if ( isset( $flags['time'] ) )
			$this->replacetime = $flags['time'];

		if ( isset( $flags['recursesd'] ) )
			$this->recursesd = $flags['recursesd'];
	
		if ( isset( $flags['storepath'] ) )
			$this->storepath = $flags['storepath'];
	
		if ( isset( $flags['level'] ) )
			$this->level = $flags['level'];

		if ( isset( $flags['comment'] ) )
			$this->comment = $flags['comment'];

		$this->Archive( $flags );
	}


	/**
	 * @access public
	 */
	function addFile( $data, $filename, $flags = array() ) 
	{
		if ( $this->storepath != 1 )
			$filename = strstr( $filename, "/" )? substr( $filename, strrpos( $filename, "/" ) + 1 ) : $filename;
		else
			$filename = preg_replace( "/^(\.{1,2}(\/|\\\))+/", "", $filename );

		$mtime = !empty( $this->replacetime )? getdate( $this->replacetime ) : ( isset( $flags['time'] )? getdate( $flags['time'] ) : getdate() );
		$mtime = preg_replace( "/(..){1}(..){1}(..){1}(..){1}/","\\x\\4\\x\\3\\x\\2\\x\\1", dechex( ( $mtime['year'] - 1980 << 25 ) | ( $mtime['mon'] << 21 ) | ( $mtime['mday'] << 16 ) | ( $mtime['hours'] << 11 ) | ( $mtime['minutes'] << 5 ) | ( $mtime['seconds'] >> 1 ) ) );
		eval( '$mtime = "' . $mtime . '";' );

		$crc32      = crc32( $data );
		$normlength = strlen( $data );
		$data       = gzcompress( $data, $this->level );
		$data       = substr( $data, 2, strlen( $data ) - 6 );
		$complength = strlen( $data );

		$this->zipdata[] = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00" . $mtime . pack( "VVVvv", $crc32, $complength, $normlength, strlen( $filename ), 0x00 ) . $filename . $data . pack( "VVV", $crc32, $complength, $normlength );
		$this->central[] = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00" . $mtime . pack( "VVVvvvvvVV", $crc32, $complength, $normlength, strlen( $filename ), 0x00, 0x00, 0x00, 0x00, 0x0000, $this->offset ) . $filename;

		$this->offset = strlen( implode( "", $this->zipdata ) );
	}

	/**
	 * @access public
	 */
	function addFiles( $filelist ) 
	{
		$pwd = getcwd();
		@chdir( $this->cwd );

		foreach ( $filelist as $current ) 
		{
			if ( !@file_exists( $current ) )
				continue;

			$stat = stat( $current );

			if ( $fp = @fopen( $current, "rb" ) ) 
			{
				$data = fread( $fp, $stat[7] );
				fclose( $fp );
			}
			else
			{
				$data = "";
			}
			
			$flags = array( 'time' => $stat[9] );
			$this->addFile( $data, $current, $flags );
		}

		@chdir( $pwd );
	}

	/**
	 * @access public
	 */
	function getData() 
	{
		$central = implode( "", $this->central );
		$zipdata = implode( "", $this->zipdata );
		
		return $zipdata . $central . "\x50\x4b\x05\x06\x00\x00\x00\x00" . pack( "vvVVv", sizeof( $this->central ), sizeof( $this->central ), strlen( $central ), strlen( $zipdata ), strlen( $this->comment ) ) . $this->comment;
	}

	/**
	 * @access public
	 */
	function download( $filename ) 
	{
		@header( "Content-type: application/zip" );
		@header( "Content-disposition: attachment; filename=$filename" );

		print( $this->getData() );
	}
} // END OF Archive_ZipFile

?>
