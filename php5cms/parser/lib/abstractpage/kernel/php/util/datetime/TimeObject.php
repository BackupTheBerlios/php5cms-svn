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


using( 'util.Debug' );


/**
 * @package util_datetime
 */
 
class TimeObject extends PEAR
{
	/**
	 * @access public
	 */
	var $time;

	/**
	 * @access public
	 */
	var $hour;
	
	/**
	 * @access public
	 */
	var $min;
	
	/**
	 * @access public
	 */
	var $sec;
	
	/**
	 * @access public
	 */
	var $am_pm;

	/**
	 * @access public
	 */
	var $month;
	
	/**
	 * @access public
	 */
	var $day;
	
	/**
	 * @access public
	 */
	var $year;

	/**
	 * @access public
	 */
	var $debug;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function TimeObject()
	{
		$this->time  = 0;

		$this->hour  = 0;
		$this->min   = 0;
		$this->sec   = 0;
		$this->am_pm = 0;

		$this->month = 0;
		$this->day   = 0;
		$this->year  = 0;

		$this->GetLocalTime();

		$this->debug = new Debug();
		$this->debug->Off();
	}


	/**
	 * @access public
	 */	
	function GetLocalTime()
	{
		$this->time = time();
		$this->FormatTime();
	}

	/**
	 * @access public
	 */
	function FormatTime()
	{
		$this->hour  = date( 'h', $this->time );
		$this->min   = date( 'i', $this->time );
		$this->sec   = date( 's', $this->time );
		$this->am_pm = date( 'A', $this->time );

		$this->month = date( 'm', $this->time );
		$this->day   = date( 'd', $this->time );
		$this->year  = date( 'Y', $this->time );
	}

	/**
	 * @access public
	 */
	function Modify( $hour = 0, $min = 0, $sec = 0, $mon = 0, $day = 0, $year = 0 )
	{
		if ( $hour != 0 )
			$this->hour  += $hour;
			
		if ( $min  != 0 )
			$this->min   += $min;
			
		if ( $sec  != 0 )
			$this->sec   += $sec;
			
		if ( $mon  != 0 )
			$this->month += $mon;
			
		if ( $day  != 0 )
			$this->day   += $day;
			
		if ( $year != 0 )
			$this->year  += $year;
		
		$this->time = mktime(  $this->hour, $this->min, $this->sec, $this->month, $this->day, $this->year );
		$this->FormatTime();
	}

	/**
	 * @access public
	 */
	function Import( $time_string )
	{
		if ( ereg( '(....)-(.+)-(.+) (.+):(.+):(.+)', $time_string, $regs ) || ereg( '(....)/(.+)/(.+) (.+):(.+):(.+)', $time_string, $regs ) )
		{
			// YYYY/MM/DD HH:MM:SS
			$this->time = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
			$this->FormatTime();
			
			return $this->time;
		}
		
		$this->time = mktime( $time_string );
		$this->FormatTime();
		
		return $this->time;
	}

	/**
	 * This is a fix for php3's broken serialize that exhibits the
	 * need to strip functions off of object references.
	 *
	 * @access public
	 */
	function Copy( $broken_object )
	{
		$this->hour	 = $broken_object->hour;
		$this->min	 = $broken_object->min;
		$this->sec	 = $broken_object->sec;
		$this->month = $broken_object->month;
		$this->day	 = $broken_object->day;
		$this->year	 = $broken_object->year;
		$this->time	 = $broken_object->time;
		
		// $this->Modify();
   	}

	/**
	 * @access public
	 */
	function DebugDump( $title = '' )
	{
		$debug_state = $this->debug->debug;
		$this->debug->On();
		
		if ( $title != '' )
			$this->debug->Message( $title );

		$this->debug->Message( 'Month    : ' . $this->month );
		$this->debug->Message( 'Day      : ' . $this->day   );
		$this->debug->Message( 'Year     : ' . $this->year  );
		$this->debug->Message( 'Hour     : ' . $this->hour  );
		$this->debug->Message( 'Min      : ' . $this->min   );
		$this->debug->Message( 'Sec      : ' . $this->sec   );
		$this->debug->Message( 'Am/Pm    : ' . $this->am_pm );
		$this->debug->Message( 'Time     : ' . $this->time  );

		$this->debug->debug = $debug_state;
	}

	/**
	 * @access public
	 */
	function LessThan( $time_obj )
	{
		if ( $this->year > $time_obj->year )
			return false;

		if ( $this->month > $time_obj->year )
			return false;

		if ( $this->day > $time_obj->day )
			return false;

		if ( $this->hour > $time_obj->hour )
			return false;

		if ( $this->min > $time_obj->min )
			return false;

		return true;
	}

	/**
	 * @access public
	 */
	function FormatDateStyle()
	{
		return date( 'M/d/Y h:i:s:A', $this->time );
	}

	/**
	 * @access public
	 */
	function ConvertSecondsToHuman( $sec = '' )
	{
		if ( $sec == '' )
			$sec = $this->time;

		$this->debug->Message( 'Seconds to : ' . $sec );

		$days  = 0;
		$hours = 0;
		$mins  = 0;

		if ( $sec > 86400 )
		{
			$days = floor( $sec / 86400 );
			$sec  = $sec - ( 86400 * $days );
		}

		if ( $sec > 3600 )
		{
			$hours = floor( $sec / 3600 );
			$sec   = $sec - ( 3600 * $hours );
		}

		if ( $sec > 60 )
		{
			$mins  = floor( $sec / 60 );
			$sec   = $sec - ( 60 * $mins );
		}

		$human_time = array();

		if ( $days  != 0 )
		{
			$s = '';
			
			if ( $days > 1 )
				$s = 's';
				
			$human_time[] = $days . ' Day' . $s;
		}

		if ( $hours != 0 )
		{
			$s = '';
			
			if ( $hours > 1 )
				$s = 's';
				
			$human_time[] = $hours . ' Hour' . $s;
		}

		if ( $mins  != 0 )
		{
			$s = '';
			
			if ( $mins > 1 )
				$s = 's';
				
			$human_time[] = $mins . ' Min' . $s;
		}

		if ( $sec != 0 )
		{
			$s = '';
			
			if ( $sec > 1 )
				$s = 's';
				
			$human_time[] = $sec . ' Second' . $s;
		}

		return implode( ' ', $human_time );
	}
} // END OF TimeObject

?>
