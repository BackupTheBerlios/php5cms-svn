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
 * Reads a UNIX telnet tracefile's termdata which is saved in hex
 * and output it ASCII readable format. 
 *
 * .telnetrc file should look like this
 *
 * DEFAULT set tracefile <dir>/<filename> 
 * DEFAULT set termdata
 *
 *
 * Usage: 
 * 
 * $text = new TelnetLog( ("/tmp/telnet.log" );
 * echo $text->get();
 * echo $text->get_html();
 *
 * @package peer_telnet
 */

class TelnetLog extends PEAR
{
	/**
	 * @access public
	 */
	var $text;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function TelnetLog( $file )
	{
		if ( $this->_open_file( $fp, $file ) )
		{
			$this->_transfile( $fp, $this->text );
			$this->_close_file( $fp );
		}
	}
	

	/**
	 * @access public
	 */	
	function get()
	{	
		return $this->text;
	}

	/**
	 * @access public
	 */	
	function get_html()
	{
		 return "\n<pre>" . htmlspecialchars( $this->text ) . "</pre>\n";
	}

	
	// private methods

	/**
	 * @access private
	 */	
	function _transfile( &$fp, &$text )
	{	
		while ( $this->_getline( $fp, $input ) )
		{
			if ( $this->_is_output_line( $input ) )
				$text .= $this->_transdata( $input );
		}
	}
	
	/**
	 * @access private
	 */	
	function _open_file( &$fp, $file )
	{
		$status = true;

		if ( is_file( $file ) && filesize( $file ) > 0 )
			$fp = fopen ( $file, "r" );
		else
			$status = false;
		
		if ( $fp && $status )
			return true;
		else
			return false;
	}

	/**
	 * @access private
	 */		
	function _close_file( &$fp )
	{
		fclose ( $fp );
	}
	
	/**
	 * @access private
	 */	
	function _transdata( $input )
	{
		$output = "";
		$input  = $this->_getdata( $input );
		
		for ( $i = 0; $i < strlen( $input ); $i += 2 )
			$output .= $this->_getchar( substr($input, $i, 2 ) );
		
		return $output;
	}		

	/**
	 * @access private
	 */	
	function _getchar( $char )
	{
		return  chr( hexdec( $char ) );
	}
	
	/**
	 * @access private
	 */	
	function _getline( &$fp, &$input )
	{
		if ( ! feof( $fp ) )
		{
			$input = fgets( $fp, 4096 );
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @access private
	 */	
	function _getdata( $line )
	{
		 $array = preg_split( "/\s+/", $line );
		 return $array[2];
	}
	
	/**
	 * @access private
	 */	
	function _is_output_line( $line )
	{
		if ( preg_match ( "/^>/", $line ) )
			return true;
		else
			return false;
	}
} // END OF TelnetLog

?>
