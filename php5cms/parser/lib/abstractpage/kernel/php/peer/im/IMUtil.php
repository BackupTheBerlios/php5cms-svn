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


define( "IMUTIL_YAHOO_ONLINE",  1 );
define( "IMUTIL_YAHOO_OFFLINE", 2 );
define( "IMUTIL_YAHOO_UNKNOWN", 3 );


/**
 * @package peer_im
 */
 
class IMUtil
{
	/**
	 * This class allows to check the online status of an Yahoo! account.	
	 * It connects directly to the Yahoo! status server.
	 *
	 * @access public
	 * @static
	 */
	function getYahooStatus( $yahoo = "" )
	{
		$lines = @file( "http://opi.yahoo.com/online?u=" . $yahoo . "&m=t" ); 
		
		if ( $lines !== false ) 
		{
			$response = implode( "", $lines );
			
			if ( strpos( $response, "NOT ONLINE" ) !== false )
				return IMUTIL_YAHOO_OFFLINE;
			else if ( strpos( $response, "ONLINE" ) !== false )
				return IMUTIL_YAHOO_ONLINE;
			else
				return IMUTIL_YAHOO_UNKNOWN;
		}
		else
		{
			return PEAR::raiseError( "Unable to connect to http://opi.yahoo.com." );
		}
	}
} // END OF IMUtil

?>
