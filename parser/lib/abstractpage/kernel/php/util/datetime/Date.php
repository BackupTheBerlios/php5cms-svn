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
 * Class representing a specific instant in time.
 *
 * @package util_datetime
 */

class Date extends PEAR
{
	/**
	 * @access public
	 */
    var $seconds = 0;
	
	/**
	 * @access public
	 */
	var $minutes = 0;
	
	/**
	 * @access public
	 */
	var $hours = 0;
	
	/**
	 * @access public
	 */
	var $mday = 0;
	
	/**
	 * @access public
	 */
	var $wday = 0;
	
	/**
	 * @access public
	 */
	var $mon = 0;
	
	/**
	 * @access public
	 */
	var $year = 0;
	
	/**
	 * @access public
	 */
	var $yday = 0;
	
	/**
	 * @access public
	 */
	var $weekday = '';
	
	/**
	 * @access public
	 */
	var $month = '';

	/**
	 * @access private
	 */
	var $_utime = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   mixed in default NULL either a string or a Unix timestamp, defaulting to now
     */
    function Date( $in = null ) 
	{
      	if ( is_string( $in ) && ( -1 !== ( $time = strtotime( $in ) ) ) )
        	$this->_utime( $time );
		else if ( is_int( $in ) ) 
        	$this->_utime( $in );
		else if ( is_null( $in ) ) 
        	$this->_utime( time() );
		else
        	$this->_utime( time() );
    }
    
	
    /**
     * Static method to get current date/time.
     *
     * @static
     * @access  public
     * @return  &util.Date
     */
    function &now()
	{
      	return new Date( null );
    }
    
    /**
     * Create a date from a string.
     *
     * <code>
     *   $d= &Date::fromString('yesterday');
     *   $d= &Date::fromString('2003-02-01');
     * </code>
     *
     * @access  public
     * @static
     * @see     php://strtotime
     * @param   string str
     * @return  &Date
     */
    function &fromString( $str ) 
	{
      	return new Date( $str );
    }
    
    /**
     * Compare this date to another date.
     *
     * @access  public
     * @param   &Date date A date object
     * @return  int equal: 0, date before $this: < 0, date after $this: > 0
     */
    function compareTo( &$date ) 
	{
      	return $date->getTime() - $this->getTime();
    }
    
    /**
     * Checks whether this date is before a given date.
     *
     * @access  public
     * @param   &Date date
     * @return  bool
     */
    function isBefore( &$date ) 
	{
      	return $this->getTime() < $date->getTime();
    }

    /**
     * Checks whether this date is after a given date.
     *
     * @access  public
     * @param   &Date date
     * @return  bool
     */
    function isAfter( &$date ) 
	{
      	return $this->getTime() > $date->getTime();
    }
    
    /**
     * Retrieve Unix-Timestamp for this date.
     *
     * @access  public
     * @return  int Unix-Timestamp
     */
    function getTime()
	{
      	return $this->_utime;
    }

    /**
     * Get seconds.
     *
     * @access  public
     * @return  int
     */
    function getSeconds()
	{
      	return $this->seconds;
    }

    /**
     * Get minutes.
     *
     * @access  public
     * @return  int
     */
    function getMinutes()
	{
      	return $this->minutes;
    }

    /**
     * Get hours.
     *
     * @access  public
     * @return  int
     */
    function getHours()
	{
      	return $this->hours;
    }

    /**
     * Get day.
     *
     * @access  public
     * @return  int
     */
    function getDay()
	{
      	return $this->mday;
    }

    /**
     * Get month.
     *
     * @access  public
     * @return  int
     */
    function getMonth()
	{
      	return $this->mon;
    }

    /**
     * Get year.
     *
     * @access  public
     * @return  int
     */
    function getYear()
	{
      	return $this->year;
    }

    /**
     * Get day of year.
     *
     * @access  public
     * @return  int
     */
    function getDayOfYear()
	{
      	return $this->yday;
    }

    /**
     * Get day of week.
     *
     * @access  public
     * @return  int
     */
    function getDayOfWeek()
	{
      	return $this->wday;
    }
    
    /**
     * Create a string representation.
     *
     * @access  public
     * @see     php://date
     * @param   string format default 'r' format-string
     * @return  string the formatted date
     */
    function toString( $format= 'r' ) 
	{
      	return date( $format, $this->_utime );
    }

    /**
     * Format date.
     *
     * @access  public
     * @see     php://strftime
     * @param   string format default '%c' format-string
     * @return  string the formatted date
     */
    function format( $format= '%c' ) 
	{
      	return strftime( $format, $this->_utime );
    }
	
	
	// private methods
	
    /**
     * Private helper function which sets all of the public member variables.
     *
     * @access  private
     * @param   int utime Unix-Timestamp
     */
    function _utime( $utime ) 
	{
      	foreach ( getdate( $this->_utime = $utime ) as $key => $val ) 
		{
        	if ( is_string( $key ) ) 
				$this->$key = $val;
      	}
    }
} // END OF Date

?>
