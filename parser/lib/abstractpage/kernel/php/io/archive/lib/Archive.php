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


/**
 * @package io_archive_lib
 */
 
class Archive extends PEAR
{
	/**
	 * @access public
	 */
	var $overwrite = 0;
	
	/**
	 * @access public
	 */
	var $defaultperms = 0644;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Archive( $flags = array() ) 
	{
		if ( isset( $flags['overwrite'] ) )
			$this->overwrite = $flags['overwrite'];
		
		if ( isset( $flags['defaultperms'] ) )
			$this->defaultperms = $flags['defaultperms'];
	}


	/**
	 * @access public
	 */
	function addDirectories( $dirlist ) 
	{
		$pwd = getcwd();
		@chdir( $this->cwd );
		$filelist = array();

		foreach ( $dirlist as $current ) 
		{
			if ( @is_dir( $current ) ) 
			{
				$temp = $this->parseDirectories( $current );
				
				foreach ( $temp as $filename )
					$filelist[] = $filename;
			}
			else if ( @file_exists( $current ) )
			{
				$filelist[] = $current;
			}
		}

		@chdir( $pwd );
		$this->addFiles( $filelist );
	}

	/**
	 * @access public
	 */
	function parseDirectories( $dirname ) 
	{
		$filelist = array();
		$dir = @opendir( $dirname );

		while ( $file = @readdir( $dir ) ) 
		{
			if ( $file == "." || $file == ".." )
			{
				continue;
			}
			else if ( @is_dir( $dirname . "/" . $file ) ) 
			{
				if ( $this->recursesd != 1 )
					continue;
					
				$temp = $this->parseDirectories( $dirname . "/" . $file );
				
				foreach ( $temp as $file2 )
					$filelist[] = $file2;
			}
			else if ( @file_exists( $dirname . "/" . $file ) )
			{
				$filelist[] = $dirname . "/" . $file;
			}
		}

		@closedir( $dir );
		return $filelist;
	}

	/**
	 * @access public
	 */
	function fileWrite( $filename,$perms = null ) 
	{
		if ( $this->overwrite != 1 && @file_exists( $filename ) )
			return PEAR::raiseError( "File $filename already exists." );

		if ( @file_exists( $filename ) )
			@unlink( $filename );

		$fp = @fopen( $filename, "wb" );

		if ( !fwrite( $fp, $this->getData() ) )
			return PEAR::raiseError( "Could not write data to $filename." );

		@fclose( $fp );

		if ( !isset( $perms ) )
			$perms = $this->defaultperms;

		@chmod( $filename, $perms );
	}

	/**
	 * @access public
	 */
	function extractFile( $filename ) 
	{
		if ( $fp = @fopen( $filename, "rb" ) ) 
		{
			return $this->extract( fread( $fp, filesize( $filename ) ) );
			@fclose($fp);
		}
		else
		{
			return PEAR::raiseError( "Could not open $filename." );
		}
	}
	
	
	// static
	
	function factory( $type )
	{
		// TODO
		
		switch ( $type )
		{
			case 'zip':
				break;
			
			case 'tar':
			
			case 'tarball':
				break;
			
			case 'gz':
			
			case 'gzip':
			
			case 'g-zip':
			
			case 'gnuzip':
			
			case 'gnu-zip':
			
			default:
		}
	}
} // END OF Archive

?>
