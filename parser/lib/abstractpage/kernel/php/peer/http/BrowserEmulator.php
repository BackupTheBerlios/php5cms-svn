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
 * BrowserEmulator class.
 * Provides methods for opening urls and emulating a web browser request.
 *
 * Usage:
 *
 * $be = new BrowserEmulator(); 
 * $be->addHeaderLine( "Referer", "http://previous.server.com/" ); 
 * $be->addHeaderLine( "Accept-Encoding", "x-compress; x-zip" ); 
 * $be->addPostData( "Submit", "OK" ); 
 * $be->addPostData( "item", "42" ); 
 * $be->setAuth( "admin", "secretpass" ); 
 * // also possible: 
 * // $be->setPort( 10080 ); 
 *
 * $file = $be->fopen( "http://restricted.server.com:10080/somepage.html" ); 
 * $response = $be->getLastResponseHeaders(); 
 *
 * while ( $line = fgets( $file, 1024 ) )
 * {
 *   // do something with the file 
 * }
 *
 * fclose( $file );
 *
 * @package peer_http
 */

class BrowserEmulator extends PEAR
{
	/**
	 * @access public
	 */
	var $port;
		
	/**
	 * @access public
	 */
	var $authUser = "";
	
	/**
	 * @access public
	 */
	var $authPass = "";
	
	/**
	 * @access public
	 */
	var $headerLines = array();
	
	/**
	 * @access public
	 */
	var $postData = array();
	
	/**
	 * @access public
	 */
	var $lastResponse = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function BrowserEmulator() 
	{
		$this->resetHeaderLines();
		$this->resetPort();
	}
	
	
	/**
	 * Adds a single header field to the HTTP request header. The resulting header
	 * line will have the format.
	 *
	 * $name: $value\n
	 *
	 * @access public
	 */
	function addHeaderLine( $name, $value ) 
	{
		$this->headerLines[$name] = $value;
	}
	
	/**
	 * Deletes all custom header lines. This will not remove the User-Agent header field,
	 * which is necessary for correct operation.
	 *
	 * @access public
	 */
	function resetHeaderLines() 
	{
		$this->headerLines = array();
                                                                      
		/* 
		default is "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)",
		which means Internet Explorer 6.0 on WinXP
		*/
		$this->headerLines["User-Agent"] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
	}
	
	/**
	 * Add a post parameter. Post parameters are sent in the body of an HTTP POST request.
	 *
	 * @access public
	 */
	function addPostData( $name, $value ) 
	{
		$this->postData[$name] = $value;
	}
	
	/**
	 * Deletes all custom post parameters.
	 *
	 * @access public
	 */
	function resetPostData() 
	{
		$this->postData = array();
	}
	
	/**
	 * Sets an auth user and password to use for the request.
	 * Set both as empty strings to disable authentication.
	 *
	 * @access public
	 */
	function setAuth( $user, $pass ) 
	{
		$this->authUser = $user;
		$this->authPass = $pass;
	}
	
	/**
	 * Selects a custom port to use for the request.
	 *
	 * @access public
	 */
	function setPort( $portNumber ) 
	{
		$this->port = $portNumber;
	}
	
	/**
	 * Resets the port used for request to the HTTP default (80).
	 *
	 * @access public
	 */
	function resetPort() 
	{
		$this->port = 80;
	}
	
	/**
	 * Make an fopen call to $url with the parameters set by previous member
	 * method calls. Send all set headers, post data and user authentication data.
	 * Returns a file handle on success, or false on failure.
	 *
	 * @access public
	 */
	function fopen( $url ) 
	{
		$this->lastResponse = array();
		
		preg_match( "~([a-z]*://)?([^:^/]*)(:([0-9]{1,5}))?(/.*)?~i", $url, $matches );
		var_dump( $matches );
		
		$protocol = $matches[1];
		$server   = $matches[2];
		$port     = $matches[4];
		$path     = $matches[5];
		
		if ( $port != "" )
			$this->setPort( $port );
		
		if ( $path == "" ) 
			$path = "/";
		
		$socket = false;
		$socket = fsockopen( $server, $this->port );
		
		if ( $socket ) 
		{
			$this->headerLines["Host"] = $server;
			
			if ( ( $this->authUser != "" ) && ( $this->authPass != "" ) ) 
				$headers["Authorization"] = "Basic " . base64_encode( $this->authUser . ":" . $this->authPass );
			
			if ( count( $this->postData ) == 0 ) 
				$request = "GET $path HTTP/1.0\r\n";
			else
				$request = "POST $path HTTP/1.0\r\n";
			
		    fputs( $socket, $request );
			
			if ( count( $this->postData ) > 0 ) 
			{
				$PostStringArray = array();
				
				foreach ( $this->postData as $key => $value )
					$PostStringArray[] = "$key=$value";
				
				$PostString = join( "&", $PostStringArray );
				$this->headerLines["Content-Length"] = strlen( $PostString );
			}
			
			foreach ( $this->headerLines as $key => $value )
			    fputs( $socket, "$key: $value\r\n" );
			
			fputs( $socket, "\r\n" );
			
			if ( count( $this->postData ) > 0 )
				fputs( $socket, $PostString . "\r\n" );
		}

		if ( $socket ) 
		{
			$line = fgets( $socket, 1000 );
			$this->lastResponse[] = $line;
			$status = substr( $line, 9, 3 );
			
			while ( trim( $line = fgets( $socket, 1000 ) ) != "" )
			{
				$this->lastResponse[] = $line;
				
				if ( $status == "401" && strpos( $line, "WWW-Authenticate: Basic realm=\"" ) === 0 ) 
				{
					fclose( $socket );
					return false;
				}
			}
		}
		
		return $socket;
	}
	
	/**
	 * Make an file call to $url with the parameters set by previous member
	 * method calls. Send all set headers, post data and user authentication data.
	 * Returns the requested file as an array on success, or false on failure.
	 *
	 * @access public
	 */
	function file( $url ) 
	{
		$file   = array();
		$socket = $this->fopen( $url );
		
		if ( $socket ) 
		{
			$file = array();
			
			while ( !feof( $socket ) )
				$file[] = fgets( $socket, 10000 );
		} 
		else 
		{
			return false;
		}
		
		return $file;
	}

	/**
	 * @access public
	 */
	function getLastResponseHeaders() 
	{
		return $this->lastResponse;
	}
} // END OF BrowserEmulator

?>
