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
 * @package util_datetime
 */
 
class TimeUtil
{
    /**
     * Converts a UNIX timestamp to a 4-byte DOS date and time format (date
     * in high 2-bytes, time in low 2-bytes allowing magnitude comparison).
     *
     * @access private
     * @param  optional integer $unixtime  The current UNIX timestamp.
     * @return integer  The current date in a 4-byte DOS format.
	 * @static
     */
    function unixToDosTime( $unixtime = null )
    {
        $timearray = ( is_null( $unixtime ) )? getdate() : getdate( $unixtime );

        if ( $timearray['year'] < 1980 ) 
		{
             $timearray['year']    = 1980;
             $timearray['mon']     = 1;
             $timearray['mday']    = 1;
             $timearray['hours']   = 0;
             $timearray['minutes'] = 0;
             $timearray['seconds'] = 0;
        }

        return ( ( $timearray['year'] - 1980 ) << 25 ) |
                 ( $timearray['mon']     << 21 ) |
                 ( $timearray['mday']    << 16 ) |
                 ( $timearray['hours']   << 11 ) |
                 ( $timearray['minutes'] <<  5 ) |
                 ( $timearray['seconds'] >>  1 );
    }
	
	/**
	 * A function to convert number of seconds (eg; 96172) to a readable format
	 * such as: 1 day, 2 hours, 42 mins, and 52 secs
	 * takes unix timestamp as input
	 * Based on the word_time function from PG+ (http://pgplus.ewtoo.org)
	 *
	 * @static
	 * @access public
	 */
	function timeToString( $t = 0 )
	{
		if ( !$t )
			return "no time at all";
		
		if ( $t < 0 )
		{
			$neg = 1;
			$t = 0 - $t;
		}

		$days = $t / 86400;
		$days = floor( $days );
		$hrs  = ( $t / 3600 ) % 24;
		$mins = ( $t /   60 ) % 60;
		$secs = $t % 60;

		$timestring = "";

		if ( $neg )
			$timestring .= "negative ";
		
		if ( $days )
		{
			$timestring .= "$days day" . ( $days == 1 ? "" : "s" );
			
			if ( $hrs || $mins || $secs )
				$timestring .= ", ";
		}
		
		if ( $hrs )
		{
			$timestring .= "$hrs hour" . ( $hrs == 1? "" : "s" );
			
			if ( $mins && $secs )
				$timestring .= ", ";
			
			if ( ( $mins && !$secs ) || ( !$mins && $secs ) )
				$timestring .= " and ";
		}
		
		if ( $mins )
		{
			$timestring .= "$mins min" . ( $mins == 1? "" : "s" );
			
			if ( $mins && $secs )
				$timestring .= ", ";
			
			if ( $secs )
				$timestring .= " and ";
		}
		
		if ( $secs )
			$timestring .= "$secs sec" . ( $secs == 1? "" : "s" );
				
		return $timestring;
	}
} // END OF TimeUtil

?>
