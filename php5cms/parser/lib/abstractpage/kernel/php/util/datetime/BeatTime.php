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
 * Swatch Internet Time, Beat
 *
 * A Swatch .beat is 1 minute and 26.4 seconds. The virtual and real day is divided into 1000 ".beats". 
 * That means that 12 noon in the old time system is the equivalent of @500 Swatch .beats.
 *
 * Biel MeanTime (BMT) is the universal reference for Internet Time. 
 * A day in Internet Time begins at midnight BMT (@000 Swatch .beats) (Central European Wintertime). 
 * The BMT meridian was inaugurated on 23 October 1998 in the presence of Nicholas Negroponte, 
 * founder and director of the Massachusetts Institute of Technology's Media Laboratory. 
 *
 * @package util_datetime
 */
 
class BeatTime extends PEAR
{
	/**
	 * timestamp adjustment
	 * @access public
	 */	
	var $adjustment;
	
	/**
	 * number of seconds since midnight
	 * @access public
	 */	
	var $seconds;
	
	/**
	 * GMT timestamp
	 * @access public
	 */	
	var $gmtts;
	
	/**
	 * day
	 * @access public
	 */	
	var $day;
	
	/**
	 * month
	 * @access public
	 */	
	var $month;
	
	/**
	 * year
	 * @access public
	 */	
	var $year;
	
	/**
	 * beat time
	 * @access public
	 */	
	var $beat;


	/**
	 * Constructor
	 *
	 * @access public
	 */
  	function BeatTime()
  	{
		// Biel Mean Time (BMT) is GMT +0100, so we have to add one hour (3600 seconds)
    	$this->adjustment = 3600;

		// call getGMT to get the proper date/time strings along with the beat
    	$this->getGMT();
  	}


	/**
	 * @access public
	 */	
  	function getGMT()
	{
    	// there are 86400 seconds to 24 hours
    	$this->gmtts   = ( gmdate( time() ) + $this->adjustment );	// adjust UNIX timestamp to be BMT instead of GMT
    	$this->seconds = ( $this->gmtts ) % 86400;					// seconds since midnight GMT
    	$this->day     = gmdate( "d" );								// day in string with leading zero
    	$this->month   = strtolower( gmdate( "M" ) );				// month in string, abbreviated
    	$this->year    = gmdate( "Y" );								// year in string, 4 digits long
    	$this->beat    = intval( $this->seconds / 86.4 );			// one beat is 1 minute and 26.4 seconds = 86.4 seconds
	}

	/**
	 * @access public
	 */	
	function formatBeat( $beat ) 
	{
    	// Always make sure that the beat time is 3 chars long, 
		// add leading zeroes if necessary, design issue only.
    	if ( $beat < 10 )
			$beat = "00" . $beat;
    	else if ( $beat < 100 )
			$beat = "0" . $beat;
    
		return $beat;
  	}

	/**
	 * @access public
	 */	
  	function getBeat()
	{
    	// includes a call to formatBeat which adds leading zeroes to the beat time
    	$beatstring = sprintf( "<b>%s</b>|<b>%s</b>|<b>%s</b>|<b>@%s</b>", $this->day, $this->month, $this->year, $this->formatBeat( $this->beat ) );
    	return $beatstring;
  	}
} // END OF BeatTime

?>
