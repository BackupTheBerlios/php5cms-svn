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
 * apache_lookup_uri wrapper 
 * 
 * If apache_lookup_uri doesn't exists, it tries to simulate it.
 *
 * @package org_apache
 */ 

class Apache_LookupUri
{ 
  	/** 
   	 * Constructor
   	 * 
   	 * @param  string object for lookup 
   	 * @return object identical to apache_lookup_uri
   	 * @access public
   	 */ 
  	function Apache_LookupUri( $uri ) 
  	{ 
    	if ( function_exists( "apache_lookup_uri" ) && !defined( "APACHE_LOOKUPURI_WRAPPER" ) ) 
		{ 
      		$l = apache_lookup_uri( $uri ); 
      
	  		foreach ( $l as $k => $v ) 
        		$this->$k = $v;
    	} 
		else 
		{ 
      		$this->method       = "GET"; 
      		$this->request_time = time(); 
      		$this->unparsed_uri = $uri; 
      		$this->uri          = preg_replace( "/\?.*/", "", $uri ); 
      		$this->the_request  = "GET " . $uri . " HTTP/1.1";
      		
			$args = stristr( $uri, "?" ); 
      		$this->args = substr( $args, 1, strlen( $args ) );
      
      		$res = $this->_parseURI( $uri ); 
     	 	$this->filename     = $res['fname']; 
      		$this->path_info    = $res['path']; 
      		$this->content_type = $res['type']; 
      		$this->status       = ( file_exists( $this->filename )? "200" : "404" ); 
    	} 
  	} 
  

	// private methods
	
  	/** 
   	 * Parses URI.
   	 * 
   	 * @param  string URI to parse 
   	 * @return array
	 * @access private
   	 */ 
  	function _parseURI ( $uri ) 
	{
	  	// When folder with specified name doesn't exists, but we can find file
  		// with the same name there, we can be sure that MultiViews is used and
  		// that file is request handler.
  		// Thus we will return it's name as handler.
  		// 
  		// Only one requirement exists - filename (without extension) should be 
  		// unique, otherwise first found file will be returned.
    	// 
    	// We can detect MultiViews by special REDIRECT_URL value
    	// where will be not the real script filename, but URL
    	$dr = $_SERVER['DOCUMENT_ROOT']; 
    	$d  = preg_replace( "/\?.*/", "", $uri ); 
    
		if ( file_exists( $dr . $d ) ) 
		{ 
      		$res[0] = $dr . $d; 
      		$p = array( "" ); 
    	} 
		else 
		{ 
      		$rdr = explode( "/", $d ); 
      
	  		while ( $rdr ) 
			{ 
        		$res = glob( $dr . implode( "/", $rdr ) . ".*" ); 
        		
				if ( isset( $res[0] ) ) 
					break; 
        		
				$p[] = $rdr[sizeof( $rdr ) - 1]; 
        		unset( $rdr[sizeof( $rdr ) - 1] ); 
      		} 
      		
			if ( !$res ) 
			{ 
        		$res[0] = $dr . $d; 
        		$p = array(); 
      		} 
    	} 
    	
		krsort( $p ); 
    
		$r['fname'] = $res[0]; 
    	$r['path']  = $p? "/" . implode( "/", $p ) : ""; 
    	$r['type']  = $this->_getType( $r['fname'] ); 
    	
		return $r; 
  	}
    
  	/** 
   	 * Small wrapper for mime_content_type, very simple.
   	 * 
   	 * @var     string thing to be typed 
   	 * @return  string mime-type 
	 * @access  private
   	 */ 
  	function _getType( $path ) 
	{
    	if ( is_dir( $path ) ) 
			return "httpd/unix-directory"; 
    	
		if ( function_exists( "mime_content_type" ) )
		{
      		return mime_content_type( $path ); 
		}
    	else 
		{
      		$d = pathinfo( $path ); 
      		
			switch ( $d['extension'] )
			{ 
        		case "html": 
        		
				case "htm": 
					return "text/html"; 
        		
				case "php": 
        		
				case "php3": 
        		
				case "phtml": 
        		
				case "phtm": 
					return "application/x-httpd-php"; 
        		
				default: 
					return "text/plain"; 
      		} 
    	} 
  	} 
} // END OF Apache_LookupUri


if ( !function_exists( "apache_lookup_uri" ) && !defined( "APACHE_LOOKUPURI_WRAPPER" ) )
{ 
  	define( "APACHE_LOOKUPURI_WRAPPER", true ); 
  
  	function apache_lookup_uri( $uri ) 
	{ 
		return new Apache_LookupUri( $uri ); 
	}  
}

?>
