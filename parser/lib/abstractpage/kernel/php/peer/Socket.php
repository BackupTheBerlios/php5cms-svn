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
 * Simple client socket implementation.
 *
 * @package peer
 */
 
class Socket extends PEAR
{
	/**
	 * @access private
	 */
	var $sock;

	/**
	 * @access private
	 */
	var $port;

	/**
	 * @access private
	 */
	var $host;

	/**
	 * @access private
	 */
	var $timeout;

	/**
	 * @access private
	 */
	var $errno;

	/**
	 * @access private
	 */
	var $errstr;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Socket() 
	{
		$this->setTimeout( 30 );
	}

	
	/**
	 * Specify the host to connect to.
	 *
	 * @access public
	 */
	function setHost( $host ) 
	{
		$this->host = $host;
	}

	/**
	 * Specify the port to connect to.
	 *
	 * @access public
	 */
	function setPort( $port ) 
	{
		$this->port = $port;
	}

	/**
	 * Specify the timeout for connection attempts.
	 *
	 * @access public
	 */
	function setTimeout( $timeout ) 
	{
		$this->timeout = $timeout;
	}

	/**
	 * Open the connection.
	 *
	 * @access public
	 */
	function open() 
	{
		$this->sock = fsockopen( $this->host, $this->port, &$this->errno, &$this->errstr, $this->timeout );
		
		if ( !$this->sock )
			return false;
		else
			return true;
	}

	/**
	 * Get the error message from failed connection.
	 *
	 * @access public
	 */
	function getError() 
	{
		return $this->errstr;
	}

	/**
	 * Specify that the socket should block for input.
	 *
	 * @access public
	 */
	function setBlocking( $block ) 
	{
		socket_set_blocking( $this->sock, ( $block? 1 : 0 ) );
	}

	/**
	 * Write data to the host.
	 *
	 * @access public
	 */
	function write( $data, $length = null ) 
	{
		if ( $length === NULL )
			return fwrite( $this->sock, $data );
		else
			return fwrite( $this->sock, $data, $length );
	}

	/**
	 * Read data from the host.
	 *
	 * @access public
	 */
	function read( $length ) 
	{
		return fread( $this->sock, $length );
	}

	/**
	 * Read a line of data from the host.
	 *
	 * @access public
	 */
	function readLine( $length = 1024 ) 
	{
		return fgets( $this->sock, $length );
	}

	/**
	 * Determine whether the socket has been closed.
	 *
	 * @access public
	 */
	function eof() 
	{
		return feof( $this->sock );
	}

	/**
	 * Close the socket.
	 *
	 * @access public
	 */
	function close() 
	{
		fclose( $this->sock );
	}
} // END OF Socket

?>
