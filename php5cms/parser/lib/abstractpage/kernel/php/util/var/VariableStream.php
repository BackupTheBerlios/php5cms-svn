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
 * Basic stream wrapper.
 *
 * Usage:
 *
 * stream_wrapper_register( "var", "VariableStream" )
 *		or die( "Failed to register protocol." );
 * 
 * $myvar = "";
 *    
 * $fp = fopen( "var://myvar", "r+" );
 * 
 * fwrite( $fp, "line1\n" );
 * fwrite( $fp, "line2\n" );
 * fwrite( $fp, "line3\n" );
 * 
 * rewind( $fp );
 *
 * while ( !feof( $fp ) )
 *		echo fgets($fp);
 * 
 * fclose( $fp );
 * var_dump( $myvar );
 *
 * @package util_var
 */
 
class VariableStream
{
	/**
	 * @access public
	 */
	var $position;
	
	/**
	 * @access public
	 */
	var $varname;
   
	
	/**
	 * @access public
	 */
	function stream_open( $path, $mode, $options, &$opened_path ) 
	{
		$url = parse_url( $path );
		$this->varname  = $url["host"];
		$this->position = 0;
       
		return true;
	}

	/**
	 * @access public
	 */
	function stream_read( $count ) 
	{
		$ret = substr( $GLOBALS[$this->varname], $this->position, $count );
		$this->position += strlen( $ret );
		
		return $ret;
	}

	/**
	 * @access public
	 */
	function stream_write( $data ) 
	{
		$left  = substr( $GLOBALS[$this->varname], 0, $this->position );
		$right = substr( $GLOBALS[$this->varname], $this->position + strlen( $data ) );
		$GLOBALS[$this->varname] = $left . $data . $right;
		$this->position += strlen( $data );
		
		return strlen( $data );
	}

	/**
	 * @access public
	 */
	function stream_tell() 
	{
		return $this->position;
	}

	/**
	 * @access public
	 */
	function stream_eof() 
	{
		return $this->position >= strlen( $GLOBALS[$this->varname] );
	}

	/**
	 * @access public
	 */
	function stream_seek( $offset, $whence ) 
	{
		switch ( $whence ) 
		{
			case SEEK_SET:
				if ( $offset < strlen( $GLOBALS[$this->varname] ) && $offset >= 0 ) 
				{
					$this->position = $offset;
					return true;
				}
				else
				{
					return false;
				}
				
				break;
               
			case SEEK_CUR:
				if ( $offset >= 0 ) 
				{
					$this->position += $offset;
					return true;
				} 
				else 
				{
					return false;
				}
					
				break;
               
			case SEEK_END:
				if ( strlen( $GLOBALS[$this->varname] ) + $offset >= 0 ) 
				{
					$this->position = strlen( $GLOBALS[$this->varname] ) + $offset;
					return true;
				}
				else 
				{
					return false;
				}
					
				break;
               
			default:
				return false;
		}
	}
} // END OF VariableStream

?>
