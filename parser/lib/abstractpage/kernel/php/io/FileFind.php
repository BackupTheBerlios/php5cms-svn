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
 *  Commonly needed functions searching directory trees.
 *
 * @package io
 */

class FileFind extends PEAR
{
    /**
     * internal dir-list
     * @var array
	 * @access private
     */
    var $_dirs = array ();
	
    /**
     * founded files
     * @var array
	 * @access public
     */
    var $files = array ();
	
    /**
     * founded dirs
     * @var array
	 * @access public
     */
    var $directories = array ();


    /**
     * Search the current directory to find matches for the
     * the specified pattern.
     *
     * @param string $pattern a string containing the pattern to search
     * the directory for.
     *
     * @param string $dirpath a string containing the directory path
     * to search.
     *
     * @param string $pattern_type a string containing the type of
     * pattern matching functions to use (can either be 'php' or
     * 'perl').
     *
     * @return array containing all of the files and directories
     * matching the pattern or null if no matches
	 *
	 * @access public
     */
	function &glob( $pattern, $dirpath, $pattern_type = 'php' )
    {
        $dh = @opendir( $dirpath );

        if ( !$dh )
			return false;

		$match_function = FileFind::_determineRegex( $pattern, $pattern_type );
		$matches = array();
		
        while ( $entry = @readdir( $dh ) )
		{
            if ( $match_function( $pattern, $entry ) && $entry != '.' && $entry != '..' )
                $matches[] = $entry;
        }

        @closedir( $dh );
        return ( count( $matches ) > 0 )? $matches : null;
    }

    /**
     * Map the directory tree given by the directory_path parameter.
     *
     * @param string $directory contains the directory path that you
     * want to map.
     *
     * @return array a two element array, the first element containing a list
     * of all the directories, the second element containing a list of all the
     * files.
	 *
	 * @access public
     */
    function &maptree( $directory )
    {
        $this->_dirs = array( $directory );

        while ( count( $this->_dirs ) )
		{
            $dir = array_pop( $this->_dirs );
            FileFind::_build( $dir );
            array_push( $this->directories, $dir );
        }

        return array( $this->directories, $this->files );
    }

    /**
     * Map the directory tree given by the directory parameter.
     *
     * @param string $directory contains the directory path that you
     * want to map.
     *
     * @return array a multidimensional array containing all subdirectories
     * and their files. For example:
     *
     * Array
     * (
     *    [0] => file_1.php
     *    [1] => file_2.php
     *    [subdirname] => Array
     *       (
     *          [0] => file_1.php
     *       )
     * )
	 *
	 * @access public
     */
    function &mapTreeMultiple( $directory )
    {   
        $retval = array();
        $directory .= DIRECTORY_SEPARATOR;
        $dh = opendir( $directory );
		
        while ( $entry = readdir( $dh ) )
		{
            if ( ( $entry != "." ) && ( $entry != ".." ) )
                 array_push( $retval, $entry );
        }

        closedir( $dh );
     
        while ( list( $key, $val ) = each( $retval ) )
		{
            $path = $directory . $val;
            $path = str_replace( "//", "/", $path ); // DIRECTORY_SEPARATOR?
      
            if ( !( is_array( $val ) ) )
			{
                if ( is_dir( $path ) )
				{
                    unset( $retval[$key] );
                    $retval[$val] = FileFind::mapTreeMultiple( $path );
                }
            }
        }
		
        return ( $retval );
    }

    /**
     * Search the specified directory tree with the specified pattern.  Return an
     * array containing all matching files (no directories included).
     *
     * @param string $pattern the pattern to match every file with.
     *
     * @param string $directory the directory tree to search in.
     *
     * @param string $type the type of regular expression support to use, either
     * 'php' or 'perl'.
     *
     * @return array a list of files matching the pattern parameter in the the directory
     * path specified by the directory parameter
	 *
	 * @access public
     */
    function &search( $pattern, $directory, $type = 'php' )
	{
        $matches = array();
        list ( , $files ) = FileFind::maptree( $directory );
        $match_function   = FileFind::_determineRegex( $pattern, $type );

        reset( $files );
        while ( list( , $entry ) = each( $files ) )
		{
            if ( $match_function( $pattern, $entry ) )
                $matches[] = $entry;
        }

        return ( $matches );
    }
	
	
	// private
	
    /**
     * internal function to build singular directory trees, used by
     * FileFind::maptree()
     *
     * @param  string $directory name of the directory to read
     * @return void
	 * @access public
     */
    function _build ( $directory )
    {
        $dh = @opendir( $directory );

        if ( !$dh )
			return false;
			
        while ( $entry = @readdir( $dh ) )
		{
            if ( ( $entry != '.' ) && ( $entry != '..' ) )
			{
                $entry = $directory . DIRECTORY_SEPARATOR . $entry;

                if ( is_dir( $entry ) )
                    array_push( $this->_dirs, $entry );
                else
                    array_push( $this->files, $entry );
            }
        }

        @closedir( $dh );
    }

    /**
     * internal function to determine the type of regular expression to
     * use, implemented by FileFind::glob() and FileFind::search()
     *
     * @param  string $type given RegExp type
     * @return string kind of function ( "eregi", "ereg" or "preg_match") ;
	 * @access public
     */
    function _determineRegex( $pattern, $type )
    {
        if ( !strcasecmp( $type, 'perl' ) )
			$match_function = 'preg_match';
        else if ( !strcasecmp( substr( $pattern, -2 ), '/i' ) )
            $match_function = 'eregi';
        else
            $match_function = 'ereg';

        return $match_function;
    }
} // END OF FileFind

?>
