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
 * Class for reading a directory structure.
 *
 * aFiles contains multiple aFile entries
 * aFile:   Path        => relative path eg. ../xx/yy/
 *          File        => filename eg. filename (without extension)
 *          Extension   => ext
 *          IsDirectory => true/false
 *          FullName    => Path . File . "." . Extension
 *          FileName    => File . "." . Extension
 *
 * Notes
 *
 * Filenames with multiple Extensions: only the last extensions is saved as extensions
 * eg: aaa.bbb.ccc results in File=aaa.bbb and Extension=ccc
 * Filenames are stored in the same case as the are stored in the filesystem
 * sFilter is only applied to files.
 *
 * @package io
 */
 
class FolderStructure extends PEAR
{
	/**
	 * @access public
	 */
    var $aFiles;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function FolderStructure()
    {
        $this->init();
    }

	
	/**
	 * @access public
	 */
    function init()
    {
        unset( $this->aFiles );
        $this->aFiles = array();
    }

    /**
     * @param  string  sPath            path eg. "../xx/yy/" (note the last "/")
     * @param  string  sInclude         regular expression for filtering path- and filenames
     * @param  boolean fRecursive       true/false: go down the whole structure
     * @param  boolean fFiles           result set will contain entries which are files
     * @param  boolean fDirectory       result set will contain entries which are directories
     * @param  string  sRoot            Root-Path. Will be appended to the entries.
     * @param  string  sExclude         regular expression for filtering path- and filenames
	 * @access public
	 */
    function read( $sPath, $sInclude = "", $fRecursive = false, $fFiles = true, $fDirectories = true, $sRoot = "", $sExclude = "" )
    {
        $oHandle = opendir( $sPath );
        
		while ( $sFilename = readdir( $oHandle ) )
        {
            $fInsert = true;

            if ( $sFilename == "." || $sFilename == ".." )
                continue;

            $fIsDirectory = is_dir( $sPath . $sFilename );

            if ( !$fFiles && !$fIsDirectory )
                $fInsert = false;
				
            if ( !$fDirectories && $fIsDirectory )
                $fInsert = false;

            if ( $fInsert && !$fIsDirectory && ( !empty( $sInclude ) || !empty( $sExclude ) ) )
            {
                $sFullname  = $sRoot;
                $sFullname .= $sFilename;

                if ( !empty( $sInclude ) )
				{
                    if ( !ereg( $sInclude, $sFullname ) )
                        $fInsert = false;
				}
				
                if ( !empty( $sExclude ) )
				{
                    if ( ereg( $sExclude, $sFullname ) )
                        $fInsert = false;
				}
            }

            if ( $fInsert )
            {
                $i = strrpos( $sFilename, "." ) + 1;
				
                if ( substr( $sFilename, $i - 1, 1 ) == "." )
                {
                    $sFile = substr( $sFilename, 0, $i - 1 );
                    $sExtension = substr( $sFilename, $i );
                }
                else
                {
                    $sFile = $sFilename;
                    $sExtension = "";
                }

                $aFile = array(
                	"Path" 		  => $sRoot,
                	"File" 		  => $sFile,
                	"Extension"   => $sExtension,
                	"IsDirectory" => $fIsDirectory
				);

                // insert current file into aFiles array
                $this->aFiles[] = $aFile;
            }

            // Recursion?
            if ( $fRecursive && $fIsDirectory )
                $this->read( $sPath . $sFilename . DIRECTORY_SEPARATOR, $sInclude, $fRecursive, $fFiles, $fDirectories, $sRoot . $sFilename . DIRECTORY_SEPARATOR, $sExclude );
        }

        closedir( $oHandle );
    }

	/**
	 * @access public
	 */
    function output()
    {
        reset( $this->aFiles );
        
		while( list( $sKey, $aFile ) = each( $this->aFiles ) )
            $this->outputFile( $aFile );
    }

	/**
	 * @access public
	 */
    function outputFile( $aFile )
	{
        $out  = sprintf( "path: %s<br>\n",       $this->getPath( $aFile ) );
        $out .= sprintf( "file: %s<br>\n",       $this->getFile( $aFile ) );
        $out .= sprintf( "extension: %s<br>\n",  $this->getExtension( $aFile ) );
        $out .= sprintf( "isFolder: %s<br>\n",   $this->getIsFolder( $aFile )? "true" : "false" );
        $out .= sprintf( "isFile: %s<br>\n",     $this->getIsFile( $aFile )?   "true" : "false" );
        $out .= sprintf( "fullName: %s<br>\n",   $this->fullName( $aFile ) );
        $out .= sprintf( "fileName: %s<br>\n",   $this->fileName( $aFile ) );
        $out .= sprintf( "folderName: %s<br>\n", $this->folderName( $aFile ) );
		$out .= "<hr>";
		
		echo( $out );
    }

	/**
	 * @access public
	 */
    function getPath( $aFile )
    {
        return ( $aFile[ "Path" ] );
    }

	/**
	 * @access public
	 */
    function getFile( $aFile )
    {
        return ( $aFile[ "File" ] );
    }

	/**
	 * @access public
	 */
    function getExtension( $aFile )
    {
        return ( $aFile[ "Extension" ] );
    }

	/**
	 * @access public
	 */
    function getIsFolder( $aFile )
    {
        return ( $aFile[ "IsDirectory" ] );
    }

	/**
	 * @access public
	 */
    function getIsFile( $aFile )
    {
        return ( !$this->getIsFolder( $aFile ) );
    }

	/**
	 * @access public
	 */
    function fullName( $aFile )
    {
        return ( $this->getPath( $aFile ) . $this->fileName( $aFile ) );
    }

	/**
	 * @access public
	 */
    function fileName( $aFile )
    {
        $sBuffer = $this->folderName( $aFile );
        
		if ( $this->getIsFolder( $aFile ) )
            $sBuffer .= DIRECTORY_SEPARATOR;

        return ( $sBuffer );
    }
	
	/**
	 * folderName returns the same as fileName, but without a ending "/" for Directories.
	 *
	 * @access public
	 */
    function folderName( $aFile )
    {
        $sBuffer = $this->getExtension( $aFile );
        
		if ( !empty( $sBuffer ) )
            $sBuffer = "." . $sBuffer;
        
		$sBuffer = $this->getFile( $aFile ) . $sBuffer;
        return ( $sBuffer );
    }
} // END OF FolderStructure

?>
