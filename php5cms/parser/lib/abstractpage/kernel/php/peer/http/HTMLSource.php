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
 * @package peer_http
 */
 
class HTMLSource extends PEAR
{
	/**
	 * @access public
	 */
	var $host;
	
	/**
	 * @access public
	 */
	var $page;
	
	/**
	 * @access public
	 */
	var $request;
	
	/**
	 * @access public
	 */
	var $httpversion;
	
	/**
	 * @access public
	 */
	var $striptags;
	
	/**
	 * @access public
	 */
	var $showsource;
	
	/**
	 * @access public
	 */
	var $port;
	
	/**
	 * @access public
	 */
	var $timeout;
	
	/**
	 * @access public
	 */
	var $method;
	
	/**
	 * @access public
	 */
	var $cookies = array();
	
	/**
	 * @access public
	 */
	var $getvars = array();
	
	/**
	 * @access public
	 */
	var $postvars = array();
	
	/**
	 * @access public
	 */
	var $strip_responseheader = true;
	
	
	/* Request fields */
	
	/**
	 * format: Accept: */*
	 * @access public
	 */
	var $accept;
	
	/**
	 * format: gzip,deflate
	 * @access public
	 */
	var $accept_encoding;
	
	/**
	 * format: en-gb
	 * @access public
	 */
	var $accept_language;
	
	/**
	 * format: username:password
	 * @access public
	 */
	var $authorization;
	
	/**
	 * format: 40 (for POST)
	 * @access public
	 */
	var $content_length;
	
	/**
	 * format: application/x-www-form-urlencoded
	 * @access public
	 */
	var $content_type;
	
	/**
	 * format: Date: Tue, 15 Nov 1994 08:12:31 GMT
	 * @access public
	 */
	var $date;
	
	/**
	 * format: Referer: http://www.domain.com
	 * @access public
	 */
	var $referer;
	
	/**
	 * format: User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)
	 * @access public
	 */
	var $useragent;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function HTMLSource()
	{
		$this->useragent   = ap_ini_get( "agent_name", "settings" );

		$this->port        = 80;		
		$this->method      = "GET";
		$this->httpversion = "HTTP/1.0";
		$this->timeout     = 30;
	}
	
	
	/**
	 * @access public
	 */
	function addPostVar( $name, $value )
	{
		if ( !empty( $name ) && !empty( $value ) )
			$this->postvars[] = $name . "=" . $value;
	}

	/**
	 * @access public
	 */	
	function addGetVar( $name, $value )
	{
		if ( !empty( $name ) && !empty( $value ) )
			$this->getvars[] = $name . "=" . $value;
	}

	/**
	 * @access public
	 */	
	function addCookie( $name, $value )
	{
		if ( !empty( $name ) && !empty( $value ) )
			$this->cookies[] = $name . "=" . $value;
	}

	/**
	 * @access public
	 */	
	function getSource()
	{
		// Error check
		if ( empty( $this->httpversion ) )
			$this->httpversion = "1.0";
		
		if ( empty( $this->method ) )
			$this->method = "GET";
		
		// Make GET variables
		$vars       = "";
		$cookiehead = "";
		
		if ( sizeof( $this->getvars ) >0 && $this->method == "GET" )
		{
			$vars  = "?";
			$vars .= join( $this->getvars, "&" );
			
			// Knock last '&' off
			// Remove this..?
			
			if ( sizeof( $this->getvars ) > 1 )
				$vars = substr( $vars, 0, strlen( $vars ) -1 );
		}
		
		// Make POST variables
		if ( sizeof( $this->postvars ) >0 && $this->method == "POST" )
		{
			$vars       = "\r\n";
			$strpostvar = join( $this->postvars, "&" );
			$vars      .= $strpostvar;
			$vars      .= "\r\n";
		}
		
		// Make Cookies
		if ( sizeof( $this->cookies ) > 0 )
		{
			$cookiehead  = "Cookie: ";
			$cookiehead .= join( $this->cookies, "; " );
			$cookiehead .= "\r\n";
		}
		
		// Make up request. Host isn't strictly needed except IIS winges
		if ( $this->method == "POST" )
		{
			$this->content_length = strlen( $strpostvar );
			$this->content_type   = "application/x-www-form-urlencoded";
			
			$this->request  = $this->method . " " . $this->page . " HTTP/" . $this->httpversion . "\r\n";
			$this->request .= "Host: " . $this->host . "\r\n";
			$this->request .= $cookiehead;
			$this->request .= $this->_makeRequest();
			$this->request .= $vars . "\r\n";
		}
		else
		{
			$this->request  = $this->method . " " . $this->page . $vars . " HTTP/" . $this->httpversion . "\r\n";
			$this->request .= "Host: " . $this->host . "\r\n";
			$this->request .= $cookiehead;
			$this->request .= $this->_makeRequest();
			$this->request .= "\r\n";
		}

		// Open socket to URL
		$sHnd = fsockopen( $this->host, $this->port, $errno, $errstr, $this->timeout );
		fputs( $sHnd, $this->request );
		
		// Get source
		while ( !feof( $sHnd ) )
			$result .= fgets( $sHnd, 128 );
		
		// Strip header
		if ( $this->strip_responseheader )
			$result = $this->_stripResponseHeader( $result );
		
		// Strip tags
		if ( $this->striptags )
			$result = strip_tags( $result );
		
		// Show the source only
		if ( $this->showsource && !$this->striptags )
		{
			$result = htmlentities( $result );
			$result = nl2br( $result );
		}
		
		return $result;
	}


	// private methods
	
	/**
	 * Make up headers.
	 *
	 * @access private
	 */
	function _makeRequest()
	{
		if ( !empty( $this->accept ) )
			$result .= "Accept: " . $this->accept . "\r\n";
		
		if ( !empty( $this->accept_encoding ) )
			$result .= "Accept-Encoding: " . $this->accept_encoding . "\r\n";

		if ( !empty( $this->accept_language ) )
			$result .= "Accept-Language: " . $this->accept_language . "\r\n";

		if ( !empty( $this->authorization ) )
			$result .= "Authorization: Basic " . base64_encode( $this->authorization ) . "\r\n";

		if ( !empty( $this->content_length ) )
			$result .= "Content-length: " . $this->content_length . "\r\n";

		if ( !empty( $this->content_type ) )
			$result .= "Content-type: " . $this->content_type . "\r\n";

		if ( !empty( $this->date ) )
			$result .= "Date: " . $this->date . "\r\n";

		if ( !empty( $this->referer ) )
			$result .= "Referer: " . $this->referer . "\r\n";

		if ( !empty( $this->useragent ) )
			$result .= "User-Agent: " . $this->useragent . "\r\n";
		
		return $result;
	}
	
	/**
	 * @access private
	 */
	function _stripResponseHeader( $source )
	{
		$headerend = strpos( $source, "\r\n\r\n" );
		
		if ( is_bool( $headerend ) )
			$result = $source;
		else
			$result = substr( $source, $headerend + 4, strlen( $source ) - ( $headerend + 4 ) );
		
		return $result;
	}
} // END OF HTMLSource

?>
