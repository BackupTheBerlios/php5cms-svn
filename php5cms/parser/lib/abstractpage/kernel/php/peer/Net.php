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


using( 'util.Debug' );


/**
 * @package peer
 */
 
class Net extends PEAR
{
	/**
	 * @access public
	 */
	var $server;
	
	/**
	 * @access public
	 */
	var $port;

	/**
	 * @access public
	 */
	var $persistent;
	
	/**
	 * @access public
	 */
	var $connnected;
	
	/**
	 * @access public
	 */
	var $connection_handle;

	/**
	 * @access public
	 */
	var $debug;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Net()
	{
		$this->server    = '';
		$this->port      = '';
		$this->connected = 0;
		
		$this->connection_handle = undef;
		$this->persistent = 0;

		$this->debug = new Debug();
		$this->debug->Off();
	}


	/**
	 * @access public
	 */	
	function Open()
	{
		return $this->Connect();
	}

	/**
	 * @access public
	 */
	function Connect()
	{
		if ( $this->persistent == 1 )
		{
			$this->debug->Message( "Opening persistent connection." );
			$this->connection_handle = pfsockopen( $this->server, $this->port, $error_number, $error_string );
		}
		else
		{
			$this->debug->Message( "Opening non-persistent connection." );
			$this->connection_handle = fsockopen( $this->server, $this->port, $error_number, $error_string );
		}

		if ( ! $this->connection_handle )
		{
			$this->debug->Message( "Could not connect: " . $error_number . ' : ' . $error_string );
			return array( false, "Could not connect: " .  $error_number, ' - ' . $error_string );
		}

		$this->debug->Message( "Connected." );
		$this->connected = true;
	}

	/**
	 * @access public
	 */
	function Close()
	{
		return $this->Disconnect();
	}

	/**
	 * @access public
	 */	
	function Disconnect()
	{
		if ( $this->persistent == 1 )
			return true;
			
		return fclose( $this->connection_handle );
	}

	/**
	 * @access public
	 */
	function ReadLine( $buffer_len = 2048 )
	{
		if ( $this->connected == true )
		{
			$line = fgets( $this->connection_handle, $buffer_len );
			$this->debug->Message( "Read line: " . $line );

			return $line;
		}
	}

	/**
	 * @access public
	 */
	function SendLine( $line )
	{
		if ( $this->connected == true )
		{
			$this->debug->Message( "Sending line: " . $line );
			return fputs( $this->connection_handle, $line );
		}
		
		return false;
	}
} // END OF Net

?>
