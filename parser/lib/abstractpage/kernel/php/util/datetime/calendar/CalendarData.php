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
 * Calendar class
 * 
 * Return monthly calender as an array containing dates formated in a matrix
 * successfully splits format and structure.
 *
 * @package util_datetime_calendar
 */

class CalendarData extends PEAR
{
	/**
	 * Year of interest
	 * @access public
	 */
	var $year;
	
	/**
	 * Month of interest
	 * @access public
	 */
	var $month;
	
	/**
	 * Number of days in given month
	 * @access public
	 */
	var $numdays;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function CalendarData($m=false, $y=false)
	{
		$this->setYear( $y );
		$this->setMonth( $m );
	}
    

	/**
	 * @access public
	 */
	function setMonth( $m = false ) 
	{
		if ( !$m )
			$this->month = date( "m" );
		else
			$this->month = $m;
			
		$this->numdays = $this->getDaysInMonth();
		return true;		
	}

	/**
	 * @access public
	 */
	function setYear( $y = false ) 
	{
		if ( !$y )
			$this->year = date( "Y" );		
		else
			$this->year = $y;
		
		return true;
	}
	
	/**
	 * Get calendar month.
	 * - retrieve matrix of days with in month of interest
	 * - either format output or return timestamps
	 * - returns matrix (weekday) by (week of year)
	 */
	function getCalendarMonth( $format = false ) 
	{
		for ( $i = 1; $i <= $this->numdays; $i++ ) 
		{
			$wday = date( "D", mktime( 0, 0, 0, $this->month, $i, $this->year ) ); // textual day of week e.g. Sun
			$week = date( "W", mktime( 0, 0, 0, $this->month, $i, $this->year ) ); // week of year
			
			if ( !$format )
				$month[$week][$wday] = mktime( 0, 0, 0, $this->month, $i, $this->year );
			else
				$month[$week][$wday] = date( $format, mktime( 0, 0, 0, $this->month, $i, $this->year ) );
		}
		
		return $month;
	}
	
	/**
 	 * Get calendar year.
	 * - retrieves monthly calendars for the whole year
	 */
	function getCalendarYear( $format = false ) 
	{
		for ( $i = 0; $i < 12; $i++ ) 
		{
			$this->setMonth( $i );
			$year[$i] = $this->getCalendarMonth( $format );
		}
		
		return $year;
	}
	
	/**
	 * get Days in Month.
	 * - retrieves the number of days in this month
	 * - e.g, a leap year Feb has 29 days so getDaysInMonth returns 29
	 */
	function getDaysInMonth()
	{
		return date( "t", mktime( 0, 0, 0, $this->month, 01, $this->year ) );
	}
	
	/**
	 * @access public
	 */
	function getNextMonth( $format = false )
	{
		if ( !$format )
			return mktime( 0, 0, 0, $this->month + 1, 01, $this->year );
		else
			return date( $format, mktime( 0, 0, 0, $this->month + 1, 01, $this->year ) );
	}

	/**
	 * @access public
	 */
	function getLastMonth( $format = false ) 
	{
		if ( !$format )
			return mktime( 0, 0, 0, $this->month, 0, $this->year );
		else
			return date( $format, mktime( 0, 0, 0, $this->month, 0, $this->year ) );
	}
} // END OF CalendarData

?>
