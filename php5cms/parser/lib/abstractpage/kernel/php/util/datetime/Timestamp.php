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
 * Class manipulate Timestamps in standrd and UNIX epochs format.
 *
 * @package util_datetime
 */

class Timestamp extends PEAR
{
	/**
	 * @access public
	 */
	var $day;
	
	/**
	 * @access public
	 */
	var $month;
	
	/**
	 * @access public
	 */
	var $year;
	
	/**
	 * @access public
	 */
	var $hour = 00;
	
	/**
	 * @access public
	 */
	var $minute = 00;
	
	/**
	 * @access public
	 */
	var $second = 00;
	
	/**
	 * @access public
	 */
	var $default_tz;
	
	/**
	 * @access public
	 */
	var $remote_tz;


    /**
     * Constructor
     *
     * Creates a new Timestamp Object initialized to the current date/time in the
     * system-default timezone by default. A date optionally TIMESTAMP or 
	 * UNIX TIMESTAMP format.
     *
     * @access public
     * @param  mixed $date optional - date/time to initialize
     * @return object Timestamp
     */
	function Timestamp( $date = null )
	{
		$this->local_tz = getenv( "TZ" );
			
		if ( !is_null( $date ) )
		{ 
			// Check if we have a timestamp, if so determine if its mySQL
			// or UNIX formatted, and then convert it :)
			if ( is_numeric( $date ) )
			{
				if ( !$this->setTS( $date ) )
 				    $this->setUTS( $date );
			}
			else
			{
				// Like a string.. needs to be implemented..				
			}			
		}
		else
		{
			$date = time();
			$this->setUTS( $date );
		}
	}
		

    /**
     * Set the date and time properties from a Unix style TIMESTAMP.  This may only be
     * valid for dates from 1970 to ~2038.
     *
     * @access public
     * @param  int is a unix formatted timestamp 
     */				
	function setUTS( $ts )
	{
		// Generate date and times from time stamps
		$date = explode( "/", date( "m/d/y", $ts ) );
		$time = explode( ":", date( "H:i:s", $ts ) );

		// feed values into object attributes
		$this->month  = $date[0];
		$this->day    = $date[1];
		$this->year   = $date[2];
				
		$this->hour   = $time[0];
		$this->minute = $time[1];
		$this->second = $time[2]; 		
	}

    /**
     * Set the date and time properties from a mySQL style TIMESTAMP.  
     *
     * @access public
     * @param int is a mysql formatted timestamp 
     */
	function setTS( $ts )
	{
		if ( preg_match( '/^(\d{4})-?(\d{2})-?(\d{2})([T\s]?(\d{2}):?(\d{2}):?(\d{2})(Z|[\+\-]\d{2}:?\d{2})?)?$/i', $ts, $regs ) )
		{
       	    $this->year   = $regs[1];
			$this->month  = $regs[2];
            $this->day    = $regs[3];
            $this->hour   = isset( $regs[5] )? $regs[5] : 0;
	        $this->minute = isset( $regs[6] )? $regs[6] : 0;
            $this->second = isset( $regs[7] )? $regs[7] : 0;
			
			return true;
		}
		
		return false;
	}
		
    /**
     * Get a representation of this date in Unix TIMESTAMP format. This may only be
     * valid for dates from 1970 to ~2038.
     *
     * @access public
     * @return int number of seconds since the unix epoch
     */
	function getUTS()
	{
		return mktime( $this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year );
	}
	
	/**
	 * @access public
	 */	
	function setDateStr( $str )
	{
		$arr = explode( "/", $str );

		$this->day   = $arr[1];
		$this->month = $arr[0];
		$this->year  = $arr[2];
	}
	
    /**
     * Get a representation of this date in mySQL TIMESTAMP format.  
     *
     * @access public
     * @return the date formatted in mySQL standard TIMESTAMP
     */		
	function getTS()
	{
		return date( "YmdHis", mktime( $this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year ) );
	}		

    /**
     * Sets the TimeZone for the period of the current request, the original TimeZone is 
     * stored $default_ts and can be restored by calling @setDefaultTZ(). All Timestamps
	 * are treat as UTC, so Timezone calculations is only required and used when formatting
	 * for  output.
	 *
     * @access public
     * @param int is a mysql formatted timestamp 
     */
	function setTZ( $tz )
	{
		$this->default_tz = $tz;
		putenv( "TZ=" . $tz );
	}		

    /**
     * Set the default from this request.
     *
     * Restores the default timezone, this is only applicable if the timezone has been changed with
	 * setTZ().
     *
     * @access public
     * @return null
     */			
	function setDefaultTZ()
	{
		if ( isset( $this->default_tz ) ) 
			putenv( "TZ=" . $this->default_tz );
	}

    /**
     * Get the default Timezone from this request.
     *
	 * Retrieves the original timezone for this request
     *
     * @access public
     * @return string the default time zone
     */			
	function getDefaultTZ()
	{
		if ( !isset( $this->default_tz ) ) 
			$this->default_tz = getenv( "TZ" );	
		
		return $this->default_tz;
	}

    /**
     * Get current date time values formatted with an optional timezone.
     *
	 * This function will format the the DateTime with the specified format settings 
	 * (see PHP Date function for more information).  Optionally you can also pass
	 * in a timezone as string (see Timezone.php for a potential list).
     *
     * @access public
	 * @param String is the values how the DateTime should be formatted
	 * @param String the required timezone (optional).
     * @return string the default time zone
     */			
	function format( $format, $tz = null )
	{
		// If a timezone is provided then we store the current and restore
		// it after we've format the date to the new timezone
		if ( !is_null( $tz ) ) 
			$this->setTZ( $tz );
			
		$ret = date( $format, time( $this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year ) );

		if ( !is_null( $tz ) ) 
			$this->setDefaultTZ();
			
		return $ret;
	}
} // END OF Timestamp

?>
