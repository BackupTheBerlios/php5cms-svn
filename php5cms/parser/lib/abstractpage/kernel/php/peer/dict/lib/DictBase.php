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
 * This set of classes implement a subset of the DICT protocol
 * and it is meant to be use to generate clients that
 * query a DICT server
 *
 * By default, the class uses the dict.org server in
 * the port 2628.
 *
 *
 * Class DictBase
 *
 * Base class for implementing the DICT protocol to communicate
 * with dictionary servers. It defaults to dict.org and port 2628.
 *
 * @package peer_dict_lib
 */
 
class DictBase extends PEAR
{
	/**
	 * @access public
	 */
	var $host;
	
	/**
	 * @access public
	 */
	var $port;
	
	/**
	 * @access public
	 */
	var $socket;
	
	/**
	 * 1024 * 6 to cover UTC8 chars
	 * @access public
	 */
	var $max_length = 6144;
	
	/**
	 * @access public
	 */
	var $return_code = array();
	
	/**
	 * @access public
	 */
	var $valid_codes = array( 
		110, 111, 112, 113, 114, 130, 150, 151, 152, 
		210, 220, 221, 230, 250,
		330,
		420, 421,
		500, 501, 502, 503, 530, 531, 532,
		550, 551, 552, 554, 555
	);


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function DictBase( $host = 'dict.org', $port = 2628 )
	{
		$this->host = $host;
		$this->port = $port;
	}
	
	
	/**
	 * @access public
	 */
	function set( $var, $val )
	{
		$this->$var = $val;
	}

	/**
	 * @access public
	 */
	function get( $var )
	{
		return $this->$var;
	}

	/**
	 * @access public
	 */
	function parse_code( $str )
	{
		ereg( "^([0-9]{3}) (.+)", $str, &$reg );
		$error = ( $reg[1] >= 300 );
		
		$this->return_code = array(
			"error" => $error, 
			"code"  => $reg[1], 
			"desc"  => $reg[2]
		);
	}

	/**
	 * @access public
	 */
	function is_valid_code()
	{
		return in_array( $this->return_code["code"], $this->valid_codes );
	}

	/**
	 * @access public
	 */
	function is_error_code()
	{
		return $this->return_code["error"];	
	}

	/**
	 * @access public
	 */	
	function print_code()
	{
		$out  = $this->is_error_code()? "<ERROR> " : "";
		$out .= "[" . $this->return_code["code"] . "] " . $this->return_code["desc"] . "\n";
		
		echo( $out );
	}

	/**
	 * @access public
	 */
	function connect()
	{
		$fp = fsockopen( $this->host, $this->port, &$errno, &$errstr, 90 );
		
		if ( !$fp )
			return PEAR::raiseError( "Cannot connect: " . $errno . " = " . $errstr );
		else
			$this->socket = $fp;
			
		return true;
	}

	/**
	 * @access public
	 */
	function close()
	{
		fputs( $this->socket, "QUIT\r\n" );
		$tmp = fgets( $this->socket, $this->max_length );
		fclose( $this->socket );
	}

	/**
	 * @access public
	 */
	function read_data()
	{
		while ( $read = fgets( $this->socket, $this->max_length ) )
		{
			if ( ereg( "^\.\r\n$", $read ) )
				break;
			
			$out .= $read;
		}
		
		return $out;
	}
} // END OF DictBase

?>
