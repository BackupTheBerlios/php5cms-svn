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
|Authors: Richard Heyes <richard.heyes@heyes-computing.net>            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package io
 */
 
class SearchReplace extends PEAR
{
	/**
	 * @access public
	 */
	var $find;
	
	/**
	 * @access public
	 */
	var $replace;
	
	/**
	 * @access public
	 */
	var $files;
	
	/**
	 * @access public
	 */
	var $directories;
	
	/**
	 * @access public
	 */
	var $include_subdir;
	
	/**
	 * @access public
	 */
	var $ignore_lines;
	
	/**
	 * @access public
	 */
	var $ignore_sep;
	
	/**
	 * @access public
	 */
	var $occurences;
	
	/**
	 * @access public
	 */
	var $search_function;


	/** 
	 * Constructor
	 *
	 * @access public
	 */
	function SearchReplace( $find, $replace, $files, $directories = '', $include_subdir = 1, $ignore_lines = array() )
	{
		$this->find            = $find;
		$this->replace         = $replace;
		$this->files           = $files;
		$this->directories     = $directories;
		$this->include_subdir  = $include_subdir;
		$this->ignore_lines    = $ignore_lines;
		$this->occurences      = 0;
		$this->search_function = 'search';
	}


	/**
	 * @access public
	 */	
	function getNumOccurences()
	{
		return $this->occurences;
	}
	
	/**
	 * @access public
	 */
	function setFind( $find )
	{
		$this->find = $find;
	}
	
	/**
	 * @access public
	 */
	function setReplace( $replace )
	{
		$this->replace = $replace;
	}

	/**
	 * @access public
	 */
	function setFiles( $files )
	{
		$this->files = $files;
	}

	/**
	 * @access public
	 */
	function setDirectories( $directories )
	{
		$this->directories = $directories;
	}

	/**
	 * @access public
	 */
	function setIncludeSubdir( $include_subdir )
	{
		$this->include_subdir = $include_subdir;
	}

	/**
	 * @access public
	 */
	function setIgnoreLines( $ignore_lines )
	{
		$this->ignore_lines = $ignore_lines;
	}

	/**
	 * @access public
	 */
	function setSearchFunction( $search_function )
	{
		switch ( $search_function )
		{
			case 'normal' :
				$this->search_function = 'search';
                return true;
				
                break;

			case 'quick' :
				$this->search_function = 'quicksearch';
				return true;
				
				break;

			case 'preg' :
				$this->search_function = 'pregsearch';
				return true;
				
				break;

			case 'ereg' :
				$this->search_function = 'eregsearch';
				return true;
				
			 	break;
		}
	}
	
	/**
	 * @access public
	 */
	function search( $filename )
	{
		$occurences = 0;
		$file_array = file( $filename );

		for ( $i = 0; $i < count( $file_array ); $i++ )
		{
			if ( count( $this->ignore_lines ) > 0 )
			{
				for ( $j = 0; $j < count( $this->ignore_lines ); $j++ )
				{
					if ( substr( $file_array[$i], 0, strlen( $this->ignore_lines[$j] ) ) == $this->ignore_lines[$j] )
						continue 2;
				}
			}

			$occurences += count( explode( $this->find, $file_array[$i] ) ) - 1;
			$file_array[$i] = str_replace( $this->find, $this->replace, $file_array[$i] );
		}

		if ( $occurences > 0 )
			$return = array( $occurences, implode( '', $file_array ) );
		else
			$return = false;

		return $return;
	}

	/**
	 * @access public
	 */
	function quickSearch( $filename )
	{
		clearstatcache();

		$file = fread( $fp = fopen( $filename, 'r' ), filesize( $filename ) );
		fclose( $fp );
		$occurences = count( explode( $this->find, $file ) ) - 1;
		$file = str_replace( $this->find, $this->replace, $file );

		if ( $occurences > 0 )
			$return = array( $occurences, $file );
		else
			$return = false;

		return $return;
	}

	/**
	 * @access public
	 */
	function pregSearch( $filename )
	{
		clearstatcache();

		$file = fread( $fp = fopen( $filename, 'r' ), filesize( $filename ) );
		fclose( $fp );
		$occurences = count( $matches = preg_split( $this->find, $file ) ) - 1;
		$file = preg_replace( $this->find, $this->replace, $file );

		if ( $occurences > 0 )
			$return = array( $occurences, $file );
		else
			$return = false;

		return $return;
	}

	/**
	 * @access public
	 */
	function eregSearch( $filename )
	{
		clearstatcache();

		$file = fread( $fp = fopen( $filename, 'r' ), filesize( $filename ) );
		fclose( $fp );
		$occurences = count( $matches = split( $this->find, $file ) ) -1;
		$file = ereg_replace( $this->find, $this->replace, $file );

		if ( $occurences > 0 )
			$return = array( $occurences, $file );
		else
			$return = false;

		return $return;
	}

	/**
	 * @access public
	 */
	function writeout( $filename, $contents )
	{
		if ( $fp = @fopen( $filename, 'w' ) )
		{
			flock( $fp, 2 );
			fwrite( $fp, $contents );
			flock( $fp, 3 );
			fclose( $fp );
			
			return true;
		}
		else
		{
			return PEAR::raiseError( "Could not open file " . $filename );
		}
	}

	/**
	 * @access public
	 */
	function doFiles( $ser_func )
	{
		if ( !is_array( $this->files ) )
			$this->files = explode( ',', $this->files );

		for ( $i = 0; $i < count( $this->files ); $i++ )
		{
			if ( $this->files[$i] == '.' || $this->files[$i] == '..' )
				continue;
			
			if ( is_dir( $this->files[$i] ) == true )
				continue;

			$newfile = $this->$ser_func( $this->files[$i] );
			
			if ( is_array( $newfile ) == true )
			{
				$this->writeout( $this->files[$i], $newfile[1] );
				$this->occurences += $newfile[0];
			}
		}
	}

	/**
	 * @access public
	 */
	function doDirectories( $ser_func )
	{
		if ( !is_array( $this->directories ) )
			$this->directories = explode( ',', $this->directories );

		for ( $i = 0; $i < count( $this->directories ); $i++ )
		{
			$dh = opendir( $this->directories[$i] );
  			
			while ( $file = readdir( $dh ) )
			{
				if ( $file == '.' || $file == '..' )
					continue;

				if ( is_dir( $this->directories[$i] . $file ) == true )
				{
					if ( $this->include_subdir == 1 )
					{
						$this->directories[] = $this->directories[$i] . $file . DIRECTORY_SEPARATOR;
      					continue;
					}
					else
					{
						continue;
					}
				}

				$newfile = $this->$ser_func( $this->directories[$i] . $file );
				
				if ( is_array( $newfile ) == true )
				{
					$this->writeout( $this->directories[$i] . $file, $newfile[1] );
					$this->occurences += $newfile[0];
				}
			}
		}
	}

	/**
	 * @access public
	 */
	function performSearch()
	{
		if ( $this->find != '' )
		{
			if ( ( is_array( $this->files ) && count( $this->files ) > 0 ) || $this->files != '' )
				$this->doFiles( $this->search_function );

			if ( $this->directories != '' )
				$this->doDirectories( $this->search_function );
		}
	}
} // END OF SearchReplace

?>
