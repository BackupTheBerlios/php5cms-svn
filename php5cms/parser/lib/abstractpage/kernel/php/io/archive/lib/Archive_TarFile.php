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
 * Archive_TarFile Class
 *
 * To create tar files:
 *
 * $example = new Archive_TarFile($cwd,$flags); // args optional
 * - current working directory
 * - flags (array):
 *  - overwrite - whether to overwrite existing files or return
 *    an Error object
 *  - defaultperms - default file permissions (like chmod(),
 *    must include 0 in front of value [eg. 0777, 0644])
 *  - recursesd[1,0] - whether or not to include subdirs
 *  - storepath[1,0] - whether or not to store relative paths
 *  - replacestats[array] - values to replace those from files
 *   - mode - same as the result of a fileperms() call
 *   - uid/gid - user/group id
 *   - time - timestamp
 *   - type - file type (5=dir,1=link,0=file)
 *   - link - the file that is linked to
 *   - path - only supported in USTAR, not recommended
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
 
class Archive_TarFile extends Archive 
{
	/**
	 * @access public
	 */
	var $cwd = "./";
	
	/**
	 * @access public
	 */
	var $tardata = "";
	
	/**
	 * @access public
	 */
	var $replacestats = array();
	
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
	var $defaultflags	= array(
		'mode'	=> 0,
		'uid'	=> 0,
		'gid'	=> 0,
		'time'	=> 0,
		'type'	=> 0,
		'link'	=> "",
		'path'	=> "",
	);
	

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Archive_TarFile( $cwd = "./", $flags = array() ) 
	{
		$this->cwd = $cwd;
		$this->defaultflags['mode'] = decoct( 0x8000 | 0x0100 | 0x0080 | 0x0020 | 0x0004 );
		$this->defaultflags['time'] = time();

		if ( isset( $flags['recursesd'] ) )
			$this->recursesd = $flags['recursesd'];
	
		if ( isset( $flags['storepath'] ) )
			$this->storepath = $flags['storepath'];
	
		if ( isset( $flags['replacestats'] ) ) 
		{
			if ( is_array( $flags['replacestats'] ) ) 
			{
				if ( isset( $flags['replacestats']['mode'] ) )
					$this->replacestats['mode'] = $flags['replacestats']['mode'];
				
				if ( isset( $flags['replacestats']['time'] ) )
					$this->replacestats['time'] = $flags['replacestats']['time'];
			}
			else if ( $flags['replacestats'] == 1 ) 
			{
				$this->replacestats['mode'] = $this->defaultflags['mode'];
				$this->replacestats['time'] = $this->defaultflags['time'];
			}
		}

		$this->Archive( $flags );
	}

	
	/**
	 * @access public
	 */
	function addFile( $data, $filename, $flags = array() ) 
	{
		if ( strlen( $filename ) > 99 )
			return PEAR::raiseError( "The file name $filename is too long to archive." );

		$flags['mode'] = isset( $this->replacestats['mode'] )? $this->replacestats['mode'] : ( isset( $flags['mode'] )? $flags['mode'] : $this->defaultflags['mode']);
		$flags['uid']  = isset( $flags['uid']  )? $flags['uid']  : $this->defaultflags['uid'];
		$flags['gid']  = isset( $flags['gid']  )? $flags['gid']  : $this->defaultflags['gid'];
		$flags['time'] = isset( $this->replacestats['time'] )? $this->replacestats['time'] : ( isset( $flags['time'] )? $flags['time'] : $this->defaultflags['time']);
		$flags['type'] = isset( $flags['type'] )? $flags['type'] : $this->defaultflags['type'];
		$flags['link'] = isset( $flags['link'] )? $flags['link'] : $this->defaultflags['link'];
		$flags['path'] = isset( $flags['path'] )? $flags['path'] : $this->defaultflags['path'];
		$flags['size'] = isset( $flags['size'] )? $flags['size'] : strlen( $data );

		if ( $this->storepath != 1 ) 
		{
			$filename = strstr( $filename, "/" )? substr( $filename, strrpos( $filename, "/" ) + 1 ) : $filename;
			$flags['path'] = "";
		}
		else
		{
			$filename = preg_replace( "/^(\.{1,2}(\/|\\\))+/", "", $filename );
		}
		
		$blockbeg = pack( "a100a8a8a8a12a12", $filename, $flags['mode'], sprintf( "%6s ", decoct( $flags['uid'] ) ), sprintf( "%6s ", decoct( $flags['gid'] ) ), sprintf( "%11s ", decoct( $flags['size'] ) ), sprintf( "%11s ", decoct( $flags['time'] ) ) );
		$blockend = pack( "a1a100a6a2a32a32a8a8a155", $flags['type'], $flags['link'], "ustar", "00", "Unknown", "Unknown", "", "", $flags['path'] );
		
		$checksum = 0;
		
		for ( $i = 0; $i < 148; $i++ )
			$checksum += ord( substr( $blockbeg, $i, 1 ) );
			
		for ( $i = 148; $i < 156; $i++ )
			$checksum += ord( " " );
			
		for ( $i = 156; $i < 512; $i++ )
			$checksum += ord( substr( $blockend, $i - 156, 1 ) );
			
		$checksum = pack( "a8", sprintf( "%6s ", decoct( $checksum ) ) );

		if ( $flags['size'] % 512 > 0 )
			$data .= $this->_nullpad( 512 - $flags['size'] % 512 );

		$this->tardata .= $blockbeg . $checksum . $blockend . pack( "a12", "" ) . $data;
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
			$file = array();

			if ( $this->storepath != 1 )
				$file['name'] = strstr( $current, "/" )? substr( $current, strrpos( $current, "/" ) + 1 ) : $current;
			else
				$file['name'] = preg_replace( "/^(\.{1,2}(\/|\\\))+/", "", $current );

			$file['mode'] = @fileperms( $current );

			if ( $file['mode'] & 0x4000 )
				$file['type'] = 5;	// Directory
			else if ( $file['mode'] & 0x8000 )
				$file['type'] = 0;	// Regular
			else if ( $file['mode'] & 0xA000 )
				$file['type'] = 1;	// Link
			else
				$file['type'] = 9;	// Unknown
			
			$file['mode'] = decoct( $file['mode'] );

			if ( $file['type'] == 0 && !@file_exists( $current ) )
			{
				return PEAR::raiseError( "$current does not exist." );
			}
			else if ( strlen( $file['name'] ) > 99 ) 
			{
				$offset = strrpos( $file['name'], "/" ) + 1;
				$file['path'] = substr( $file['name'], 0, $offset );
				$file['name'] = substr( $file['name'], $offset );
				
				if ( strlen( $file['name'] ) > 99 || strlen( $file['path'] ) > 154 )
					return PEAR::raiseError( "The file name {$file['name']} is too long to archive." );
			}
			else
			{
				$file['path'] = "";
			}
			
			$stat = stat( $current );

			if ( ( $file['type'] == 0 || $file['type'] == 1 ) && $fp = @fopen( $current, "rb" ) ) 
			{
				$data = fread( $fp, $stat[7] );
				fclose( $fp );
			}
			else
			{
				$data = "";
			}
			
			$flags = array(
				'mode'	=>   $file['mode'],
				'uid'	=>   $stat[4],
				'gid'	=>   $stat[5],
				'size'	=>   $stat[7],
				'time'	=>   $stat[9],
				'type'	=>   $file['type'],
				'link'	=> ( $file['type'] == 1 )? @readlink( $current ) : "",
				'path'	=>   $file['path'],
			);

			$this->addFile( $data, $file['name'], $flags );
		}

		@chdir( $pwd );
	}

	/**
	 * @access public
	 */
	function extract( $data ) 
	{
		$return = array();
		$blocks = strlen( $data ) / 512 - 1;
		$offset = 0;

		while ( $offset < $blocks ) 
		{
			$header  = substr( $data, 512 * $offset, 512 );
			$current = unpack( "a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1type/a100linkname/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155path", $header );

			foreach ( $current as $k => $v )
				$current[$k] = trim( $v );
				
			$current['mode']     = octdec( $current['mode']     );
			$current['uid']      = octdec( $current['uid']      );
			$current['gid']      = octdec( $current['gid']      );
			$current['size']     = octdec( $current['size']     );
			$current['mtime']    = octdec( $current['mtime']    );
			$current['checksum'] = octdec( $current['checksum'] );
			$current['type']     = octdec( $current['type']     );

			if ( $this->storepath != 1 )
				$current['filename'] = substr( $current['filename'], strrpos( $current['filename'], "/" ) + 1 );

			$checksum = 0;
			
			for ( $i = 0; $i < 148; $i++ )
				$checksum += ord( substr( $header, $i, 1 ) );
				
			for ( $i = 148; $i < 156; $i++ )
				$checksum += ord( " " );
				
			for ( $i = 156; $i < 512; $i++ )
				$checksum += ord( substr( $header, $i, 1 ) );

			if ( $current['checksum'] != $checksum )
				return PEAR::raiseError( "Checksum error." );

			$size = ceil( $current['size'] / 512 );
			$current['data'] = substr( $data, 512 * ( ++$offset ), $current['size'] );
			$offset += $size;
			$return[] = $current;
		}

		return $return;
	}

	/**
	 * @access public
	 */
	function getData() 
	{
		return $this->tardata . pack( "a512", "" );
	}

	/**
	 * @access public
	 */
	function download( $filename ) 
	{
		@header( "Content-type: application/x-tar" );
		@header( "Content-disposition: attachment; filename=$filename" );

		print( $this->getData() );
	}

	
	// private methods
	
	/**
	 * @access private
	 */
	function _nullpad( $bytes ) 
	{
		$return = "";
		
		for ( $i = 0; $i < $bytes; ++$i )
			$return .= "\0";
			
		return $return;
	}
} // END OF Archive_TarFile

?>
