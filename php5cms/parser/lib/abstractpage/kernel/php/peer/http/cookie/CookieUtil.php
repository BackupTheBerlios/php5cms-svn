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
 * @package peer_http_cookie
 */
 
class CookieUtil
{
	/**
	 * @access public
	 * @static
	 */
	function enabled()
	{
		// set temporary cookie
		setcookie( "TEMPCOOKIE", "NOVALUE", time() + 60 * 60 );

		// set cookie variable
		$cookieinfo = $_COOKIE["TEMPCOOKIE"];

		if ( $cookieinfo == "NOVALUE" )
			return true;
		else
			return false;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function set( $name, $value = '', $expire = null, $path = null, $domain = null, $secure = null ) 
	{
		if ( !is_null( $expire ) ) 
			$expire = (double)$expire;
		
		if ( is_null( $expire ) ) 
			return setcookie( $name, $value );
		else if ( is_null( $path ) ) 
			return setcookie( $name, $value, $expire );
		else if ( is_null( $domain ) ) 
			return setcookie( $name, $value, $expire, $path );
		else if ( is_null( $secure ) ) 
			return setcookie( $name, $value, $expire, $path, $domain );
		else 
			return setcookie( $name, $value, $expire, $path, $domain, $secure );
	}
} // END OF CookieUtil
	
?>
