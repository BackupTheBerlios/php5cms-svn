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
 * @package peer_soap_lib
 */
 
class SoapClient extends PEAR
{
	/**
	 * @access public
	 */
	var $path;
	
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
	var $errno;
	
	/**
	 * @access public
	 */
	var $errstring;

	/**
	 * @access public
	 */	
	var $debug = 0;
	
	/**
	 * @access public
	 */
	var $username = "";
	
	/**
	 * @access public
	 */
	var $password = "";

  
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SoapClient( $host, $port = 80, $user = '', $password = '', $path = '' )
	{
		$this->port		  = $port;
		$this->server	  = $host;
		$this->path		  = $path;
		$this->user		  = $user;
		$this->password	  = $password;
		
		$this->masquerade = ap_ini_get( "agent_name", "settings" );
  	}


	/**
	 * @access public
	 */
	function setDebug( $in )
	{
		if ( $in )
			$this->debug = 1;
		else
			$this->debug = 0;
  	}

	/**
	 * @access public
	 */
	function setCredentials( $u, $p )
	{
		$this->username = $u;
		$this->password = $p;
	}

	/**
	 * @access public
	 */
  	function send( $msg, $timeout = 0 )
	{
		// where msg is an soapmsg
		$msg->debug = $this->debug;
		
		return $this->sendPayloadHTTP10( $msg, $this->server, $this->port, $timeout, $this->username, $this->password );
	}

	/**
	 * @access public
	 */
	function sendPayloadHTTP10( $msg, $server, $port, $timeout = 0, $username = "", $password = "" )
	{
		if ( $timeout > 0 )
			$fp = fsockopen( $server, $port, &$this->errno, &$this->errstr, $timeout );
		else
			$fp = fsockopen( $server, $port, &$this->errno, &$this->errstr );
		
		if ( !$fp )   
			return false;
		
		// only create the payload if it was not created previously
		if ( empty( $msg->payload ) )
			$msg->createPayload();
		
		$credentials = "";

		if ( $username != "" )
		{
			$credentials = "Authorization: Basic " .
			base64_encode( $username . ":" . $password ) . "\r\n";
		}

		$op = "POST " . $this->path. " HTTP/1.0\r\nUser-Agent: " . $this->masquerade . "\r\n" .
			"Host: ". $this->server  . "\r\n" .
			$credentials . 
			"Content-Type: text/xml\r\nContent-Length: " .
			strlen( $msg->payload ) . "\r\n\r\n" .
			$msg->payload;
		
		if ( !fputs( $fp, $op, strlen( $op ) ) )
		{
			$this->errstr = "Write error";
			return false;
		}
		
		$resp = $msg->parseResponseFile( $fp );
		fclose( $fp );
		
		return $resp;
	}
} // END OF SoapClient

?>
