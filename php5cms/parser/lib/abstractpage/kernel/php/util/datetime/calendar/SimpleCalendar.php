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
 * This is an extensible calendar class. It doesn't print any HTML for you,
 * but it gives you easy straight-forward access to all the days of a given month.  
 * 
 * Day objects are not real objects, just arrays. 
 * $calendar->nextDay(); 
 * $day = $calendar->currentDay; 
 * $day[0] = date, $day[1] = day of week 
 *
 * @package util_datetime_calendar
 */
 
class SimpleCalendar extends PEAR
{ 
	/**
	 * @access public
	 */
    var $maxDays;
	
	/**
	 * @access public
	 */
    var $currentDay;
	
	/**
	 * @access public
	 */
    var $dayPointer = 0;
	
	/**
	 * @access public
	 */
    var $weekPointer = 0;
	
	/**
	 * @access public
	 */
    var $dayArray = array();
	
	/**
	 * @access public
	 */
    var $skipEnds = false;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
    function SimpleCalendar( $month, $year )
	{
        $tmp = date( "d | w", mktime( 0, 0, 0, $month, 1, $year ) ); 
        $firstDay = explode(" | ",$tmp); 
        $this->maxDays = $this->daysInMonth( $month, $year ); 

        // fill in week with blanks until first of month 
        for ( $x = 0; $x < $firstDay[1]; $x++ )
			$this->dayArray[] = array( "", -1 );

        // fill in rest of days 
        $y = $firstDay[1]; 
        
		for ( $x = 1; $x <= $this->maxDays; $x++ )
		{ 
        	$this->dayArray[] = array( $x, $y ); 
        
			if ( $y == 6 )
				$y = 0;
			else 
				$y++;
        } 

        for ( $x = $y; $x <= 6; $x++ )
			$this->dayArray[] = array( "",-1 );         
    } 


	/**
	 * @access public
	 */
    function nextDay()
	{ 
		if ( $this->dayPointer > count( $this->dayArray ) )
		{ 
			$this->currentDay = array( "", -1 ); 
        	return PEAR::raiseError( "No more days." );
		} 

       	$curDay = $this->dayArray[$this->dayPointer]; 

		if ( $this->skipEnds )
		{ 
        	if ( $curDay[1] == 6 )
				$this->dayPointer += 2; 
        
			if ( $curDay[1] == 0 )
				$this->dayPointer++;
				
        	$curDay = $this->dayArray[$this->dayPointer]; 
		}
		 
		$this->dayPointer++; 
		$this->currentDay = $curDay; 

		return true; 
	}
	
	/**
	 * @access public
	 */
	function hasMoreDays()
	{ 
        return ( $this->dayPointer < count( $this->dayArray ) ); 
    } 

	/**
	 * @access public
	 */
    function daysInMonth( $month, $year ) 
	{ 
		return 31 - ( ( ( $month - ( ( $month < 8 )? 1 : 0 ) ) % 2 ) + ( ( $month == 2 )? ( ( !( $year % ( ( !( $year % 100 ) )? 400 : 4 ) ) )? 1 : 2 ) : 0 ) ) ;
	}
} // END OF SimpleCalendar

?>
