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
 * Manual Session-Fallback.
 *
 * @package peer_http_session
 */
 
class GenericSession extends Base
{
	/**
	 * Client uses Cookies
	 * @access public
	 */
    public $usesCookies = false;
	
	/**
	 * compiled with --enable-trans-sid
	 * @access public
	 */
    public $transSID = false;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */   
    public function __construct( $session_name = "PHPSESSID" )
	{
        $this->sendNoCacheHeader();
		
		// set session name and init session 
        session_name( $session_name );
        @session_start();
        
		// Check if sessionid is 32 digits long. If not, set a new session id.
        if ( strlen( session_id() ) != 32 )
      	{
			mt_srand( (double)microtime() * 1000000 );
			session_id( md5( uniqid( mt_rand() ) ) );
		}
        
		// session id has been sent (Cookie, POST or GET)
        $IDpassed = false;

        if ( isset( $_COOKIE[session_name()] ) && @strlen( $_COOKIE[session_name()]) == 32 )
			$IDpassed = true;

        if ( isset( $_POST[session_name()] ) && @strlen( $_POST[session_name()] ) == 32 )
			$IDpassed = true;

        if ( isset( $_GET[session_name()] )  && @strlen( $_GET[session_name()] ) == 32 )
			$IDpassed = true;
        
        if ( !$IDpassed )  
     	{   
			// No valid session id has been sent. So we build a new url here.                
         	$query = ( @$_SERVER["QUERY_STRING"] != "" )? "?" . $_SERVER["QUERY_STRING"] : "";
         	header( "Status: 302 Found" );
                
        	// terminate script
          	$this->redirectTo( $_SERVER["PHP_SELF"] . $query );
      	}
           
		// given id must not be valid
        
        // memorize    
        $this->usesCookies = ( isset( $_COOKIE[session_name()] ) && @strlen( $_COOKIE[session_name()] ) == 32 );
	}    
 
 	/**
	 * Prevent from caching.
	 *
	 * override "session.cache_limiter = nocache"
	 * @access public
	 */
    public function sendNoCacheHeader()
	{        
        header( "Expires: Sat, 05 Aug 2000 22:27:00 GMT" );
        header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
        header( "Cache-Control: no-cache, must-revalidate" );
        header( "Pragma: no-cache" );
        header( "Cache-Control: post-check=0, pre-check=0" );        
    }

	/**
	 * Execute HTTP-Redirect
	 * Function supports non-standard ports and SSL.
	 * If needed, get-Parameter will be added to the url.
	 * Control will be passed to Target.
	 *
	 * @param  string  target (z.B. "index.php")
	 * @access public
	 */
    public function redirectTo( $pathInfo )
	{
    	// path is relative    
        if ( $pathInfo[0] != "/" )
			$pathInfo = substr( getenv( "SCRIPT_NAME" ), 0, strrpos( getenv( "SCRIPT_NAME" ), "/" ) + 1 ) . $pathInfo;

		// are we runing on a non-standard port?
        $port = !preg_match( "/^(80|443)$/", $_SERVER['SERVER_PORT'], $portMatch )? ":" . $_SERVER['SERVER_PORT'] : "";
        
        // redirect    
        header( "Location: " . ( ( $portMatch[1] == 443 )? "https://" : "http://" ) . $_SERVER["SERVER_NAME"] . $port . $this->url( $pathInfo ) );
        exit;
    }

	/**
	 * Remove "&" and "?".
	 *
	 * @param  string  String
	 * @return string  String ohne abschlieﬂende "&" und "?"
	 * @access public
	 */
    public function removeTrail( $pathInfo )
	{
        $dummy = preg_match( "/(.*)(?<!&|\?)/", $pathInfo, $match );
        return $match[0];  
    }

	/**
	 * Fallback via GET (if Cookies are turned off).
	 *
	 * @param  string  Ziel-Datei
	 * @return string  Ziel-Datei mit - bei Bedarf - angeh‰ngter Session-ID
	 * @access public
	 */
    public function url( $pathInfo )
	{        
        if ( $this->usesCookies || $this->transSID )
			return $pathInfo;

        // extract Anchor-Fragment
        $dummyArray = split( "#", $pathInfo );
        $pathInfo   = $dummyArray[0];

		// remove invalid session id from querystring
        $pathInfo = preg_replace( "/[?|&]".session_name()."=[^&]*/", "", $pathInfo );
        
        // correct query delimiter
        if ( preg_match( "/&/", $pathInfo ) && !preg_match( "/\?/", $pathInfo ) )
			$pathInfo = preg_replace( "/&/", "?", $pathInfo, 1 ); 
        
        // remove trash
        $pathInfo = $this->removeTrail( $pathInfo );
        
		// set fresh session name and id
        $pathInfo .= preg_match( "/\?/", $pathInfo )? "&" : "?";
        $pathInfo .= session_name() . "=" . session_id();
        
        // add Anchor-Fragment
        $pathInfo .= isset( $dummyArray[1] )? "#".$dummyArray[1] : "";
        
        return $pathInfo;                       
    }
    
	/**
	 * Fallback via HIDDEN FIELD (if Cookies are turned off).
	 *
	 * @param  void
	 * @return string  HTML-Hidden-Input-Tag mit der Session-ID
	 * @access public
	 */
    public function hidden()
	{
		if ( $this->usesCookies || $this->transSID )
			return "";
        
		return "<INPUT  type=\"hidden\" name=\"" . session_name() . "\" value=\"" . session_id() . "\">";
    }
} // END OF GenericSession

?>
