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
 * @package peer_http_curl
 */
 
class CurlUtil extends PEAR
{
	/**
	 * @access public
	 * @var string
	 */
    var $url;
	
	/**
	 * @access public
	 * @var array
	 */
	var $data;


	/**
	 * Constructor
	 *
	 * @access public
	 */
    function CurlUtil() 
	{
        $this->ch = curl_init();
		
		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $this->ch, CURLOPT_COOKIEJAR,  "cookies.txt" );  // initiates cookie file if needed
		curl_setopt( $this->ch, CURLOPT_COOKIEFILE, "cookies.txt" );
        curl_setopt( $this->ch, CURLOPT_VERBOSE, 1 );
    }


	/**
	 * @access public
	 */
    function reset_cookies() 
	{
        $handle = fopen( "cookies.txt","w" );
		
		if ( !$handle )
			return PEAR::raiseError( "Cannot write cookie data." );
			
        $canwrite = flock( $handle,LOCK_EX + LOCK_NB );
		
        if ( $canwrite )
            ftruncate( $handle, 0 ); 
        
        fclose( $handle );
    }    

	/**
	 * @access public
	 */
    function url( $url ) 
	{
         curl_setopt( $this->ch, CURLOPT_URL, $url );
    }

	/**
	 * @access public
	 */
    function referer( $url ) 
	{
        curl_setopt( $this->ch, CURLOPT_REFERER, $url );
    }

	/**
	 * @access public
	 */
    function data( $data ) 
	{
        // post data
		foreach ( $data as $key => $value )
			$req[] = "$key=" . urlencode( $value );

        $this->postdata = join( "&", $req );
        curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $this->postdata );
    }

	/**
	 * @access public
	 */
    function get() 
	{
        $html = curl_exec( $this->ch );
		curl_close( $this->ch );
		
		return $html;
    }

	/**
	 * @access public
	 */
    function post() 
	{
        curl_setopt( $this->ch, CURLOPT_POST, 1 );
        $html = curl_exec( $this->ch );
        curl_close( $this->ch );
		
        return $html;
    }
} // END OF CurlUtil

?>
