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


using( 'util.Util' );


/**
 * Very accurate Atomic Time class
 * Gets the time from NIST, accurate to 0.01 sec
 * The NIST time is Official U.S time and is extremely
 * accurate and precise and is a National Standard Time
 *
 * Usage:
 *
 *   Nightly cron job -
 *		You can run this script as nightly cron job to set the 
 *		system date and time. Login as root and do 'crontab -e'
 *		and put a line like
 *     14,15 0,5,12 * * * (/usr/bin/php /www/html/atomictime.php >> $HOME/cron.out 2>&1 ; date >> $HOME/cron.out)
 *
 *   Console mode - At unix bash prompt type
 * 		bash$ php atomictime.php
 *
 *
 * Browser Example:
 *
 *   $tm = new AtomicTime();
 *   $tm->display();
 *
 *
 * Atomic time resources:
 *
 *	Gets the most accurate time from 
 *		http://nist.time.gov  (IP 132.163.4.213)
 *		http://nist.time.gov/timezone.cgi?Central/d/-6
 *		http://nist.time.gov/timezone.cgi?Pacific/d/-8
 *
 *		http://www.atomictime.net/time_tel.html?4  (all time zones)
 *		http://www.atomictime.net/time_tel.html?2
 *
 *		http://www.worldtimeserver.com  (whole world time)
 *		http://www.worldtimeserver.com/time.asp?locationid=US-TX
 *
 * @package util_datetime
 */
 
class AtomicTime extends PEAR
{
	/**
	 * @access public
	 */
	var $current_time;
	
	/**
	 * @access public
	 */
	var $current_date;
	
	/**
	 * @access public
	 */
	var $current_year;
	
	/**
	 * @access public
	 */
	var $current_month;
	
	/**
	 * @access public
	 */
	var $current_day;
	
	/**
	 * default is "yes" - But run as root user
	 * @access public
	 */
	var $sync_atomic = 'Y';

	/**
	 * @access private
	 */
	var $_page_contents;
	
	/**
	 * @access private
	 */
	var $_max_urls;
	
	/**
	 * @access private
	 */
	var $_which_system;

	/**
	 * @access private
	 */
	var $_time_url = array();
	
	/**
	 * @access private
	 */
	var $_time_page = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function AtomicTime()
	{
		//$this->_time_url[0] = "http://nist.time.gov/timezone.cgi?Central/d/-6";
		$this->_time_url[0]   = "http://132.163.4.213/timezone.cgi?Central/d/-6";
		//$this->_time_url[1] = "http://www.atomictime.net/time_tel.html?4";
		//$this->_time_url[2] = "http://www.worldtimeserver.com/time.asp?locationid=US-TX";
		
		$this->_max_urls = count( $this->_time_url );
		$this->_open_timeurl();
	}

	
	/**
	 * @access public
	 */
	function display()
	{
		print "\n<b>Time:</b> $this->current_time<br>";
		print "\n<b>Date:</b> $this->current_date<br>";
	
		// If you want set system time - run a root.
		if ( $this->sync_atomic == "Y" )
			$this->set_system_time();
	}

	/**
	 *Will set the unix system's date and time - But run as root.
	 *
	 * @access public
	 */
	function set_system_time()
	{
		$this->AtomicTime(); // get current time before doing set time

		// Make sure the date and time got are correct values, because you
		// are going to set the cpu's system time !!
		$this->current_year = intval( substr( $this->current_date, strlen( $this->current_date ) - 4 ) );

		$wx_start = strpos(  $this->current_date, ',' ); // see also strrpos()
		$wx_end   = strrpos( $this->current_date, ',' ); // see also strrpos()
		
		$this->current_month = substr( $this->current_date, $wx_start + 1, $wx_end - $wx_start - 3 );
		$this->month_to_numbers();
	
		$this->current_day = substr( $this->current_date, strlen( $this->current_date ) - 8, 2 );
		
		if ( $this->current_year < 2000 )
		{
			print "\n<br>Year is : $this->current_year . \n";
			print "\n<br>Something is going wrong! Check the url\n";
		
			return;
		}
	
		if ( $this->current_month < 1 || $this->current_month > 12 )
		{
			print "\n<br>Month is : $this->current_month . \n";
			print "\n<br>Something is going wrong! Check the url\n";
		
			return;
		}
		
		if ( $this->current_day < 1 || $this->current_day > 31 )
		{
			print "\n<br>Day is : $this->current_day . \n";
			print "\n<br>Something is going wrong! Check the url\n";
		
			return;
		}

		// Check if the NIST site is working
		if ( ( trim( $this->current_time ) != "") && ( trim( $this->current_date ) != "" ) )
		{
			// The NIST site is working, now then get the CURRENT ACCURATE time
			$this->current_time = "";
			$this->current_date = "";
		
			print "\n\n<br><br><b>Localhost Server time: </b>";
			
			// Reduce the number of commands between get time and set time, in order
			// to reduce the time lag between get and set....
			if ( Util::getOS() == "win" )
			{
				// For MS Windows enter command as "c\:>time hh:mm:ss.ss" and "c\:>date mm-dd-yyyy"
				$this->_get_accurate_time( 0, false );
				
				if ( $this->current_time = "" )
					return;
					
				$this->_get_accurate_time( 0, true ); // repeat command to get more accurate time
				$syscmd = "time " . $this->current_time;
				system( $syscmd, $return_status );
			
				if (!$return_status)
					print "\n\nThe system time synchronized to Atomic clock \n\n";
				else
					print "\n\nError: The system command failed. \nYour command was $syscmd \n\n";
			
				$syscmd = "date " . $this->current_month . "-" . $this->current_day . "-" . $this->current_year;
				system( $syscmd, $return_status );
			
				if ( !$return_status )
					print "\n\nThe system date and time synchronized to Atomic clock \n\n";
				else
					print "\n\nError: The system command failed. \nYour command was $syscmd \n\n";
			}
			else if ( Util::getOS() == "nix" )
			{
				$this->_get_accurate_time( 0, false );
			
				if ( $this->current_time = "" )
					return;
			
				$syscmd = "date +'%A, %B %e, %Y %T'  -s '" . $this->current_date . " ";
				$this->_get_accurate_time( 0, true ); // repeat command to get more accurate time
				$syscmd .= $this->current_time . "'";
				system( $syscmd, $return_status );
				
				if ( !$return_status )
					print "\n\nThe system date and time synchronized to Atomic clock \n\n";
				else
					print "\n\nError: The system command failed. \nYour command was $syscmd \n\n";
			}
		}
	}

	/**
	 * Converts month "January" to number.
	 *
	 * @access public
	 */
	function month_to_numbers()
	{
		$month = strtoupper( trim( $this->current_month ) );

		if ( $month == "JANUARY" )
			$this->current_month = 1;
		else if ( $month == "FEBRUARY" )
			$this->current_month = 2;
		else if ( $month == "MARCH" )
			$this->current_month = 3;
		else if ( $month == "APRIL" )
			$this->current_month = 4;
		else if ( $month == "MAY" )
			$this->current_month = 5;
		else if ( $month == "JUNE" )
			$this->current_month = 6;
		else if ( $month == "JULY" )
			$this->current_month = 7;
		else if ( $month == "AUGUST" )
			$this->current_month = 8;
		else if ( $month == "SEPTEMBER" )
			$this->current_month = 9;
		else if ( $month == "OCTOBER" )
			$this->current_month = 10;
		else if ( $month == "NOVEMBER" )
			$this->current_month = 11;
		else if ( $month == "DECEMBER" )
			$this->current_month = 12;
	}

	/**
	 * Converts month number to string "January".
	 *
	 * @access public
	 */
	function month_to_string()
	{
		$month = intval( $this->current_month );

		switch ( $month )
		{
			case 1:
				$month = "JANUARY";
				break;
		
			case 2:
				$month = "FEBRUARY";
				break;
		
			case 3:
				$month = "MARCH";
				break;
		
			case 4:
				$month = "APRIL";
				break;
		
			case 5:
				$month = "MAY";
				break;
			
			case 6:
				$month = "JUNE";
				break;
		
			case 7:
				$month = "JULY";
				break;
		
			case 8:
				$month = "AUGUST";
				break;
			
			case 9:
				$month = "SEPTEMBER";
				break;
			
			case 10:
				$month = "OCTOBER";
				break;
		
			case 11:
				$month = "NOVEMBER";
				break;
		
			case 12:
				$month = "DECEMBER";
				break;
		
			default:
				$month = "NO_MATCH!!";
				break;
		}
		
		// $this->current_month = $month;
		$this->current_month = strtolower( $month ); // if you want all lowercase
	}
	
	
	// private methods

	/**
	 * @access private
	 */
	function _open_timeurl()
	{
		for ( $tmsource = 0; $tmsource < $this->_max_urls; $tmsource++ )
		{
			for ( $counter = 0; $counter < 4; $counter++ )  // make 3 attempts
			{
				$this->_get_accurate_time( $tmsource, false );
			
				if ( $this->current_time != "" ) 
					break;
			}
		
			if ( trim( $this->_page_contents ) != '' )
				break;
		}
	}

	/**
	 * @access private
	 */
	function _get_accurate_time( $tmsource, $time_only )
	{
		$this->_time_page[$tmsource] = $this->_time_url[$tmsource];
		$handle = fopen( $this->_time_page[$tmsource], "r" );
		 
		if ( $handle ) 
		{
			$this->_page_contents = fread( $handle, 2000 ); // fread is faster than file()??
			
			if ( $wx_start = strpos( $this->_page_contents, "Right now, the official U.S. time is:" ) )
			{
				// Find content
				$wx_content = substr( $this->_page_contents, $wx_start + 193, 100 ); // trial prints 193, 100

				// Find current time
				$this->current_time = substr( $wx_content, 0, 8 ); // get 0,8 by trial prints  

				if ( $time_only )
					return; // return immdly for greater accuracy

				// Find current date
				$this->current_date = substr( $wx_content, 65 );
				$wx_end = strpos( $this->current_date, '<' );  // see also strrpos()
				$this->current_date = substr( $this->current_date, 0, $wx_end );
			}
		}
	}
} // END OF AtomicTime

?>
