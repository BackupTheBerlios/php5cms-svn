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
 * @package html_widget_calendar
 */
 
class CalendarWidget extends PEAR
{
	var $table_border;
	var $table_width;
	
	var $bg_cell_color;
	var $bg_cell_colorToday;
	var $bg_cell_colorBlank;
	var $bg_cell_colorholidays;

	var $font_face;
	var $font_faceToday;
	var $font_faceBlank;
	var $font_faceholidays;

	var $font_color;
	var $font_colorToday;
	var $font_colorBlank;
	var $font_colorholidays;

	var $link;
	var $week_start;
	var $show_blanks;
	var $how_many_days;
	var $days_format;
	var $days;
	var $holidays;
	var $show_holidays;


	/**
	 * Constructor
	 */
	function CalendarWidget( $parameters = array() ) 
	{
		$this->table_border          = 0;
		$this->table_width           = '300';
		$this->bg_cell_color         = '#C0C0C0';
		$this->bg_cell_colorToday    = '#000000';
		$this->bg_cell_colorBlank    = '#FFFFFF';
		$this->bg_cell_colorholidays = '#FF0000';
		$this->font_face             = 'Verdana, Arial';
		$this->font_faceToday        = 'Verdana, Arial';
		$this->font_faceBlank        = 'Verdana, Arial';
		$this->font_faceholidays     = 'Verdana, Arial';
		$this->font_color            = '#000000';
		$this->font_colorToday       = '#FFFFFF';
		$this->font_colorBlank       = '#000000';
		$this->font_colorholidays    = '#FFFFFF';
		$this->link                  = '';
		$this->week_start            = 1;
		$this->show_blanks           = 1;
		$this->how_many_days         = 7;
		$this->days_format           = '%d %B %Y';
		$this->days                  = array( 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su' );
		$this->holidays              = array( 'easter', 'christmas', 'newyear', 'epiphany', 'labourday' );
		$this->show_holidays         = 1;

		if ( count( $parameters ) > 0 ) 
		{
			foreach ( $parameters as $nomecampo => $valorecampo )
				$this->$nomecampo = $valorecampo;
		}

		if ( $this->month_shift ) 
		{
			/* If we have a month_shift we set time_stamp, too.*/
			$this->setMonthShift( $this->month_shift );
		} 
		else if ( $this->time_stamp ) 
		{
			/* If we have an UNIX time_stamp we calculate the time_stamp at
			   midnight of the same day. Then we calculate the month_shift.*/
			$this->setTimeStamp( $this->time_stamp );
		} 
		else 
		{
			$this->setMonthShift();
		}

		/* Now we set some useful variables.*/
		$this->calculateVars();

	}

	function setMonthShift( $monthshift = 0 ) 
	{
		$this->month_shift = $monthshift;
		$this->time_stamp  = mktime( 0, 0, 0, date( 'n' ) + $this->month_shift, 1, date( 'Y' ) );
	}

	function setTimeStamp( $timestamp = 0 ) 
	{
		if ( $timestamp == 0 ) 
			$timestamp = time();
		
		$this->month_shift = $this->calculateMonthShift( $this->time_stamp );
		$this->time_stamp  = mktime( 0, 0, 0, date( "n", $timestamp ), date( "d", $timestamp ), date( "Y", $timestamp ) );
	}
	
	/**
	 * This function calculates the month shift between 'now'
	 * and the input timestamp.
	 */
	function calculateMonthShift( $timestamp ) 
	{
		list( $thismonth,  $thisyear  ) = explode( '/', date( 'm/Y' ) );
		list( $othermonth, $otheryear ) = explode( '/', date( 'm/Y', $timestamp ) );
		
		$diff  = ( $otheryear  - $thisyear  ) * 12;
		$diff += ( $othermonth - $thismonth );

		return $diff;
	}

	/**
	 * Returns the month name, according to the system locale,
	 * referring to the global class time + $shift.
	 */
	function monthName( $shift = 0 ) 
	{
		return strftime( "%B",  mktime( 0, 0, 0, date( 'n' ) + $shift + $this->month_shift, date( 'd' ), date( 'Y' ) ) );
	}

	/**
	 * Returns the 4-digits year, referring to the global class
	 * time + $shift.
	 */
	function year( $shift = 0 ) 
	{
		return date( 'Y', mktime( 0, 0, 0, date( 'n' ) + $shift + $this->month_shift, date( 'd' ), date( 'Y' ) ) );
	}

	/**
	 * Returns the 4-digits year, referring to the global class
	 * time + $shift and, if differs, the 4-digit year of the next
     * month.
	 */
	function doubleYear( $shift = 0 ) 
	{
		$first  = date( 'Y', mktime( 0, 0, 0, date( 'n' ) +    $shift + $this->month_shift, date( 'd' ), date( 'Y' ) ) );
		$second = date( 'Y', mktime( 0, 0, 0, date( 'n' ) +1 + $shift + $this->month_shift, date( 'd' ), date( 'Y' ) ) );
		
		if ( $first == $second )
			return $first;
		else
			return "$first/$second";
	}

	function calculateVars() 
	{
		if ( $this->week_start < 1 || $this->week_start > 7 ) 
			$this->week_start = 7;

		$thisday = date( 'w', $this->time_stamp );
		
		if ( $thisday == 0 ) 
			$thisday = 7;
		
		if ( $this->week_start > $thisday ) 
			$thisday += 7;

		$fwd = date( 'j', $this->time_stamp ) - $thisday + $this->week_start;
		$lwd = $fwd + $this->how_many_days - 1;

		$this->today             = strftime( $this->days_format, mktime( 0, 0, 0, date( 'n' ), date( 'd' ), date( 'Y' ) ) );
		$this->first_week_day_ts = mktime( 0, 0, 0, date( 'n' ) + $this->month_shift, $fwd, date( 'Y' ) );
		$this->last_week_day_ts  = mktime( 0, 0, 0, date( 'n' ) + $this->month_shift, $lwd, date( 'Y' ) );
		$this->first_week_day    = strftime( $this->days_format, $this->first_week_day_ts );
		$this->last_week_day     = strftime( $this->days_format, $this->last_week_day_ts  );
	}

	function noMoreThan6( $i ) 
	{
		if ( $i > 6 ) 
		{
			$i = $i - 7;
			$this->noMoreThan6( $i );
		} 
		else if ( $i < 0 ) 
		{
			$i = 0;
		}

		return $i;
	}

	/**
	 * Prints a table with the calendar of this month + $shift,
	 * referring to the global class time. Always returns 0.
	 *
	 * First, calculate the timestamp for the first and the
	 * last day of the month. Then calculate the number of days
	 * in the month.
	 */
	function showMonth( $shift = 0 ) 
	{	
		$oneday = 24 * 60 * 60;

		$first_month_day_ts = mktime( 0, 0, 0, date( "n" ) + $shift + $this->month_shift,  1, date( 'Y' ) );
		$last_month_day_ts  = mktime( 0, 0, 0, date( "n" ) + $shift + $this->month_shift + 1, 0, date( 'Y' ) );

		$year               = date( 'Y', $first_month_day_ts );
		$daysInMonth        = date( 't', $first_month_day_ts );

		// replace day 0 for day 7, week starts on monday
		$dayMonth_start = date( "w", $first_month_day_ts );
		
		if ( $dayMonth_start == 0 )
			$dayMonth_start = 7;

		$dayMonth_end = date( "w", $last_month_day_ts );
		
		if ( $dayMonth_end == 0 )
			$dayMonth_end = 7;

		$dst = date('Z', $first_month_day_ts);

		if ( $this->show_holidays == 1 ) 
		{
			if ( in_array( 'easter', $this->holidays ) )
				$holidays[] = easter_date( $year );
			
			if ( in_array( 'christmas', $this->holidays ) )
				$holidays[] = mktime( 0, 0, 0, 12, 25, $year );
			
			if ( in_array( 'newyear', $this->holidays ) )
				$holidays[] = mktime( 0, 0, 0, 1, 1, $year );
			
			if ( in_array( 'epiphany', $this->holidays ) )
				$holidays[] = mktime( 0, 0, 0, 1, 6, $year );
			
			if ( in_array( 'orthodoxnewyear', $this->holidays ) )
				$holidays[] = mktime( 0, 0, 0, 1, 14, $year );
			
			if ( in_array( 'orthodoxepiphany', $this->holidays ) )
				$holidays[] = mktime( 0, 0, 0, 1, 19, $year );
			
			if ( in_array( 'nawroz', $this->holidays ) )
				$holidays[] = mktime( 0, 0, 0, 3, 21, $year );
			
			if ( in_array( 'ramanavani', $this->holidays ) )
				$holidays[] = mktime( 0, 0, 0, 4, 11, $year );
			
			if ( in_array( 'orthodoxeaster', $this->holidays ) )
				$holidays[] = easter_date( $year ) + 7 * $oneday;
			
			if ( in_array( 'labourday', $this->holidays ) )
				$holidays[] = mktime( 0, 0, 0, 5, 1, $year );
			
			if ( in_array( 'poson', $this->holidays ) )
				$holidays[] = mktime( 0, 0, 0, 6, 14, $year );
		}

		/* Print table header.*/
		echo "\t" . '<table border="' . $this->table_border . '" width="' . $this->table_width . '">'."\n";
		echo "\t" . '  <tr bgcolor="' . $this->bg_cell_color . '">' . "\n";
	  
		/* Print the first row containing days names (array $this->days).*/
		$temp  = array_slice( $this->days, $this->week_start - 1 );
		$temp2 = array_merge( $temp, $this->days );
		
		$i = 0;
		foreach ( $temp2 as $name ) 
		{
			if ( $i > 6 ) 
				break;
		
			if ( $i > $this->how_many_days - 1 ) 
				break;
		
			echo "\t".'    <td><center><font face="' . $this->font_face . '" color="' . $this->font_color . '">';
			echo '<b>' . $name . '</b></font></center></td>' . "\n";

			$i++;
		}
		
		echo "\t" . '  </tr>' . "\n" . "\t" . '  <tr>' . "\n";

		/* Prints white spaces or other month's days until the first day.*/
		$howmanyblanks = 7 + $dayMonth_start - $this->week_start;
		$howmanyblanks = $this->noMoreThan6( $howmanyblanks );

		$wday = 0;
		for ( $i = $first_month_day_ts - ( $oneday * $howmanyblanks ); $i < $first_month_day_ts; $i += $oneday ) 
		{
			$wday++;
			
			if ( $wday > $this->how_many_days ) 
				continue;
			
			if ( $howmanyblanks < $this->how_many_days ) 
			{
				echo "\t" . '    <td>';
				
				if ( $this->show_blanks == 1 ) 
				{
					echo '<font color="' . $this->font_colorBlank . '" face="' . $this->font_faceBlank . '"><center>';
					echo date( 'd', $i );
					echo '</center></font>';
				} 
				else 
				{
					echo '&nbsp;';
				}
				
				echo '</td>'."\n";
			}
		}

		$day = 0;
		$i   = $first_month_day_ts;

		/* Print the calendar of the month, seven days a week, skipping unwanted days. */
		$wday2 = $wday;

		while ( -1 ) 
		{
			$day++;
			$wday++;
			$wday2++;

			if ( date( 'Z', $i ) != $dst ) 
			{
				$i   -= date( 'Z', $i ) - $dst;
				$dst  = date( 'Z', $i );
			}
			
		    if ( $wday > 7 ) 
			{
				echo "\n" . '  </tr>' . "\n";

				$wday  = 1;
				$wday2 = 1;
			}

			if ( $i > $last_month_day_ts + 1 ) 
				break;

			if ( $wday == 1 )
				echo "\t" . '  <tr>' . "\n"; 

			if ( $wday2 <= $this->how_many_days ) 
			{
				if ( $day < 10 )
					$day = "0$day";

				if ( $this->time_stamp == $i )
					$change = 'Today';
				else if ( in_array( $i, $holidays ) )
					$change = 'holidays';
				else
					$change = '';

				if ( $this->link ) 
				{
				  	$string  = '<a href="' . $this->link . $i . '" style="text-decoration: none;">';
					$string .= '<font color="' . $this->{'font_color' . $change} . '" face="' . $this->{'font_face' . $change} . '">' . $day . "</font></A>";
				} 
				else 
				{
					$string  = '<font color="' . $this->{'font_color' . $change} . '" face="' . $this->{'font_face' . $change} . '">' . $day . "</font>";
				}
		
				echo '<td bgcolor="' . $this->{'bg_cell_color' . $change} . '"><b><center>' . $string . '</center></b></td>';
			}

			$i += $oneday;
		}

		$howmanyblanks = 7 - $dayMonth_end + $this->week_start - 1;
		
		if ( $howmanyblanks >= 7 ) 
			$howmanyblanks -= 7;

		$day = 0;
		for ( $x = 1; $x <= $howmanyblanks; $x++ ) 
		{ 
			$day++;
			$wday++;
			
			if ( $wday > $this->how_many_days + 1 ) 
				continue;
			
			if ( $day < 10 ) 
				$day = "0$day";
			
			echo "\t" . '    <td>';
			
			if ( $this->show_blanks == 1 ) 
			{
				echo '<font color="' . $this->font_colorBlank . '" face="' . $this->font_faceBlank . '"><center>';
				echo $day;
				echo '</center></font>';
			} 
			else 
			{
				echo '&nbsp;';
			}
			
			echo '</td>'."\n";
		}

		echo "\n" . '  </tr>'."\n";
	  	echo "\t</table>\n";
	}
} // END OF CalendarWidget

?>
