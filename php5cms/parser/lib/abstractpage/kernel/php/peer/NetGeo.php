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
 * @package peer
 */
 
class NetGeo extends PEAR
{
	/**
	 * @access public
	 */
	var $error = "";
	
	
	/**
	 * @access public
	 */
	function getAddressLocation( $address, &$location, $query = "getRecord" )
	{
		$location = array();
		
		if ( !strcmp( $address, "127.0.0.1" ) )
		{
			$this->error = "$address is not a valid public Internet address!";
			return false;
		}
		
		if ( gettype( $page = @file( "http://netgeo.caida.org/perl/netgeo.cgi?method=" . urlencode( $query ) . "&target=" . urlencode( $address ) ) ) != "array" )
		{
			$this->error = "Could not query the NetGeo service";
			return false;
		}
		
		for ( $line = 0; $line < count( $page ); $line++ )
		{
			$data = strtok( $page[$line], "<\r\n" );
			
			if ( !strcmp( strtok( $data, "=" ),"VERSION" ) )
				break;
		}
		
		if ( $line >= count( $page ) )
		{
			$this->error = "Could not understand NetGeo service response";
			return false;
		}
		
		for ( $line++; $line < count( $page ); $line++ )
		{
			$attribute = strtok( strtok( $page[$line], "<\r\n"), ":" );
			
			if ( strcmp( $attribute, "" ) )
				$location[$attribute] = trim( strtok( "<\r\n" ) );
		}
		
		return true;
	}

	/**
	 * @access public
	 */
	function calculateDistance( $longitude_1, $latitude_1, $longitude_2, $latitude_2 )
	{
		$long1 = $longitude_1 * M_PI / 180;
		$lat1  = $latitude_1  * M_PI / 180;
		$long2 = $longitude_2 * M_PI / 180;
		$lat2  = $latitude_2  * M_PI / 180;
		
		return( 111 * 180 / M_PI * acos( sin( $lat1 ) * sin( $lat2 ) + cos( $lat1 ) * cos( $lat2 ) * cos( $long2 - $long1 ) ) );
	}
} // END OF NetGeo

?>
