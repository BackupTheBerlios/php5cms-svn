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
 * Static helper functions.
 *
 * @package sys
 */
 
class WinNTUtil
{
	/**
	 * @access public
	 * @static
	 */
	function checkDNS( $host, $type = '' ) 
	{
		if ( !empty( $host ) ) 
		{
			if ( $type == '' ) 
				$type = "MX";
			
			@exec( "nslookup -type=$type $host", $output );
			
			while ( list( $k, $line ) = each( $output ) ) 
			{
				if ( eregi( "^$host", $line ) )
					return true;
			}

			return false;
		}
	}

	/**
	 * @access public
	 * @static
	 */
	function getMX( $hostname, &$mxhosts ) 
	{
		if ( !is_array( $mxhosts ) ) 
			$mxhosts = array();
		
		if ( !empty( $hostname ) ) 
		{
			@exec( "nslookup -type=MX $hostname", $output, $ret );
			
			while ( list( $k, $line ) = each( $output ) ) 
			{
				if ( ereg( "^$hostname\tMX preference = ([0-9]+), mail exchanger = (.*)$", $line, $parts ) )
					$mxhosts[$parts[1]] = $parts[2];
			}

			if ( count( $mxhosts ) ) 
			{
				reset( $mxhosts );
				ksort( $mxhosts );
				
				$i = 0;
				while ( list( $pref, $host ) = each( $mxhosts ) ) 
				{
					$mxhosts2[$i] = $host;
					$i++;
				}

				$mxhosts = $mxhosts2;
				return true;
			} 
			else 
			{
				return false;
			}
		}
	}
} // END OF WinNTUtil

?>
