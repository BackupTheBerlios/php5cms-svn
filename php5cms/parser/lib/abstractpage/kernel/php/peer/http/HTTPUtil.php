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


using( 'util.Util' );


/**
 * Static utility functions.
 *
 * @package peer_http
 */

class HTTPUtil
{
	/**
	 * Check Referer.
	 *
	 * @param  array referers (e.g. array('yourhost.dom', 'www.yourhost.dom' ))
	 * @access public
	 * @static
	 */
	function checkReferer( $referers = array() )
	{
		if ( $_SERVER["HTTP_REFERER"] != "" )
		{
			while ( list( $val, $ref ) = each( $referers ) )
			{
				if ( preg_match( "/^http:\/\/$ref/", $_SERVER["HTTP_REFERER"] ) )
					return true;
			}
		}
		else
		{
			return false;
		}
	}
	
    /**
     * Format a RFC compliant HTTP header. This function
     * honors the "y2k_compliance" php.ini directive.
     *
     * @param $time int UNIX timestamp
     *
     * @return HTTP date string, or false for an invalid timestamp.
     */
    function date( $time )
	{
        // If we're y2k compliant, use the newer, reccomended RFC 822 format
        if ( ini_get( "y2k_compliance" ) == true )
			return gmdate( "D, d M Y H:i:s \G\M\T", $time );
        // Use RFC-850 which supports two character year numbers
        else
            return gmdate( "F, d-D-y H:i:s \G\M\T", $time );
    }

    /**
     * Negotiate language with the user's browser through the
     * Accept-Language HTTP header or the user's host address.
     * Language codes are generally in the form "ll" for a language
     * spoken in only one country, or "ll-CC" for a language spoken in
     * a particular country.  For example, U.S. English is "en-US",
     * while British English is "en-UK".  Portugese as spoken in
     * Portugal is "pt-PT", while Brazilian Portugese is "pt-BR".
     * Two-letter country codes can be found in the ISO 3166 standard.
     *
     * Quantities in the Accept-Language: header are supported, for
     * example:
     *
     *  Accept-Language: en-UK;q=0.7, en-US;q=0.6, no;q=1.0, dk;q=0.8
     *
     * @param $supported an associative array indexed by language
     * codes (country codes) supported by the application.  Values
     * must evaluate to true.
     *
     * @param $default the default language to use if none is found
     * during negotiation, defaults to "en-US" for U.S. English
     */
    function negotiateLanguage( &$supported, $default = 'en-US' )
	{
        // If the client has sent an Accept-Language: header, see if
        // it contains a language we support.
        if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) )
		{
            $accepted = split( ',[[:space:]]*', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
            
			for ( $i = 0; $i < count( $accepted ); $i++ )
			{
                if ( eregi( '^([a-z]+);[[:space:]]*q=([0-9\.]+)', $accepted[$i], $arr ) )
				{
                    $q = (double)$arr[2];
                    $l = $arr[1];
                }
				else
				{
                    $q = 42;
                    $l = $accepted[$i];
                }

                if ( !empty( $supported[$l] ) && ( $q > 0.0 ) )
				{
                    if ( $q == 42 )
                        return $l;
                    
                    $candidates[$l] = $q;
                }
            }
			
            if ( isset( $candidates ) )
			{
                arsort( $candidates );
                reset( $candidates );
                
				return key( $candidates );
            }
        }

        // Check for a valid language code in the top-level domain of
        // the client's host address.
        if ( isset( $_SERVER['REMOTE_HOST'] ) && ereg( "\.[^\.]+$", $_SERVER['REMOTE_HOST'], $arr ) )
		{
            $lang = strtolower( $arr[1] );
			
            if ( !empty( $supported[$lang] ) )
                return $lang;
        }

        return $default;
    }

    /**
     * Sends a "HEAD" HTTP command to a server and returns the headers
     * as an associative array. Example output could be:
     *    Array
     *    (
     *        [response_code] => 200          // The HTTP response code
     *        [response] => HTTP/1.1 200 OK   // The full HTTP response string
     *        [Date] => Fri, 11 Jan 2002 01:41:44 GMT
     *        [Server] => Apache/1.3.20 (Unix) PHP/4.1.1
     *        [X-Powered-By] => PHP/4.1.1
     *        [Connection] => close
     *        [Content-Type] => text/html
     *    )
     *
     * @param  string $url A valid url, for ex: http://www.docuverse.de/index.php
     * @return mixed Assoc array or Error object on no conection
	 * @static
     */
    function head( $url )
    {
        $purl = parse_url( $url );
        $port = ( isset( $purl['port'] ) )? $purl['port'] : 80;
        $fp   = fsockopen( $purl['host'], $port, $errno, $errstr, 10 );
		
        if ( !$fp )
            return PEAR::raiseError( "head Error $errstr ($erno)" );
        
        $path = ( !empty( $purl['path'] ) )? $purl['path'] : '/';

        fputs( $fp, "HEAD $path HTTP/1.0\r\n" );
        fputs( $fp, "Host: " . $purl['host'] . "\r\n\r\n" );

        $response = rtrim( fgets( $fp, 4096 ) );
		
        if ( preg_match( "|^HTTP/[^\s]*\s(.*?)\s|", $response, $status ) )
            $headers['response_code'] = $status[1];
        
        $headers['response'] = $response;

        while ( $line = fgets( $fp, 4096 ) )
		{
            if ( !trim( $line ) )
                break;
            
            if ( ( $pos = strpos( $line, ':' ) ) !== false )
			{
                $header = substr( $line, 0, $pos );
                $value  = trim( substr( $line, $pos + 1 ) );
                $headers[$header] = $value;
            }
        }
		
        fclose( $fp );
        return $headers;
    }
	
	/**
	 * This functions tries to open a url and decide the mime-type of that url.
	 *
	 * @access public
	 * @static
	 */
	function getMimeType( $url )	
	{
		$pathInfo = parse_url( $url );
		$getAdr   = ( $pathInfo[query] )? $pathInfo[path] . "?" . $pathInfo[query] : $pathInfo[path];
		$fp       = fsockopen( $pathInfo[host], 80, $errno, $errstr );
		
		if ( !$fp ) 
		{
			return false;
		} 
		else 
		{
	        fputs( $fp, "GET " . $getAdr . " HTTP/1.0\n\n" );
			
	        while ( !feof( $fp ) ) 
			{
				$thePortion = fgets( $fp, 128 );
				
				if ( eregi( "(^Content-Type: )(.*)", trim( $thePortion ), $reg ) )	
				{
					$res = trim( $reg[2] );
					break;
				}
	        }
			
	        fclose( $fp );
		}
		
		return $res;
	}
	
    /**
     * This function redirects the client. This is done by issuing
     * a Location: header and exiting.
     *
     * @param  string $url URL where the redirect should go to
	 * @access public
	 * @static
     */
    function redirect( $url )
    {
		if ( !preg_match( '/^(https?|ftp):\/\//', $url ) ) 
		{
            $server = 'http' . ( @$_SERVER['HTTPS'] == 'on'? 's' : '' ) . '://' . $_SERVER['SERVER_NAME'];
            
			if ( $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443 )
                $server .= ':' . $_SERVER['SERVER_PORT'];
			
			$path = dirname( $_SERVER['PHP_SELF'] );
			
            if ( $url{0} != '/' ) 
			{
				$path   .= $url;
                $server .= dirname( $_SERVER['PHP_SELF'] );
                $url     = $server . '/' . preg_replace( '!^\./!', '', $url );
            } 
			else 
			{
                $url = $server . $url;
            }
        }

        header( 'Location: ' . $url );
        exit;
    }
	
    /**
     * Output the contents of the output buffer, compressed if
     * desired, along with any relevant headers.
     *
     * @param  boolean $compress (optional) Use gzip compression, if the browser supports it.
     * @param  boolean $use_etag Generate an ETag, and don't send the body if the browser has the same object cached.
     * @param  boolean $send_body Send the body of the request? Might be false for HEAD requests.
     * @access public
	 * @static
     */
    function output( $compress = true, $use_etag = true, $send_body = true )
    {
		ob_start();
        ob_implicit_flush( 0 );
		
        $min_gz_size = 1024;
        $page = ob_get_contents();
        $length = strlen( $page );
		
        ob_end_clean();
        
        if ( $compress && Util::extensionExists( 'zlib' ) && ( strlen( $page ) > $min_gz_size ) && isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) )
		{
			$ae  = explode( ',', str_replace( ' ', '', $_SERVER['HTTP_ACCEPT_ENCODING'] ) );
            $enc = false;
			
            if ( in_array( 'gzip', $ae ) )
                $enc = 'gzip';
			else if ( in_array( 'x-gzip', $ae ) )
                $enc = 'x-gzip';
            
            if ( $enc )
			{
                $page   = gzencode( $page );
                $length = strlen( $page );
                header( 'Content-Encoding: ' . $enc );
                header( 'Vary: Accept-Encoding' );
            }
			else
			{
                $compress = false;
            }
        }
		else
		{
            $compress = false;
        }
        
        if ( $use_etag )
		{
            $etag = '"' . md5( $page ) . '"';
            header( 'ETag: ' . $etag );
			
			if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) )
			{
                $inm = explode( ',', $_SERVER['HTTP_IF_NONE_MATCH'] );
                foreach ( $inm as $i )
				{
                    if ( trim( $i ) == $etag )
					{
                        header( 'HTTP/1.0 304 Not Modified' );
                        $send_body = false;
						
                        break;
                    }
                }
            }
        }
        
        if ( $send_body )
		{
            header( 'Content-Length: ' . $length );
            echo $page;
        }
    }
} // END OF HTTPUtil

?>
