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
 * DirSearch Class
 *
 * The Class assume the variable keyword and look for it
 * in all files in the current or passed directory ($path)
 * and it subdirectories
 *
 * @package search
 */    

class DirSearch extends PEAR
{
	/**
	 * @access public
	 */
	var $ext;
	
	/**
	 * @access public
	 */
    var $excDir;
	
	/**
	 * @access public
	 */
    var $emptySearch = "The keyword(s) have not been informed.";
	
	/**
	 * @access public
	 */
    var $notFound = "No page match in your search.";
    
	/**
	 * @access public
	 */
	var $path;
	
	/**
	 * @access public
	 */
    var $keywords;
	
	/**
	 * @access public
	 */
    var $result;
	
	/**
	 * @access public
	 */
    var $searchTime;
	
	/**
	 * @access public
	 */
	var $totalRes;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function DirSearch()
	{
		$this->path = "." . DIRECTORY_SEPARATOR;
	}
	
	
	/**
	 * Performs the search.
	 *
	 * @access public
	 */
    function find( $keywords )
	{
    	clearstatcache(); 
		
		$timeparts = explode( " ", microtime() );
		$starttime = $timeparts[1] . substr( $timeparts[0], 1 );

		if ( $keywords )
		{
    		$this->keywords = $keywords;
    		$return = $this->checkDir( $this->path );
			
    		if ( count( $return ) == 0 )
				$return = $this->notFound;
				
    		$this->totalRes = count( $return );
    	}
		else
		{
    		$return = $this->emptySearch;
    	}
		
  		$timeparts  = explode( " ", microtime() );
  		$total_time = ( $timeparts[1] . substr( $timeparts[0], 1 ) ) - $starttime;    	
  		$total_time = substr( $total_time, 0, 1 );
  		$this->searchTime = $total_time;
  		
    	return $return;
    }
	
	/**
	 * Get the dir and verify every file in it
	 * if the file has the keyword.
	 *
	 * @access public
	 */
    function checkDir( $path )
	{
    	$path = realpath( $path );
		exec( 'ls ' . $path, $result );

		foreach ( $result as $item )
		{
			$tmp = $path . DIRECTORY_SEPARATOR . $item;
			
			if ( is_dir( $tmp ) )
			{
				$tmp .= DIRECTORY_SEPARATOR;
				
				if ( is_array( $this->excDir ) )
				{
					if ( !in_array( $item, $this->excDir ) )
						$this->checkDir( $tmp );
				}
				else
				{
					$this->checkDir( $tmp );
				}
			}
			else
			{
				if ( $this->checkFile( $tmp ) )
				{
					$folder  = ereg_replace( $_SERVER['DOCUMENT_ROOT'], "", $tmp );
					$webPath = "http://" . $_SERVER["HTTP_HOST"] . "/" . $folder;
					$lastMod = date( "d/m/Y G:i:s ", fileatime( $tmp ) );
					
					$this->result[] = array(
						"Name"       => $item,
						"WebPath"    => $webPath,
						"LastModify" => $lastMod,
						"FileSize"   => filesize( $tmp )
					);
				} 
			}
		}
		
	    return $this->result;
    }
	
	/**
	 * Get a File and verify if the search 
	 * string match with any word in the file.
	 * Returns TRUE if the keyword is found.
	 * Returns FALSE if not, or the file is not valid.
	 *
	 * @access public
	 */
    function checkFile( $arq )
	{
    	if ( is_array( $this->ext ) )
		{
    		$fileExt = substr( $arq, -3 );
    		
			if ( !in_array( $fileExt, $this->ext ) )
    			return false;
    	}
    	
    	if ( file_exists( $arq ) )
		{
			$arq = file( $arq );
			$arq = implode( "", $arq );
			
			if ( eregi( $this->keywords, $arq ) )
				return true;
			
			return false;
		}
		else
		{
			return false;
		}
	}
} // END OF DirSearch

?>