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


/* Incoming data handling with securing of the content of the variables. */
define( "REQUESTUTIL_VAR_STRING",  0 );
define( "REQUESTUTIL_VAR_INTEGER", 1 );
define( "REQUESTUTIL_VAR_FLOAT",   2 );
define( "REQUESTUTIL_VAR_NONE",    3 );	
		

/**
 * @package peer_http
 */
 
class RequestUtil extends PEAR
{
	/**
	 * Reads and secures the variable coming from one of the request arrays.
	 *
	 * @param string $method Name of method (POST,GET,COOKIE,SESSION,REQUEST)
	 * @param string $variable Name of variable
	 * @param int $type Type of variable (for securing).
	 * @access public
	 * @return mixed Secured variable from selected source
	 */
	function getRequest( $method, $variable, $type )
	{
		if ( !isset( $type ) )
			$type = REQUESTUTIL_VAR_STRING;
			
		$retval = null;
		
		switch ( strtoupper( $method ) )
		{
			case "REQUEST": 
				if ( isset( $_REQUEST["$variable"] ) )
					$retval = $_REQUEST["$variable"];
				
				break;
				
			case "POST": 
				if ( isset( $_POST["$variable"] ) )
					$retval = $_POST["$variable"];
				
				break;
				
			case "GET": 
				if ( isset( $_GET["$variable"] ) )
					$retval = $_GET["$variable"];
				
				break;
				
			case "SESSION": 
				if ( isset( $_SESSION["$variable"] ) )
					$retval = $_SESSION["$variable"];
				
			case "COOKIE":
				if ( isset( $_COOKIE["$variable"] ) )
					$retval = $_COOKIE["$variable"];
				
				break;
		}
		
		// method switch
		switch ( $type )
		{
			case REQUESTUTIL_VAR_STRING:
				if ( get_magic_quotes_gpc() == 0 )
    				$retval = addslashes( $retval );
				
				break;
			
			case REQUESTUTIL_VAR_INTEGER:
				$retval = intval( $retval );
				// break;
				
			case REQUESTUTIL_VAR_FLOAT:
				$retval = floatval( $retval );
				break;
				
			case REQUESTUTIL_VAR_NONE:
				break;
			
			default :
				$retval = null;
				break; 
		}
		
		return $retval;
	} 
} // END OF RequestUtil

?>
