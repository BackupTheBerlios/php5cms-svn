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


define( 'MP_NEW_MOON_NAME', 'New Moon' );
define( 'MP_NEW_MOON_ID', 0 );
define( 'MP_WAXING_CRESCENT_NAME', 'Waxing Crescent' );
define( 'MP_WAXING_CRESCENT_ID', 1 );
define( 'MP_FIRST_QUARTER_NAME', 'First Quarter Moon' );
define( 'MP_FIRST_QUARTER_ID', 2 );
define( 'MP_WAXING_GIBBOUS_NAME', 'Waxing Gibbous' );
define( 'MP_WAXING_GIBBOUS_ID', 3 );
define( 'MP_FULL_MOON_NAME', 'Full Moon' );
define( 'MP_FULL_MOON_ID', 4 );
define( 'MP_WANING_GIBBOUS_NAME', 'Waning Gibbous' );
define( 'MP_WANING_GIBBOUS_ID', 5 );
define( 'MP_THIRD_QUARTER_MOON_NAME', 'Third Quarter Moon' );
define( 'MP_THIRD_QUARTER_MOON_ID', 6 );
define( 'MP_WANING_CRESCENT_NAME', 'Waning Crescent' );
define( 'MP_WANING_CRESCENT_ID', 7 );
define( 'MP_DAY_IN_SECONDS', 60 * 60 * 24 );


/**
 * MoonPhase Class
 *
 * You will see a slight drift in the cycle if you compare the results to other phase calculations...
 * This is probably because of different degrees of precision among phase periods used, as well as 
 * float precision from computer to computer. It is more or less accurate and seems to always report the
 * correct phase name.
 *
 * Usage:
 *
 * $dateAsTimeStamp = ''; // no need to pass the date if you want to use the current date
 * // $dateAsTimeStamp = strtotime( 'June 9 2003 21:00 UT' );
 * $mp = new MoonPhase( $dateAsTimeStamp );
 * 
 * echo "<b>On this date: ", strftime( "%b %d %Y %H:%M:%S", $mp->getDateAsTimeStamp() ), ":</b>";
 * echo "<br />\n";
 * echo "The position (phase) within the moon's cycle: ", $mp->getPositionInCycle();
 * echo "<br />\n";
 * echo "The phase name: ", $mp->getPhaseName();
 * echo "<br />\n";
 * echo "The percentage of lunar illumination is ", $mp->getPercentOfIllumination();
 * echo "<br />\n";
 * echo "The days until the next full moon are: ", $mp->getDaysUntilNextFullMoon();
 * echo "<br />\n";
 * echo "The days until the next new moon are: ", $mp->getDaysUntilNextNewMoon();
 * echo "<br />\n";
 * echo "The days until the next first quarter moon are: ", $mp->getDaysUntilNextFirstQuarterMoon();
 * echo "<br />\n";
 * echo "The days until the next last quarter moon are: ", $mp->getDaysUntilNextLastQuarterMoon();
 * echo "<br />\n<br />\n";
 * echo "<b>Moon phases for upcoming week:</b>";
 * echo "<br />\n";
 * 
 * $UpcomingWeekArray = $mp->getUpcomingWeekArray();
 * foreach ( $UpcomingWeekArray as $timeStamp => $phaseID )
 * 		echo "&nbsp;&nbsp;", date( 'l', $timeStamp ), ": ", $mp->getPhaseName( $phaseID ), "<br />\n";
 *
 * @package util_datetime
 */
 
class MoonPhase extends PEAR
{
	/**
	 * @access public
	 */
	var $dateAsTimeStamp;
	
	/**
	 * @access public
	 */
	var $moonPhaseIDforDate;
	
	/**
	 * @access public
	 */
	var $moonPhaseNameForDate;
	
	/**
	 * @access public
	 */
	var $someFullMoonDate;<br>
		
	/**
	 * @access public
	 */
	var $allMoonPhases = array();
	
	/**
	 * complete moon cycle
	 * @access public
	 */
	var $periodInDays = 29.53058867;
	
	/**
	 * gets set when you ask for it
	 * @access public
	 */
	var $periodInSeconds = -1;

	
	/**
	 * Constructor
	 * $timestamp (int) date of which to calculate a moon phase and relative phases for
	 *
	 * @access public
	 */
	function MoonPhase( $timeStamp = -1 ) 
	{
		$this->allMoonPhases = array(
			MP_NEW_MOON_NAME,
			MP_WAXING_CRESCENT_NAME,
			MP_FIRST_QUARTER_NAME,
			MP_WAXING_GIBBOUS_NAME,
			MP_FULL_MOON_NAME,
			MP_WANING_GIBBOUS_NAME,
			MP_THIRD_QUARTER_MOON_NAME,
			MP_WANING_CRESCENT_NAME
		);
	
		// set base date, that we know was a full moon:
		// (http://aa.usno.navy.mil/data/docs/MoonPhase.html)
		$this->someFullMoonDate = strtotime( "August 22 2002 22:29 UT" );
	
		if ( $timeStamp == '' || $timeStamp == -1 ) 
			$timeStamp = time();
	
		$this->setDate( $timeStamp );
	}


	/**
	 * Sets the internal date for calculation and calulates the moon phase for that date.
	 * called from the constructor.
	 *
	 * $timeStamp (int) date to set as unix timestamp
	 */
	function setDate( $timeStamp = -1 ) 
	{
		if ( $timeStamp == '' || $timeStamp == -1 ) 
			$timeStamp = time();
	
		$this->dateAsTimeStamp = $timeStamp;
		$this->_calcMoonPhase();
	}
	
	/**
	 * return (array) all moon phases as ID => Name
	 */
	function getAllMoonPhases() 
	{
		return $this->allMoonPhases;
	}

	function getBaseFullMoonDate() 
	{
		return $this->someFullMoonDate;
	}

	/**
	 * return (int) timestamp of the current date being calculated
	 */
	function getDateAsTimeStamp() 
	{
		return $this->dateAsTimeStamp;
	}

	function getDaysUntilNextFullMoon() 
	{
		$position = $this->getPositionInCycle();
		return round( ( 1 - $position ) * $this->getPeriodInDays(), 2 );
	}

	function getDaysUntilNextLastQuarterMoon() 
	{
		$days = 0;
		$position = $this->getPositionInCycle();
	
		if ( $position < 0.25 )
			$days = ( 0.25 - $position ) * $this->getPeriodInDays();
		else if ( $position >= 0.25 )
			$days = ( 1.25 - $position ) * $this->getPeriodInDays();
		
		return round( $days, 1 );
	}

	function getDaysUntilNextFirstQuarterMoon() 
	{
		$days = 0;
		$position = $this->getPositionInCycle();
	
		if ( $position < 0.75 )
			$days = ( 0.75 - $position ) * $this->getPeriodInDays();
		else if ( $position >= 0.75 )
			$days = ( 1.75 - $position ) * $this->getPeriodInDays();
	
		return round($days,1);
	}

	function getDaysUntilNextNewMoon() 
	{
		$days = 0;
		$position = $this->getPositionInCycle();
	
		if ( $position < 0.5 )
			$days = ( 0.5 - $position ) * $this->getPeriodInDays();
		else if ($position >= 0.5)
			$days = ( 1.5 - $position ) * $this->getPeriodInDays();
	
		return round($days, 1);
	}

	/**
	 * returns the percentage of how much lunar face is visible
	 */
	function getPercentOfIllumination() 
	{
		// from http://www.lunaroutreach.org/cgi-src/qpom/qpom.c
		// C version: // return (1.0 - cos( ( 2.0 * M_PI * phase ) / ( LPERIOD/ 86400.0 ) ) ) / 2.0;
		$percentage  = ( 1.0 + cos( 2.0 * M_PI * $this->getPositionInCycle() ) ) / 2.0;
		$percentage *= 100;
		$percentage  = round( $percentage, 1 ) . '%';
		
		return $percentage;
	}

	function getPeriodInDays() 
	{
		return $this->periodInDays;
	}

	function getPeriodInSeconds() 
	{
		if ( $this->periodInSeconds > -1 ) 
			return $this->periodInSeconds; // in case it was cached
	
		$this->periodInSeconds = $this->getPeriodInDays() * MP_DAY_IN_SECONDS;
		return $this->periodInSeconds;
	}

	function getPhaseID() 
	{
		return $this->moonPhaseIDforDate;
	}

	/**
	 * $ID (int) ID of phase, default is to get the phase for the current date passed in constructor
	 */
	function getPhaseName( $ID = -1 ) 
	{
		if ( $ID <= -1 ) 
			return $this->moonPhaseNameForDate; // get name for this current date
	
		return $this->allMoonPhases[$ID]; // or.. get name for a specific ID
	}

	/**
	 * return (float) number between 0 and 1.  0 or 1 is the beginning of a cycle (full moon) 
	 *		and 0.5 is the middle of a cycle (new moon).
	 */
	function getPositionInCycle() 
	{
		$diff            = $this->getDateAsTimeStamp() - $this->getBaseFullMoonDate();
		$periodInSeconds = $this->getPeriodInSeconds();
		$position        = ( $diff % $periodInSeconds ) / $periodInSeconds; 
	
		if ( $position < 0 )
			$position = 1 + $position;
	
		return $position;
	}

	/**
	 * $newStartingDateAsTimeStamp (int) set a new date to start the week at, or use the current date
	 * return (array[6]) weekday timestamp => phase for weekday
	 */
	function getUpcomingWeekArray( $newStartingDateAsTimeStamp = -1 ) 
	{
		$newStartingDateAsTimeStamp = ( $newStartingDateAsTimeStamp > -1 )? $newStartingDateAsTimeStamp : $this->getDateAsTimeStamp();
		$moonPhaseObj = get_class( $this );
		$weeklyPhase = new $moonPhaseObj( $newStartingDateAsTimeStamp );
		$upcomingWeekArray = array();
		
		for ( $day = 0, $thisTimeStamp = $weeklyPhase->getDateAsTimeStamp(); $day < 7; $day++, $thisTimeStamp += MP_DAY_IN_SECONDS ) 
		{
			$weeklyPhase->setDate( $thisTimeStamp );
			$upcomingWeekArray[$thisTimeStamp] = $weeklyPhase->getPhaseID();
		}
		
		unset( $weeklyPhase );
		return $upcomingWeekArray;
	}
	

	// private methods
	
	/**
	 * Sets the moon phase ID and moon phase name internally
	 */
	function _calcMoonPhase() 
	{
		$position = $this->getPositionInCycle();
	
		if ( $position >= 0.474 && $position <= 0.53 )
			$phaseInfoForCurrentDate = array( MP_NEW_MOON_ID, MP_NEW_MOON_NAME );
		else if ( $position >= 0.53 && $position <= 0.724 )
			$phaseInfoForCurrentDate = array( MP_WAXING_CRESCENT_ID, MP_WAXING_CRESCENT_NAME );
		else if ( $position >= 0.724 && $position <= 0.776 )
			$phaseInfoForCurrentDate = array( MP_FIRST_QUARTER_ID, MP_FIRST_QUARTER_NAME );
		else if ( $position >= 0.776 && $position <= 0.974 )
			$phaseInfoForCurrentDate = array( MP_WAXING_GIBBOUS_ID, MP_WAXING_GIBBOUS_NAME );
		else if ( $position >= 0.974 || $position <= 0.026 )
			$phaseInfoForCurrentDate = array( MP_FULL_MOON_ID, MP_FULL_MOON_NAME );
		else if ( $position >= 0.026 && $position <= 0.234 )
			$phaseInfoForCurrentDate = array( MP_WANING_GIBBOUS_ID, MP_WANING_GIBBOUS_NAME );
		else if ( $position >= 0.234 && $position <= 0.295 )
			$phaseInfoForCurrentDate = array( MP_THIRD_QUARTER_MOON_ID, MP_THIRD_QUARTER_MOON_NAME );
		else if ( $position >= 0.295 && $position <= 0.4739 )
			$phaseInfoForCurrentDate = array( MP_WANING_CRESCENT_ID, MP_WANING_CRESCENT_NAME );
	
		list( $this->moonPhaseIDforDate, $this->moonPhaseNameForDate ) = $phaseInfoForCurrentDate;
	}
} // END OF MoonPhase

?>
