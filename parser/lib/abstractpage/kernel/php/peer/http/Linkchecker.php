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
 
class Linkchecker extends PEAR
{
	/**
	 * @access public
	 */
	var $codes = array(
		"N/A"  => "Ikke HTTP",
		"OK"   => "Valid hostname",
		"Fail" => "Invalid hostname",
		"Down" => "No response",
		"100"  => "Continue",
		"101"  => "Switching Protocols",
		"200"  => "OK",
		"201"  => "Created",
		"202"  => "Accepted",
		"203"  => "Non-Authoritative Information",
		"204"  => "No Content",
		"205"  => "Reset Content",
		"206"  => "Partial Content",
		"300"  => "Multiple Choices",
		"301"  => "Moved Permanently",
		"302"  => "Found",
		"303"  => "See Other",
		"304"  => "Not Modified",
		"305"  => "Use Proxy",
		"307"  => "Temporary Redirect",
		"400"  => "Bad Request",
		"401"  => "Unauthorized",
		"402"  => "Payment Required",
		"403"  => "Forbidden",
		"404"  => "Not Found",
		"405"  => "Method Not Allowed",
		"406"  => "Not Acceptable",
		"407"  => "Proxy Authentication Required",
		"408"  => "Request Timeout",
		"409"  => "Conflict",
		"410"  => "Gone",
		"411"  => "Length Required",
		"412"  => "Precondition Failed",
		"413"  => "Request Entity Too Large",
		"414"  => "Request-URI Too Long",
		"415"  => "Unsupported Media Type",
		"416"  => "Requested Range Not Satisfiable",
		"417"  => "Expectation Failed",
		"500"  => "Internal Server Error",
		"501"  => "Not Implemented",
		"502"  => "Bad Gateway",
		"503"  => "Service Unavailable",
		"504"  => "Gateway Timeout",
		"505"  => "HTTP Version Not Supported"	
	);
	
	
	/**
	 * @access public
	 */
	function specialconcat( $base,$path )
	{
		$base = ereg_replace( "(.*/)[^/]*","\\1", $base );
		$path = ereg_replace( "^(\.){1}/", "",    $path );
	
		if ( ereg( "^/", $path ) )
	   		$base = ereg_replace( "^(http://([^/]+))/{1}(.*)", "\\1", $base );
	
		return $base . $path;
	}

	/**
	 * @access public
	 */
	function sortarray( $arr )
	{
		if ( count( $arr ) == 0 )
   			return $arr;
   
		reset( $arr );
   
   		while ( list( $key, $value ) = each( $arr ) )
   			$newarr[$value] = $key;
   
   		reset( $newarr );
   
   		while ( list( $key, $value ) = each( $newarr ) )
   			$sortedarr[] = $key;
   
   		return $sortedarr;
	}

	/**
	 * @access public
	 */
	function firstArd( $url )
	{
		$urlArray = parse_url( $url );
   
		if ( !$urlArray[port] )
   			$urlArray[port] = "80";
   
   		if ( !$urlArray[path] )
   			$urlArray[path] = "/";
   
   		if ( $urlArray[query] )
			$urlArray[path] .= "?$urlArray[query]";
   
   		$sock = fsockopen( $urlArray[host], $urlArray[port] );
   
   		if ( $sock )
		{
      		$dump .= "GET $urlArray[path] HTTP/1.1\r\n";
      		$dump .= "Host: $urlArray[host]\r\nConnection: close\r\n";
      		$dump .= "Connection: close\r\n\r\n";
      
	  		fputs( $sock, $dump );
	   
	   		while ( $str = fgets( $sock, 1024 ) )
				$headers[] = $str;
	   
			fclose( $sock );
      		flush();
	   
	   		for ( $i = 0; $i < count( $headers ); $i++ )
			{
         		if ( eregi( "^HTTP/[0-9]+\.[0-9]+ 200", $headers[$i] ) )
					$location = $url;
         
		 		if ( eregi( "^Location: ", $headers[$i] ) )
					$location = eregi_replace( "^Location:( )?", "", $headers[$i] );
	   		}
		}
   
   		$location = trim( $location );
   		return $location;
	}

	/**
	 * @access public
	 */
	function check( $url )
	{
		if ( !eregi( "^http://", $url ) )
		{
			if ( eregi( "^mailto:", $url ) )
			{
				$url = trim( eregi_replace( "^mailto:(.+)", "\\1", $url ) );
				list( $brugernavn, $host ) = split( "@", $url );
				$dnsCheck = checkdnsrr( $host, "MX" );
		 
		 		if ( $dnsCheck )
					$return[code] = "OK";
		 		else
					$return[code] = "ERROR";
	  		}
      		else
			{
				$return[code] = "N/A";
			}
   		}
   		else
		{
      		$urlArray = parse_url( $url );
     
	 		if ( !$urlArray[port] )
				$urlArray[port] = "80";
      
	  		if ( !$urlArray[path] )
				$urlArray[path] = "/";
        
			$sock = fsockopen( $urlArray[host], $urlArray[port], &$errnum, &$errstr );
       
	   		if ( !$sock )
				$return[code] = "Down";
      		else
			{
            	$dump .= "GET $urlArray[path] HTTP/1.1\r\n";
            	$dump .= "Host: $urlArray[host]\r\nConnection: close\r\n";
            	$dump .= "Connection: close\r\n\r\n";
            
				fputs( $sock, $dump );
	        
				while ( $str = fgets( $sock, 1024 ) )
				{
	        		if ( eregi( "^http/[0-9]+.[0-9]+ ([0-9]{3}) [a-z ]*", $str ) )
						$return[code] = trim( eregi_replace( "^http/[0-9]+.[0-9]+ ([0-9]{3}) [a-z ]*", "\\1", $str ) );
		       
			   		if ( eregi( "^Content-Type: ", $str ) )
						$return[contentType] = trim( eregi_replace( "^Content-Type: ", "", $str ) );
	     		}
	        
				fclose( $sock );
            	flush();
    		}
		}
	
		return $return;
	}

	/**
	 * @access public
	 */
	function getText( $which )
	{
		return $this->codes( $which );
	}
	
	/**
	 * @access public
	 */
	function liste( $url )
	{
		global $Comments;
		global $otherLinks;
		global $removeQ;

		$text = implode( "", file( $url ) );
		$text = eregi_replace( "<!--([^-]|-[^-]|--[^>])*-->", "", $text );
   
   		while ( eregi( "[:space:]*(href|src)[:space:]*=[:space:]*([^ >]+)", $text, $regs ) )
		{
			$regs[2] = ereg_replace( "\"", "", $regs[2] );
			$regs[2] = ereg_replace( "'", "",  $regs[2] );
      
	  		if ( $removeQ )
				$mylist[] = ereg_replace( "\?.*$", "", $regs[2] );
      		else
				$mylist[] = ereg_replace( "#.*$", "", $regs[2] );
      
	  		$text = substr( $text, strpos( $text, $regs[1] ) + strlen( $regs[1] ) );
		}

		$mylist = $this->sortarray( $mylist );
	
		for ( $i = 0; $i < count( $mylist ); $i++ )
		{
      		$temp = "";
      
	  		if ( !eregi( "^(mailto|news|javascript|ftp)+:(//)?", $mylist[$i] ) )
			{
       			if ( !eregi( "^http://", $mylist[$i] ) )
					$temp = $this->specialconcat( $url, $mylist[$i] );
		 		else
					$temp = $mylist[$i];
    		}
	  		else
			{
	  			if ( $otherLinks )
					$temp = $mylist[$i];
	  		}
	  
	  		if ( $temp && $temp != $url )
				$return[] = $temp;
 		}

		if ( count( $return ) != 0 )
			return $return;
		else
			return false;
	}
} // END OF Linkchecker

?>
