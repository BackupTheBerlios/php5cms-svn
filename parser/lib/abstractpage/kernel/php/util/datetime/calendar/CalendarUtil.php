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

 
using( 'util.Date' );


define( 'CAL_SEC_HOUR',   3600 );
define( 'CAL_SEC_DAY',   86400 );
define( 'CAL_SEC_WEEK', 604800 );
  
define( 'CAL_DST_EU',   0x0000 );
define( 'CAL_DST_US',   0x0001 );
  
  
/**
 * Static helper functions.
 *
 * @package util_datetime_calendar
 */

class CalendarUtil
{
    /**
     * Calculates start of DST (daylight savings time).
     *
     * This is the last Sunday of March for Europe, the first Sunday of 
     * April in the U.S.
     *
     * @static
     * @access  public
     * @param   int year default -1 Year, defaults to current year
     * @param   int method default CAL_DST_EU Method to calculate (CAL_DST_EU|CAL_DST_US)
     * @return  &util.datetime.Date
     */
    function &dstBegin( $year = -1, $method = CAL_DST_EU ) 
	{
      	if ( -1 == $year ) 
			$year = date( 'Y' );
      
	  	$i = 0;
      	$ofs = ( $method == CAL_DST_US )? 1 : -1;
      
	  	do 
		{
        	$w  = date( 'w', $m = mktime( 0, 0, 0, 4, $i, $year ) );
        	$i += $ofs;
      	} while ( $w > 0 );
      
	  	return new Date( $m );
    }
  
    /**
     * Calculates end of DST (daylight savings time).
     * This is the last Sunday of October.
     *
     * @static
     * @access  public
     * @param   int year default -1 Year, defaults to current year
     * @return  &util.datetime.Date
     */
    function &dstEnd( $year = -1 ) 
	{
      	if ( -1 == $year ) 
			$year = date( 'Y' );
      
	  	$i = 0;
      	
		do 
		{
        	$w = date( 'w', $m = mktime( 0, 0, 0, 11, $i--, $year ) );
      	} while ( $w > 0 );
      
	  	return new Date( $m );
    }
  
    /**
     * Calculates the amount of workdays between to dates. Workdays are 
     * defined as Monday through Friday.
     *
     * This method takes an optional argument, an array of the following
     * form:
     *
     * <code>
     *   $holidays[gmmktime(...)] = true;
     * </code>
     *
     * @access  public
     * @param   &util.datetime.Date start
     * @param   &util.datetime.Date end
     * @param   array holidays default array() holidays to be included in calculation
     * @return  int number of workdays
     */
    function workdays( &$start, &$end, $holidays = array() ) 
	{
      	$s = $start->getTime();
      	$e = $end->getTime();

      	// For holidays, we have to compare to midnight
      	// else, don't calculate this
      	if ( !empty( $holidays ) ) 
			$s -= $s % CAL_SEC_DAY;
      
      	// Is there a more intelligent way of doing this?
      	$diff = floor( ( $e - $s ) / CAL_SEC_DAY );
      
	  	for ( $i = $s; $i <= $e; $i+= CAL_SEC_DAY )
        	$diff -= ( ( date( 'w', $i ) + 6 ) % 7 > 4 || isset( $holidays[$i] ) );
      
      	return $diff + 1;
    }
    
    /**
     * Return midnight of a given date.
     *
     * @static
     * @access  public
     * @param   &util.datetime.Date date
     * @return  &util.datetime.Date
     */
    function &midnight( &$date ) 
	{
      	return new Date( mktime( 0, 0, 0, $date->mon, $date->mday, $date->year ) );
    }
    
    /**
     * Return beginning of month for a given date. E.g., given a date
     * 2003-06-08, the function will return 2003-06-01 00:00:00.
     *
     * @static
     * @access  public
     * @param   &util.datetime.Date date
     * @return  &util.datetime.Date
     */
    function &monthBegin( &$date ) 
	{
      	return new Date( mktime( 0, 0, 0, $date->mon, 1, $date->year ) );
    }

    /**
     * Return end of month for a given date. E.g., given a date
     * 2003-06-08, the function will return 2003-06-30 23:59:59.
     *
     * @static
     * @access  public
     * @param   &util.datetime.Date date
     * @return  &util.datetime.Date
     */
    function &monthEnd( &$date ) 
	{
      	return new Date( mktime( 23, 59, 59, $date->mon + 1, 0, $date->year ) );
    }

    /**
     * Helper method for CalendarUtil::week.
     *
     * @static
     * @access  private
     * @param   int stamp
     * @param   int year
     * @return  int
     */
    function caldiff( $stamp, $year ) 
	{
      	$d4 = mktime( 0, 0, 0, 1, 4, $year );
      	return floor( 1.05 + ( $stamp - $d4 ) / CAL_SEC_WEEK + ( ( date( 'w', $d4 ) + 6 ) % 7 ) / 7 );
    }
  
    /**
     * Returns calendar week for a day.
     *
     * @static
     * @access  public
     * @param   &util.datetime.Date date
     * @return  int calendar week
     * @link    http://www.salesianer.de/util/kalwoch.html 
     */
    function week( &$date ) 
	{
      	$d = $date->getTime();
      	$y = $date->year + 1;
      
	  	do 
		{
        	$w = CalendarUtil::caldiff( $d, $y );
        	$y--;
      	} while ( $w < 1 );

      	return (int)$w;
    }
    
    /**
     * Get first of advent for given year.
     *
     * @static
     * @access  public
     * @param   int year default -1 year, defaults to this year
     * @return  &util.datetime.Date for date of the first of advent
     * @link    http://www.salesianer.de/util/kalfaq.html
     */
    function &advent( $year = -1 ) 
	{
      	if ( -1 == $year ) 
			$year = date( 'Y' );
     
      	$s = mktime( 0, 0, 0, 11, 26, $year );
      
	  	while ( 0 != date( 'w', $s ) )
        	$s += CAL_SEC_DAY;
      
      	return new Date( $s );
    }
    
    /**
     * Get easter date for given year.
     *
     * @static
     * @access  public
     * @param   int year default -1 Year, defaults to this year
     * @return  &util.datetime.Date date for Easter date
     * @link    http://www.koenigsmuenster.de/rsk/epakte.htm
     * @link    http://www.salesianer.de/util/kalfaq.html
     * @see     php://easter-date#user_contrib
     */
    function &easter( $year = -1 ) 
	{
      	if ( -1 == $year ) 
			$year = date( 'Y' );
      
      	$g = $year % 19;
      	$c = (int)( $year / 100 );
      	$h = (int)( $c - ( $c / 4 ) - ( ( 8 * $c + 13 ) / 25 ) + 19 * $g + 15 ) % 30;
      	$i = (int)$h - (int)( $h / 28 ) * ( 1 - (int)( $h / 28 ) * (int)( 29 / ( $h + 1 ) ) * ( (int)( 21 - $g ) / 11 ) );
      	$j = ( $year + (int)( $year / 4 ) + $i + 2 - $c + (int)( $c / 4 ) ) % 7;
      	$l = $i - $j;
      	$m = 3 + (int)( ( $l + 40 ) / 44 );
      	$d = $l + 28 - 31 * ( (int)( $m / 4 ) );

      	return new Date( mktime( 0, 0, 0, $m, $d, $year ) );
    }
} // END OF CalendarUtil

?>
