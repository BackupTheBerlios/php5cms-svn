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
 * With this class it should be easy to get all 
 * the necessary information about a directory recursively.
 *
 * Example
 *
 * $filedir = new FolderArray( "/opt/cvs/pear/" );
 * $filedir->setRecursive = true;
 * $filedir->setIgnore = array( ".", ".." );
 * $filedir->parseDir();
 * print "<pre>";
 * print_r( $filedir->getFolderArray() );
 *
 * @package io
 */
       
class FolderArray extends PEAR 
{
	/**
	 * @access public
	 */
    var $path;

	/**
	 * @access public
	 */
    var $resultArray;
	
	/**
	 * @access public
	 */
    var $ignore = array( 
		".", 
		".." 
	);
	
	/**
	 * @access public
	 */
    var $doRecursive = true;

	/**
	 * @access public
	 */
    var $callback = null;
    
    /**
     * array containing strings of the parameters, which should be included in the result array
     *
     * @var    array
	 * @access public
     */    
    var $getOptions = array(
		"size",               // the size of the file
		"name",               // the name of the file
		"atime",              // the last access time of the file
		"ctime",              // the create time of the file
		"mtime",              // the last modified time of the file
		"ownerID",            // the owner uid of the file
		"OwnerInfo",          // the owner info of the file, see posix_getpwuid() in the manual for details
		"groupID",            // the group id  of the file
		"groupInfo",          // the group info  of the file, see posix_getgrgid() in the manual for details
		"perms",              // the permissions of the file in octal format
		"type",               // the file type as string

		// the following parameters are only for directories
		"dirsize",            // the size of all files in the dir
		"dirsizeRecursive",   // the size of all files in the dir and subdirs
		"count",              // the number of all dirs and files in the dir
		"countRecursive",     // the number of all dirs and files in the dir and subdirs
		"filecount",          // the number of all files in the dir
		"filecountRecursive", // the number of all files in the dir and subdir
		"dircount",           // the number of all dirs in the dir
		"dircountRecursive",  // the number of all dirs in the dir and subdir
	);
	

	/**
	 * Constructor
	 *
	 * @access public
	 */
    function FolderArray( $path, $getOptions = null ) 
	{
        $this->path = $path;
		
        if ( $getOptions )
            $this->getOptions = $getOptions;
    }


	/**
	 * @access public
	 */
    function getFolderArray() 
	{
        return $this->resultArray;
    }

	/**
	 * @access public
	 */
    function setCallback( $function ) 
	{
        return $this->callback = $function;
    }

	/**
	 * @access public
	 */
    function setIgnore( $ignore = array( ".", ".." ) ) 
	{
        $this->ignore = $ignore;
    }

	/**
	 * @access public
	 */
    function setRecursive( $doIt = true )
    {
        $this->doRecursive = $doIt;
    }

	/**
	 * @access public
	 */
    function parseDir( $path = null )
    {
        if ( !$path )
            $path = $this->path;
        
        $this->resultArray = $this->buildFolderArray( $path );
    }

	/**
	 * @access public
	 */	
    function buildFolderArray( $sPath )
    {
    	$dirSum    = 0;
    	$count     = 0;
    	$dircount  = 0;
    	$filecount = 0;
    	$fileArray = array();
    	
        // load directory into array
        $handle = opendir( $sPath  );
		$file   = readdir( $handle );
		
        while ( $file || $file === "0" )
        {
            if ( !in_array( $file, $this->ignore ) )
            {
                if ( in_array( "name", $this->getOptions ) )
                	$fileInfo["name"] = $file;
                
                if ( in_array( "size", $this->getOptions ) )
                {
                    $fileInfo["size"] = filesize( $sPath . DIRECTORY_SEPARATOR . $file );
                    $dirSum += $fileInfo["size"];
                }

                if ( in_array( "atime", $this->getOptions ) )
                	$fileInfo["atime"] = fileatime( $sPath . DIRECTORY_SEPARATOR . $file );
               
                if ( in_array( "ctime", $this->getOptions ) )
                	$fileInfo["ctime"] = filectime( $sPath . DIRECTORY_SEPARATOR . $file );

                if ( in_array( "mtime", $this->getOptions ) )
                	$fileInfo["mtime"] = filemtime( $sPath . DIRECTORY_SEPARATOR . $file );

                if ( in_array( "perms", $this->getOptions ) )
					$fileInfo["perms"] = sprintf( "%o", fileperms( $sPath . DIRECTORY_SEPARATOR . $file ) );

                if ( in_array( "type", $this->getOptions ) )
					$fileInfo["type"] = filetype( $sPath . DIRECTORY_SEPARATOR . $file );

                if ( in_array( "groupID", $this->getOptions ) )
					$fileInfo["groupID"] = filegroup( $sPath . DIRECTORY_SEPARATOR . $file );

                if ( in_array( "groupInfo", $this->getOptions ) && !stristr( getenv( "OS" ), "Windows" ) )
					$fileInfo["groupInfo"] = posix_getgrgid( filegroup( $sPath . DIRECTORY_SEPARATOR . $file ) );
					
                if ( in_array( "ownerID", $this->getOptions ) )
					$fileInfo["ownerID"] = fileowner( $sPath . DIRECTORY_SEPARATOR . $file );
					
                if ( in_array( "ownerInfo", $this->getOptions ) && !stristr( getenv( "OS" ), "Windows" ) )
                	$fileInfo["ownerInfo"] = posix_getpwuid( fileowner( $sPath . DIRECTORY_SEPARATOR . $file ) );

                if ( in_array( "count", $this->getOptions ) )
 					$count++;
               
                if ( in_array( "filecount", $this->getOptions ) )
                {
                    if ( !is_dir( $sPath . $file ) )
                    	$filecount++;
                }
				
                if ( in_array( "dircount", $this->getOptions ) )
                {
                    if ( is_dir( $sPath . $file ) )
						$dircount++;
                }
				
                if ( $fileInfo )
                    $fileArray[$file] = $fileInfo;
                else
                    $fileArray[$file] = null;

                if ( isset( $this->callback ) )
                    call_user_func( $this->callback, $sPath . $file, $fileArray[$file] );
            }
	
			$file = readdir( $handle );
        }

        closedir( $handle );
        
		if ( $this->doRecursive )
        {
            $dirSumRec    =  $dirSum;
            $countRec     =  $count;
            $filecountRec =  $filecount;
            $dircountRec  =  $dircount;
	   
	    	if ( is_array( $fileArray ) )
	    	{            
            	foreach ( $fileArray as $file => $rest )
            	{
                	if ( !in_array( $file, $this->ignore, true ) )
                	{
                    	$path = str_replace( "//", "/", $sPath . $file ); // DIRECTORY_SEPARATOR?
						
                    	if ( is_dir( $sPath . $file ) )
                    	{
                        	$fileArray[$file]["dir"] = $this->buildFolderArray( $sPath . $file . DIRECTORY_SEPARATOR );

                        	if ( in_array( "dirsizeRecursive", $this->getOptions ) )
    							$dirSumRec += $fileArray[$file]["dir"]["dirsizeRecursive"];
                        
                        	if ( in_array( "countRecursive", $this->getOptions ) )
								$countRec += $fileArray[$file]["dir"]["countRecursive"];

                        	if ( in_array( "dircountRecursive", $this->getOptions ) )
								$dircountRec += $fileArray[$file]["dir"]["dircountRecursive"];

                        	if ( in_array( "filecountRecursive", $this->getOptions ) )
								$filecountRec += $fileArray[$file]["dir"]["filecountRecursive"];
                    	}
                	}
            	}
	    	}
            
			if ( in_array( "dirsizeRecursive", $this->getOptions ) )
				$fileArray["dirsizeRecursive"] = $dirSumRec;
            
            if ( in_array( "countRecursive", $this->getOptions ) )
            	$fileArray["countRecursive"] = $countRec;

            if ( in_array( "filecountRecursive", $this->getOptions ) )
				$fileArray["filecountRecursive"] = $filecountRec;

            if ( in_array( "dircountRecursive", $this->getOptions ) )
				$fileArray["dircountRecursive"] = $dircountRec;
        }

        if ( in_array( "dirsize", $this->getOptions ) )
        	$fileArray["dirsize"] = $dirSum;
        
        if ( in_array( "count", $this->getOptions ) )
        	$fileArray["count"] = $count;
        
        if ( in_array( "dircount", $this->getOptions ) )
        	$fileArray["dircount"] = $dircount;

        if ( in_array( "filecount", $this->getOptions ) )
       		$fileArray["filecount"] = $filecount;
        
        return $fileArray;
    }
} // END OF FolderArray

?>
