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


using( 'util.datetime.Date' );
  
  
/**
 * DateUtil is a helper class to handle Date objects and 
 * calculate date- and timestamps.
 *
 * @package util_datetime
 */

class DateUtil
{
	/**
	 * A class of functions that is designed to make it easy to convert 
	 * different date formats into the Epoch time (seconds since Jan 1, 1970).
	 *
	 * Example: 
	 *
	 * print DateUtil::strToDate( "01/01/01" );
	 *
	 * Formats that it can convert:
	 * "mm/dd/yy"
	 * "mm-dd-yy"
	 * "mm/dd/yyyy"
	 * "yyyy-mm-dd hh:mm:ss" (optional am/pm)
	 * "yyyy/mm/dd hh:mm:ss" (optional am/pm)
	 * HTTP Time ("Thu, 04 Nov 1999 00:00:00 GMT")
	 *
     * @static
     * @access  public
	 */
	function strToDate( $date )
	{
   		// Try YYYY-MM-DD HH:MM:SS (optional am/pm)
   		if ( ereg( "([0-9]{4,4})[-/]([0-9]{1,2})[-/]([0-9]{1,2}) +([0-9]{1,2}):([0-9]{1,2}):?([0-9]{1,2})? *(am|pm)", $date, $regs ) )
		{
			$ampm = $regs[7];
			$sec  = $regs[6];
			$min  = $regs[5];
			$hour = $regs[4];

			// just making sure it's not army time ;)
		 	if ( $ampm == "pm" && $hour <= 12 )
			{
				$nhour = $hour + 12;
				
		    	if ( !$nhour>12 )
					$hour = $nhour;
	  	 	}
			// there is no way this should be bigger than 12
			else if ( $ampm == "am" )
			{
				// error
		    	if ( $hour > 12 )
					return PEAR::raiseError( "Invalid date format." );
		 	}
			// null, so we don't know... probably a 24 hour clock
			else if ( $ampm == "" )
			{
		    	if( $hour > 12 )
					$ampm = "pm";
				else
					$ampm = "am";
			}
	 
		 	$day  = $regs[3];
		 	$mon  = $regs[2];
		 	$year = $regs[1];
   		}
   		// Try YYYY-MM-DD or YYYY/MM/DD
   		else if ( ereg( "([0-9]{4,4})[-/]([0-9]{1,2})[-/]([0-9]{1,2})", $date, $regs ) )
		{
	 		$day  = $regs[3];
	 		$mon  = $regs[2];
	 		$year = $regs[1];
		}
		// Try MM-DD-YY or MM/DD/YY or MM/DD/YYYY or MM-DD-YYYY
		else if ( ereg( "([01][012])[-/]([0-3][0-9])[-/]([0-9]{2,4})", $date, $regs ) )
		{
	 		$day  = $regs[2];
	 		$mon  = $regs[1];
	 		$year = $regs[3];
		}
		// Try HTTP Time (Thu, 04 Nov 1999 00:00:00 GMT)
		else if ( ereg( "[a-zA-Z]{3,3}, +([0-3][0-9]) +(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) +([12][0-9][0-9][0-9]) +([012][0-9]):([0-5][0-9]):([0-5][0-9]) +GMT", $date, $regs ) )
		{
			$months = array(
				"Jan" => 1,
				"Feb" => 2,
				"Mar" => 3,
			  	"Apr" => 4,
				"May" => 5,
				"Jun" => 6,
			  	"Jul" => 7,
				"Aug" => 8,
				"Sep" => 9,
			  	"Oct" => 10,
				"Nov" => 11,
				"Dec" => 12
			);

	 		$hour = $regs[4];
	 		$min  = $regs[5];
	 		$sec  = $regs[6];
	 		$day  = $regs[1];
	 		$mon  = $months[$regs[2]];
	 		$year = $regs[3];
   		}

		if ( !$year )
			return PEAR::raiseError( "Invalid date format." );

		if ( strlen( $year ) == 2 )
		{
			if ( $year > 25 && $year <= 99 )
				$year = 1900 + $year;
			else
				$year = 2000 + $year;
		}

		if ( !isset( $hour ) )
			$hour = 0;
			
		if ( !isset( $min  ) )
			$min = 0;
			
		if ( !isset( $sec  ) )
			$sec = 0;
			
		if ( !isset( $mon  ) )
			$mon = 0;
			
		if ( !isset( $day  ) )
			$day = 0;
			
		if ( !isset( $year ) )
			$year = 0;

		// print "$hour,$min,$sec,$mon,$day,$year";
		return mktime( $hour, $min, $sec, $mon, $day, $year );
	}
	
    /**
     * Returns a Date object which represents the date at
     * the given date at midnight.
     *
     * @static
     * @access  public
     * @param   &Date
     * @return  &Date
     */
    function &getMidnight( &$date ) 
	{
      	return new Date( mktime(
			23,
			59,
			00,
			$date->getMonth(),
			$date->getDay(),
			$date->getYear()
      	) );
    }
    
    /**
     * Gets the last day of the month.
     *
     * @static
     * @access  public
     * @param   &Date
     * @return  &Date
     */
    function &getLastOfMonth( &$date ) 
	{
      	return new Date( mktime(
        	$date->getHours(),
        	$date->getMinutes(),
        	$date->getSeconds(),
        	$date->getMonth() + 1,
        	0,
        	$date->getYear()
      	) );
    }
    
    /**
     * Gets the first day of the month.
     *
     * @static
     * @access  public
     * @param   &Date
     * @return  &Date
     */
    function &getFirstOfMonth( &$date ) 
	{
      	return new Date( mktime(
        	$date->getHours(),
        	$date->getMinutes(),
        	$date->getSeconds(),
        	$date->getMonth(),
        	1,
        	$date->getYear()
      	) );
    }
    
    /**
     * Adds a positive or negative amount of months.
     *
     * @static
     * @access  public
     * @param   &Date
     * @param   int count
     * @return  &Date
     */
    function addMonths( &$date, $cnt = 1 ) 
	{
      	return new Date( mktime(
        	$date->getHours(),
        	$date->getMinutes(),
        	$date->getSeconds(),
        	$date->getMonth() + $cnt,
        	$date->getDay(),
        	$date->getYear()
      	) );
    }
    
    /**
     * Adds a positive or negative amount of days.
     *
     * @static
     * @access  public
     * @param   &Date
     * @param   int count
     * @return  &Date
     */
    function addDays( &$date, $cnt = 1 ) 
	{
      	return new Date( mktime(
        	$date->getHours(),
        	$date->getMinutes(),
        	$date->getSeconds(),
        	$date->getMonth(),
        	$date->getDay() + $cnt,
        	$date->getYear()
      	) );
    }
    
    /**
     * Adds a positive or negative amount of hours.
     *
     * @static
     * @access  public
     * @param   &Date
     * @param   int count
     * @return  &Date
     */
    function addHours( &$date, $cnt = 1 ) 
	{
      	return new Date( mktime(
        	$date->getHours() + $cnt,
        	$date->getMinutes(),
        	$date->getSeconds(),
        	$date->getMonth(),
        	$date->getDay(),
        	$date->getYear()
      	) );
    }
    
    /**
     * Adds a positive or negative amount of minutes.
     *
     * @static
     * @access  public
     * @param   &Date
     * @param   int count
     * @return  &Date
     */
    function addMinutes( &$date, $cnt = 1 ) 
	{
      	return new Date( mktime(
        	$date->getHours(),
        	$date->getMinutes() + $cnt,
        	$date->getSeconds(),
        	$date->getMonth(),
        	$date->getDay(),
        	$date->getYear()
      	) );
    }
	
	/**
	 * @static
	 * @access public
	 */
	function weekDaysISO( $yday, $wday )
	{
		return $yday - ( ( $yday - $wday + 382 ) % 7 ) + 3;
	}
	
	/**
	 * @static
	 * @access public
	 */
	function getWeekNumber( $timestamp )
	{
        $d    = getdate( $timestamp );
        $days = DateUtil::weekDaysISO( $d["yday"], $d["wday"] );

        if ( $days < 0 )
		{
			$d["yday"] += 365 + DateUtil::isLeapYear( --$d["year"] );
			$days = DateUtil::weekDaysISO( $d["yday"], $d["wday"]  );
        }
		else
		{
			$d["yday"] -= 365 + DateUtil::isLeapYear( $d["year"] );
			$d2 = DateUtil::weekDaysISO( $d["yday"], $d["wday"]  );
			
			if ( 0 <= $d2 )
			{
				// $d["year"]++;
				$days = $d2;
			}
		}

        return (int)( $days / 7 ) + 1;
	}
	
	/**
	 * Generates a fully-compliant RFC 822 date string, including correct timezone offset.
	 *
	 * @static
	 * @access public
	 */
	function getRFCDate()
	{ 
		// translated from imap-4.7c/src/osdep/unix/env_unix.c   
   		$tn     = time( 0 );
   		$zone   = gmdate( "H", $tn ) * 60 + gmdate( "i", $tn ); 
		$julian = gmdate( "z", $tn ); 
		$t      = getdate( $tn ); 
		$zone   = $t[hours] * 60 + $t[minutes] - $zone; 

		// julian can be one of: 
		//  36x  local time is December 31, UTC is January 1, offset -24 hours 
		//    1  local time is 1 day ahead of UTC, offset +24 hours 
		//    0  local time is same day as UTC, no offset 
		//   -1  local time is 1 day behind UTC, offset -24 hours 
		// -36x  local time is January 1, UTC is December 31, offset +24 hours  
		if ( $julian = $t[yday] - $julian )
			$zone += ( ( $julian < 0 ) == ( abs( $julian ) == 1 ) )? -24 * 60 : 24 * 60; 

		return date( 'D, d M Y H:i:s ', $tn ) . sprintf( "%03d%02d", $zone / 60, abs( $zone ) % 60 ) . " (" . strftime( "%Z" ) . ")"; 
	}
	
	/**
	 * Add a readable date to a timestamp (i.e. time() + "+ 1 day - 1 year" ).
	 *
	 * @param  $timestamp Integer UNIX timestamp
	 * @param  $str String time periods to add
	 * @return integer new timestamp
	 * @access public
	 * @static
	 */
	function dateMath( $timestamp, $str )
	{
		$date_ops = explode( " ", $str );
		$scratch_date = getdate( $timestamp );
		$op  = "+";
		$qty = 1;
		
		for ( $i = 0; $i < count( $date_ops ); $i++ )
		{
			$t = strtolower( trim( $date_ops[$i] ) );
			
			if ( is_numeric( $t ) )
			{
				$qty = (int)$t;
			}
			else
			{
				switch ( $t )
				{
					case "+":
				
					case "-":
						$op = $t;
						break;

					case "sec":

					case "secs":

					case "second":

					case "seconds":
						$chp = 'seconds';
						break;

					case "min":

					case "mins":

					case "minute":

					case "minutes":
						$chp = 'minutes';
						break;

					case "hour":

					case "hours":
						$chp = 'hours';
						break;

					case "day":

					case "days":
						$chp = 'mday';
						break;

					case "week":

					case "weeks":
						$chp = 'mday';
						$qty *= 7;
						break;

					case "month":

					case "months":
						$chp = 'mon';
						break;

					case "year":

					case "years":
						$chp = 'year';
						break;
				}
				
				if ( $chp )
				{
					eval( "\$scratch_date['$chp'] = \$scratch_date['$chp'] $op $qty;" );
					$qty = 1;
					$chp = "";
				}
			}
		}
		
		return mktime( 
			$scratch_date['hours'], 
			$scratch_date['minutes'], 
			$scratch_date['seconds'], 
			$scratch_date['mon'],
			$scratch_date['mday'], 
			$scratch_date['year'] 
		);
	}
	
	/**
	 * A function to return a string of when the file was last updated in a slightly
	 * fuzzy way. Takes a unix timestamp as input.
	 *
     * @static
     * @access  public
	 */
	function fuzzyTime( $time )
	{
		$now = time();
		
		// sod = start of day :)
		$sod     = mktime( 0, 0, 0, date( "m", $time ), date( "d", $time ), date( "Y", $time ) );
		$sod_now = mktime( 0, 0, 0, date( "m", $now  ), date( "d", $now  ), date( "Y", $now  ) );
		
		// check 'today'
		if ( $sod_now == $sod )
			return "today at " . date( "g:ia", $time );
		
		// check 'yesterday'
		if ( ( $sod_now - $sod ) <= 86400 )
			return "yesterday at " . date( "g:ia", $time );
		
		// give a day name if within the last 5 days
		if ( ( $sod_now - $sod ) <= ( DATEOBJECT_ONE_DAY * 5 ) )
			return date( "l \a\\t g:ia", $time );
		
		// miss off the year if it's this year
		if ( date( "Y", $now ) == date( "Y", $time ) )
			return date( "M j \a\\t g:ia", $time );
		
		// return the date as normal
		return date( "M j, Y \a\\t g:ia", $time );
	}
	
	/**
	 * Returns the first day of the month.
	 *
	 * @param  int Timestamp
	 * @return int Timestamp of the first day of month
	 * @access public
	 * @static
	 */
	function getFirstDayOfMonth( $stamp )
	{
		$month = gmdate( "m", $stamp );
		$year  = gmdate( "Y", $stamp );
		
		return gmmktime( 0, 0, 0, $month, 1, $year );
	}

	/**
	 * Returns the last day of the month.
	 *
	 * @param  int Timestamp
	 * @return int Timestamp of the last day of month
	 * @access public
	 * @static
	 */
	function getLastDayOfMonth( $stamp )
	{
		$month = intval( date( "m", $stamp ) ) + 1;
		$year  = gmdate( "Y", $stamp );
		
		return gmmktime( 23, 59, 59, $month, 0, $year );
	}

	/**
	 * Returns the first day of the next month.
	 *
	 * @param  int Timestamp
	 * @return int Timestamp of the first day of next month
	 * @access public
	 * @static
	 */
	function getNextMonth( $stamp )
	{
		$month = intval( date( "m", $stamp ) ) + 1;
		$year  = gmdate( "Y", $stamp );
		
		return gmmktime( 0, 0, 0, $month, 1, $year );
	}

	/**
	 * Returns the first day of the next month.
	 *
	 * @param  int Timestamp of first date
	 * @param  int Timestamp of second date
	 * @return float Number of days between two days
	 * @access public
	 * @static
	 */
	function daysBetween( $date1, $date2 )
	{
		$days = ( $date2 - $date1 ) / 86400;
		return $days;
	}
	
	/**
	 * Preset date styles:
	 *
	 * 1 - Friday, June 15, 01
	 * 2 - June 15, 01 
	 * 3 - Friday, 15 June, 01
	 * 4 - 15 June, 01
	 * 5 - 06/15/01
	 * 6 - 06/15/		(?)
	 * 7 - 15/06/01
	 * 8 - 15/06/		(?)
	 *
	 * @access public
	 * @static
	 */
	function textDate( $datestyle )
	{
		switch ( $datestyle )
		{
			case 1 :
				$output = date( "l, F d, y", time() );
				break;
	
			case 2 :
				$output = date( "F d, y", time() );
				break;
	
			case 3 :
				$output = date( "l, d F, y", time() );
				break;
	
			case 4 : 
				$output = date( "d F, y" );
				break;
	
			case 5 :
				$output = date("m/d/y");
				break;
	
			case 6 :
				$year   = substr( date( "y" ), 2, 2 );
				$output = date( "m/d/$year" );
				break;
	
			case 7 :
				$output = date( "d/m/y" );
				break;
	
			case 8 :
				$year   = substr( date("y"), 2, 2 );
				$output = date( "d/m/$year" );
				break;
			
			default :
				$output = date( "l, F d, y", time() );
				break;
		}
	
		return $output;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function dateAssemble( $datevar, $array_index = -1 )
	{
		global ${$datevar . "_m"};
		global ${$datevar . "_d"};
		global ${$datevar . "_y"};

		if ( $array_index == -1 )
		{
   		 	$m = ${$datevar . "_m"};
    		$d = ${$datevar . "_d"};
    		$y = ${$datevar . "_y"};
 	 	}
		else
		{
    		$m = ${$datevar . "_m"}[$array_index];
    		$d = ${$datevar . "_d"}[$array_index];
    		$y = ${$datevar . "_y"}[$array_index];
		}
  
		// ensure proper format
		if ( strlen( $m ) == 1 )
			$m = "0" . $m;
	
		if ( strlen( $d ) == 1 )
			$d = "0" . $d;

		// return the composited string
		return $y . "-" . $m . "-" . $d;
	}

	/**
	 * @access public
	 * @static
	 */
	function dateEntry( $datevar, $epoch = 1900, $format = "mdy", $array_index = -1 )
	{
		global $$datevar;
		global ${$datevar . "_m"};
		global ${$datevar . "_d"};
		global ${$datevar . "_y"};

		// move into local vars
		if ( ( $array_index + 0 ) == -1 )
		{
    		$w = $$datevar;
    		$m = ${$datevar . "_m"};
    		$d = ${$datevar . "_d"};
    		$y = ${$datevar . "_y"};
    		$suffix = "";
  		}
		else
		{
    		$w = ${$datevar}[$array_index];
    		$m = ${$datevar . "_m"}[$array_index];
    		$d = ${$datevar . "_d"}[$array_index];
    		$y = ${$datevar . "_y"}[$array_index];
    		$suffix = "[]";
  		}
	
		// if the whole thing is there, split into $m,$d,$y
		if ( !empty( $w ) && ( empty( $m ) || empty( $d ) || empty( $y ) ) )
		{
   		 	$y = substr( $w, 0, 4 );
   		 	$m = substr( $w, 5, 2 );
    		$d = substr( $w, 8, 2 );
  		}
		else if ( empty( $y ) && empty( $m ) && empty( $d ) )
		{
    		$y = date( "Y" ) + 0;
    		$m = date( "m" ) + 0;
    		$d = date( "d" ) + 0;
		}

		// set boundaries
		$starting_year = $epoch;
		$ending_year = date( "Y" ) + 10;

		// legacy dates check
		if ( ( $y > 1800 ) && ( $y < $starting_year ) )
			$starting_year = $y;
		
		if ( ( $y > 1800 ) && ( $y > $ending_year   ) )
			$ending_year   = $y;

	
		// form individual parts
	
		$month_part = "\n<SELECT NAME=\"".$datevar."_m$suffix\">
    	<OPTION VALUE=\"00\" ".( ($m == 0) ? "SELECTED" : "" ).">NONE";
	
		for ( $i = 1; $i <= 12; $i++ )
		{
    		$prefix = ( ( $i < 10 )? "0" : "" );
    		$month_part .= "\n<OPTION VALUE=\"".( ($i<10) ? "0" : "" ).$i."\" " . ( ( $i == $m ) ? "SELECTED" : "" ) . ">" . date( "M", mktime( 0, 0, 0, $i, 1, 1 ) );
  		}

		$month_part .= "\n</SELECT>\n";
	
		$day_part = "\n<SELECT NAME=\"".$datevar."_d$suffix\">
    	<OPTION VALUE=\"00\" ".( ($d == 0) ? "SELECTED" : "" ).">NONE";

		for ( $i = 1; $i <= 31; $i++ )
		{
    		$prefix = ( ( $i < 10 )? "0" : "" );
    		$day_part .= "\n<OPTION VALUE=\"".( ($i<10) ? "0" : "" ).$i."\" " .
      			( ( $i == $d )? "SELECTED" : "" ).">" .
 				( ( $i < 10  )? "0" : "" ).$i;
		}

		$day_part .= "\n</SELECT>\n";
  
		$year_part = "\n<SELECT NAME=\"".$datevar."_y$suffix\">
	   	<OPTION VALUE=\"0000\" ".( ($d == 0) ? "SELECTED" : "" ).">NONE";

		for ( $i = $starting_year; $i <= $ending_year; $i++ )
			$year_part .= "\n<OPTION VALUE=\"".$i."\" " . ( ( $i == $y )? "SELECTED" : "" ).">$i";

		$year_part .= "\n</SELECT>\n";

  
	  	// choose date format and return
		switch ( $format )
		{
    		case "ymd" :
      			return $year_part . $month_part . $day_part;
				break;
			
    		case "dmy" :
      			return $day_part . $month_part . $year_part;
				break;
			
    		case "mdy" :
		
			default :
      			return $month_part . $day_part . $year_part;
				break;
		}
	}

	/** 
	 * @access public
	 * @static
	 */
	function dateVars( $varname )
	{
		return array( $varname, $varname . "_m", $varname . "_d", $varname . "_y" );
	}

	/**
	 * @access public
	 * @static
	 */
	function dateDiff( $begin_date, $end_date = "" )
	{
		if ( empty( $end_date ) )
			$end_date = date( "Y-m-d" );

		$begin_y = substr( $begin_date, 0, 4 ) + 0;
		$begin_m = substr( $begin_date, 5, 2 ) + 0;
		$begin_d = substr( $begin_date, 8, 2 ) + 0;
		$end_y   = substr( $end_date,   0, 4 ) + 0;
		$end_m   = substr( $end_date,   5, 2 ) + 0;
		$end_d   = substr( $end_date,   8, 2 ) + 0;

		if ( ( $begin_y > $end_y ) || ( ( $begin_y == $end_y ) && ( $begin_m > $end_m ) ) || ( ( $begin_y == $end_y ) && ( $begin_m == $end_m ) && ( $begin_d > $end_d ) ) )
		{
    		// switch the dates
    		$t_y     = $begin_y;
			$t_m     = $begin_m;
			$t_d     = $begin_d;
    		$begin_y = $end_y;
			$begin_m = $end_m; 
			$begin_d = $end_d;
    		$end_y   = $t_y;
			$end_m   = $t_y;
			$end_d   = $t_d;
		}

		// determine difference in years
		$year_diff = $end_y - $begin_y;

		// determine difference in months
		$month_diff = $end_m - $begin_m;

		// perform roll overs for year
		if ( $month_diff < 0 )
		{
    		$month_diff += 12;
    		$year_diff--; // decrement from year
		}

		// determine difference in months
		$day_diff = $end_d - $begin_d;

		// perform roll overs for month
		if ( $month_diff < 0 )
		{
			$day_diff += 31; // KLUDGE!! KLUDGE!!
			$month_diff--;   // decrement from month
		}

		// return as a list
		return array ( $year_diff, $month_diff, $day_diff );
	}

	/**
	 * @access public
	 * @static
	 */
	function dateDiffDisplay( $begin_date, $end_date = "", $year_text = "year(s)", $month_text = "month(s)", $day_text = "day(s)" )
	{
		// grab the difference			    
		list( $y, $m, $d ) = DateUtil::dateDiff( $begin_date, $end_date );

		// handle born today
		if ( ( $y == 0 ) && ( $m == 0 ) && ( $d == 0 ) )
    		return "0 " . $day_text;

		// empty buffer
		$buffer = "";

		// add year(s)
		if ($y > 0)
			$buffer .= ( $y + 0 ) . " " . $year_text . " ";

		// add month(s) if years < 2
		if ( ( $m > 0 ) && ( $y < 2 ) )
			$buffer .= ( $m + 0 ) . " " . $month_text . " ";

		// add day(s) if no years at all and less than 6 months
		if ( ( $d > 0 ) && ( $y == 0 ) && ( $m < 6 ) )
			$buffer .= ( $d + 0 ) . " " . $day_text;

		// return buffer
		return $buffer;
	}

	/**
	 * @access public
	 * @static
	 */
	function isLeapYear( $year )
	{
		if ( ( ( $year % 4 ) == 0 && ( $year % 100 ) != 0 ) || ( $year % 400 ) == 0 )
			return true;
		else
			return false;
	}
} // END OF DateUtil

?>
