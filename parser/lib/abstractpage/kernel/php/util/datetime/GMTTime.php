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
 * Date and Time Convertion.
 *
 * @package util_datetime
 */

class GMTTime extends PEAR
{
	/**
	 * @access public
	 */
	var $nTZones = array(
		"-12",
		"-11",
		"-10",
		"-9",
		"-8",
		"-7",
		"-6",
		"-5",
		"-4",
		"-3",
		"-2",
		"-1",
		"0",
		"1",
		"2",
		"3",
		"3.30",
		"4",
		"4.30",
		"5",
		"5.30",
		"5.45",
		"6",
		"6.30",
		"7",
		"8",
		"9",
		"9.30",
		"10",
		"11",
		"12",
		"13"
	);
	
	
	/**
	 * Return Current GMT Time Based on Given Time Zone.
	 *
	 * @access public
	 */
	function getTime( $nTimeZone )
	{
		$nSecs = 0;
		
		if ( strpos( $nTimeZone, "." ) )
		{
			$sTimes  = split( "\.", $nTimeZone );
			$nSecs   = ( $sTimes[0] * 3600 );
			$nSecs  += ( $sTimes[1] * 60   );
		}
		else
		{	
			$nSecs   = ( $nTimeZone * 3600 );
		}
		
		$nNew  = time();
		$nNew += -$nSecs;
		
		return date( "d - F - Y H:i:s", $nNew );
	}

	/**
	 * ConvertTime - Converts Any Time to Any Time.
	 *
	 * @access public
	 */
	function convertTime( $sDate, $nTimeZone1, $nTimeZone2 )
	{	
		$nSecs1 = 0;
		$nSecs2 = 0;

		if ( strpos( $nTimeZone1, "." ) )
		{
			$sTimes  = split( "\.", $nTimeZone1 );
			$nSecs1  = ( $sTimes[0] * 3600 );
			$nSecs1 += ( $sTimes[1] * 60   );
		}
		else
		{
			$nSecs1  = ( $nTimeZone1 * 3600 );
		}
		
		if ( strpos( $nTimeZone2, "." ) )
		{
			$sTimes  = split( "\.", $nTimeZone2 );
			$nSecs2  = ( $sTimes[0] * 3600 );
			$nSecs2 += ( $sTimes[1] * 60   );
		}
		else
		{
			$nSecs2  = ( $nTimeZone2 * 3600 );
		}		

		$sNew  = $sDate;
		$sNew += -$nSecs1;
		$sNew += $nSecs2;
	
		return date( "d - F - Y H:i:s", $sNew );
	}
} // END OF GMTTime

?>
