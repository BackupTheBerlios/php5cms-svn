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
 * The purpose of this script is to be able to return the time span
 * between any two specific moments in time AFTER the Unix Epoch
 * (January 1 1970) in a human-readable format.
 *
 * @package util_datetime
 */

class TimeSpan extends PEAR
{
	/**
	 * @access public
	 */
	var $years;
	
	/**
	 * @access public
	 */
	var $months;
	
	/**
	 * @access public
	 */
	var $weeks;
	
	/**
	 * @access public
	 */
	var $days;
	
	/**
	 * @access public
	 */
	var $hours;
	
	/**
	 * @access public
	 */
	var $minutes;
	
	/**
	 * @access public
	 */
	var $seconds;
	
	
	/**
	 * @access public
	 */
	function calc( $now, $then )
	{
		$this->years   = 0;
		$this->months  = 0;
		$this->weeks   = 0;
		$this->days    = 0;
		$this->hours   = 0;
		$this->minutes = 0;
		$this->seconds = 0;

		$duration = $now - $then;

		// number of years
		$dec  = $now;
		$year = ( ( date( 'L', $now ) )? ( 60 * 60 * 24 * 366 ) : ( 60 * 60 * 24 * 365 ) );

		while ( ( $duration / $year ) >= 1 )
		{
			$this->years += 1;
			$duration -= $year;
			$dec  -= $year;
			$year  = ( ( date( 'L', $dec ) )? ( 60 * 60 * 24 * 366 ) : ( 60 * 60 * 24 * 365 ) );
		}
		
		// number of months
		$dec = $now;
		$day = ( 60 * 60 * 24 );
		$m   = date( 'n', $now );
		$d   = date( 'd', $now );

		while ( ( $duration - $day ) >= 0 )
		{
			$duration -= $day;
			$dec -= $day;
			$this->days += 1;

			if ( ( date( 'n', $dec ) != $m ) && ( date( 'd', $dec ) <= $d ) )
			{
				$m = date( 'n', $dec );
				$d = date( 'd', $dec );

				$this->months += 1;
				$this->days = 0;
			}
		}

		// number of weeks
		$this->weeks  = floor( $this->days / 7 );
		$this->days  %= 7;

		// number of hours, minutes, and seconds.
		$this->hours = floor( $duration / ( 60 * 60 ) );
		$duration %= (60*60);

		$this->minutes = floor( $duration / 60 );
		$duration %= 60;

		$this->seconds = $duration;
	}
} // END OF TimeSpan

?>
