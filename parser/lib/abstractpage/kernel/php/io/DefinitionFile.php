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
 * Load and save defintion (setting) data.
 *
 * @package io
 */

class DefinitionFile extends PEAR
{
	/**
	 * @access public
	 */
    var $path;
    
	
	/**
	 * Constructor
	 *
	 * @access public
 	 */
    function DefinitionFile( $path = "" )
    {
      	if ( strlen( $path ) > 0 )
        	$this->load( $path );
    }
    
	
	/**
	 * @access public
	 */
    function load($path)
    {
		$fp = @fopen( $path, "r" );
      
	  	if ( $fp !== false )
      	{
        	$this->path = $path;
        	$content = "";
        	
			while ( !feof( $fp ) )
          		$content .= fread( $fp, 4096 );
        	
			fclose( $fp );
        	return $this->loadData( $content );
      	}
      	else
      	{
        	$this->path = "";
        	return false;
      	}
    }
    
	/**
	 * @access public
	 */
    function loadData( $data )
    {
      	$data  = preg_split( "/\r?\n/", $data, -1, PREG_SPLIT_NO_EMPTY );
      	$ret   = array();
      	$group = "";
	  
      	for ( $i = 0; $i < count( $data ); $i++ )
      	{
			$data[$i] = trim( $data[$i] );
        	
			if ( preg_match( "/^(.*?)\s*\{$/", $data[$i], $r ) )
        	{
          		$group = trim( $r[1] );
        	}
        	else if ( $data[$i] == "}" )
        	{
          		$group = "";
        	}
        	else if ( strlen( $group ) > 0 )
        	{
          		if ( preg_match( "/^(.+?)\s*=\s*(.+?)$/", $data[$i], $r ) )
          		{
            		if ( $r[2][0] == "[" && substr( $r[2], -1 ) == "]" )
            		{
              			$ret[$group][$r[1]] = $this->_stringToArray( substr($r[2], 1, -1 ) );
            		}
            		else
            		{
            	  		$ret[$group][$r[1]] = $r[2];
            		}
          		}
        	}
      	}
      
	  	return $ret;
	}
    
	/**
	 * @access public
	 */
    function save( $data, $path = "" )
    {
      	if ( strlen( $path ) > 0 && is_dir( dirname( $path ) ) )
        	return $this->_saveData( $data, $path );
      	else if ( strlen( $this->path ) > 0 )
        	return $this->_saveData( $this->data, $path );
      	else
        	return false;
	}
    
	
	// private methods
	
	/**
	 * @access private
	 */
    function _saveData( $data, $path )
    {
		$fp = @fopen( $path, "w" );
      	
		if ( $fp !== false )
      	{
        	foreach ( $data as $groupname => $groupvalues )
        	{
          		fwrite( $fp, $groupname . " {\n" );
          		
				foreach ( $groupvalues as $name => $value )
          		{
            		fwrite( $fp, "\t" . $name . "\t=\t" );
            
					if ( is_array( $value ) )
              			fwrite( $fp, $this->_arrayToString( $value ) . "\n" );
            		else
              			fwrite( $fp, $value . "\n" );
          		}
          		
				fwrite( $fp, "}\n" );
        	}
      	}
      	else
      	{
        	return false;
      	}
	}
    
	/**
	 * @access private
	 */
    function _arrayToString( &$arr )
    {
		$ret = "";
		
		for ( $i = 0; $i < count( $arr ); $i++ )
      	{
        	if ( strlen( $ret ) > 0 )
				$ret .= ";";
				
        	if ( is_array( $arr[$i] ) )
          		$ret .= $this->_arrayToString( $arr[$i] );
        	else
          		$ret .= $arr[$i];
      	}
      	
		return "[" . $ret . "]";
	}
    
	/**
	 * @access private
	 */
	function _stringToArray( &$str )
    {
      	$ret  = array();
      	$p    = 0;
      	$pmax = strlen( $str );
      	
		while ( $p < $pmax )
      	{
        	if ( substr( $str, $p, 1 ) == "[" )
        	{
          		$c  = 0;
          		$p2 = $p + 1;
          		
				while ( true )
          		{
            		if ( substr( $str, $p2, 1 ) == "]" && $c == 0 )
              			break;
            
            		if ( substr( $str, $p2, 1 ) == "]" ) 
						$c--;
            		else if ( substr( $str, $p2, 1 ) == "[" )
						$c++;
            		
					$p2++;
          		}
          		
				$p++;
          		$ret[] = $this->_stringToArray( substr( $str, $p, $p2 - $p ) );
          		$p = $p2 + 2;
        	}
        	else
        	{
          		$p2 = strpos( $str, ";", $p );
          		
				if ( $p2 !== false )
          		{
            		$ret[] = substr( $str, $p, $p2 - $p );
            		$p = $p2 + 1;
          		}
          		else
          		{
            		$p2 = strpos( $str, "]", $p );
            		
					if ( $p2 !== false ) 
						$ret[] = substr( $str, $p, $p2 - $p );
            		else 
						$ret[] = substr( $str, $p );
            		
					$p = $pmax;
          		}
        	}
      	}
      
	  	return $ret;
	}
} // END OF DefinitionFile

?>
