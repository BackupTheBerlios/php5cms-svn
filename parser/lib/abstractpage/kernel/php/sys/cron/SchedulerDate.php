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
 * @package sys_cron
 */
 
class SchedulerDate extends PEAR
{
	/**
	 * @access public
	 */
	var $legalDays = array(
		'MON', 
		'TUE', 
		'WED', 
		'THU', 
		'FRI', 
		'SAT', 
		'SUN'
	);

	/**
	 * @access public
	 */
	var $sec;
	
	/**
	 * @access public
	 */
	var $min;
	
	/**
	 * @access public
	 */
	var $hour;
	
	/**
	 * @access public
	 */
	var $day;
	
	/**
	 * @access public
	 */
	var $month;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SchedulerDate( $raw )
	{
		// this will work for now, Mon -> MON, tUe -> TUE
		$raw = strtoupper( $raw );

		$result = $this->parse( $raw );
		
		if ( PEAR::isError( $result ) )
		{
			$this = $result;
			return;
		}
	}


	/**
	 * @access public
	 */	
	function nowMatches()
	{
		return (
			 $this->monthMatches() &&
			 $this->monthMatches() &&
			 $this->dayMatches()   &&
			 $this->hourMatches()  &&
			 $this->minMatches()   &&
			 $this->secMatches() 
		)? true : false;
	}

	/**
	 * @access public
	 */
	function monthMatches()
	{
		if ( $this->month == '*' )
			return true;

		$currentmonth = '-' . date( 'n' ) . '-';

		if ( strpos( $this->month, $currentmonth ) !== false )
			return true;

		return false;
	}

	/**
	 * @access public
	 */
	function dayMatches()
	{
		if ( $this->day["value"] == '*' )
			return true;

		$currentdaynum = '-' . date( 'j' ) . '-';
		$currentdaytxt = '-' . strtoupper( date( 'D' ) ) . '-';

		foreach ( $this->day as $day )
		{
			if ( strpos( $day["not"], $currentdaytxt ) !== false )
			{
				// do nothing
			} 
			else
			{
				$v1 = strpos( $day["value"], $currentdaynum );
				$v2 = strpos( $day["and"],   $currentdaytxt );
	
				if ( $day["and"] && ( $v1 && $v2 ) )
					return true;
				else if ( !$day["and"] && $v1 )
					return true;
			}
		}

		return false;
	}

	/**
	 * @access public
	 */
	function hourMatches()
	{
		if ( $this->hour == '*' )
			return true;

		$currenthour = '-' . date( 'G' ) . '-';

		if ( strpos( $this->hour, $currenthour ) !== false )
			return true;

		return false;
	}

	/**
	 * @access public
	 */
	function minMatches()
	{	
		if ( $this->min == '*' )
			return true;

		$currentmin = '-' . intval( date( 'i' ) ) . '-';

		if ( strpos( $this->min, $currentmin ) !== false )
			return true;

		return false;
	}

	/**
	 * @access public
	 */
	function secMatches()
	{
		if ( $this->sec == '*' )
			return true;

		$currentsec = '-' . intval( date( 's' ) ) . '-';

		if ( strpos( $this->sec, $currentsec ) !== false )
			return true;

		return false;
	}

	/**
	 * @access public
	 */
	function parse( $str )
	{
		$s = array();
		list( $s["sec"], $s["min"], $s["hour"], $s["day"], $s["month"] ) = split( "[\n\t ]+", $str );

		foreach ($s as $k => $v )
		{
			if ( strpos( $v, '*' ) !== false )
				$s[$k] = array( '*' );
			else if ( !$this->generallyDecentSyntax( $v ) )
				return PEAR::raiseError( "Illegal syntax." );
			else
				$s[$k] = explode( ",", $s[$k] );
		}

		if ( $s["sec"][0] == '*' )
		{
			$this->sec = '*';
		}
		else
		{
			for ( $i = 0; $i < sizeof( $s["sec"] ); $i++ )
			{
				if ( $this->isRange( $s["sec"][$i] ) )
				{
					$range = $this->expandRange( $this->rangeVals( $s["sec"][$i] ) );

					if ( !PEAR::isError( $range ) )
						$s["sec"][$i] = $range;
				}
			}
			
			$this->sec = '-' . join( '-', $s["sec"] ) . '-';
		}

		if ( $s["min"][0] == '*' )
		{
			$this->min = '*';
		}
		else
		{
			for ( $i = 0; $i < sizeof( $s["min"] ); $i++ )
			{
				if ( $this->isRange( $s["min"][$i] ) )
				{
					$range = $this->expandRange( $this->rangeVals( $s["min"][$i] ) );
					
					if ( !PEAR::isError( $range ) )
						$s["min"][$i] = $range;
				}
			}
			
			$this->min = '-' . join( '-', $s["min"] ) . '-';
		}

		if ( $s["hour"][0] == '*' )
		{
			$this->hour = '*';
		}
		else
		{
			for ( $i = 0; $i < sizeof( $s["hour"] ); $i++ )
			{
				if ( $this->isRange( $s["hour"][$i] ) )
				{
					$range = $this->expandRange( $this->rangeVals( $s["hour"][$i] ) );
					
					if ( !PEAR::isError( $range ) )
						$s["hour"][$i] = $range;
				}
			}
			
			$this->hour = '-' . join('-', $s["hour"] ) . '-';
		}

		if ( $s["day"][0] == '*' )
		{
			$this->day = '*';
		}
		else
		{
			for ( $i = 0; $i < sizeof( $s["day"] ); $i++ )
			{
				$tmp = array();
				
				if ( ( $char = $this->isCond( $s["day"][$i] ) ) !== false )
				{
					if ( $char == '&' )
					{
						list( $tmp["value"], $tmp["and"] ) = explode( $char, $s["day"][$i] );
						
						if ( $this->isRange( $tmp["and"] ) )
						{
							$range = $this->expandRange( $this->rangeVals( $tmp["and"] ) );
							
							if ( !PEAR::isError( $range ) )
								$tmp["and"] = $range;
						}
					}
					else 
					{
						list( $tmp["value"], $tmp["not"] ) = explode( $char, $s["day"][$i] );
						
						if ( $this->isRange( $tmp["not"] ) )
						{
							$range = $this->expandRange( $this->rangeVals( $tmp["not"] ) );
							
							if ( !PEAR::isError( $range ) )
								$tmp["not"] = $range;
						}
					}
				}
				else
				{
					$tmp = array( "value" => $s["day"][$i] );
				}
				
				$s["day"][$i] = $tmp;

				if ( $this->isRange( $s["day"][$i]["value"] ) )
				{
					$range = $this->expandRange( $this->rangeVals( $s["day"][$i]["value"] ) );
					
					if ( !PEAR::isError( $range ) )
						$s["day"][$i]["value"] = $range;
				}
			}
			
			$this->day = $s["day"]; // no join
		}

		if ( $s["month"][0] == '*' )
		{
			$this->month = '*';
		}
		else
		{
			for ( $i = 0; $i < sizeof( $s["month"] ); $i++ )
			{
				if ( $this->isRange( $s["month"][$i] ) )
				{
					$range = $this->expandRange( $this->rangeVals($s["month"][$i] ) );
					
					if ( !PEAR::isError( $range ) )
						$s["month"][$i] = $range;
				}
			}
			
			$this->month = '-' . join( '-', $s["month"] ) . '-';
		}
		
		return true;
	}

	/**
	 * @access public
	 */
	function isCond( $s )
	{
		if ( strpos( $s, '&' ) !== false )
			return '&';
		else if ( strpos( $s, '!' ) !== false )
			return '!';
		else
			return false;
	}

	/**
	 * @access public
	 */
	function isRange( $s )
	{
		if ( preg_match( '/^\w+\-\w+/', $s ) )
			return true;
		else
			return false;
	}

	/**
	 * @access public
	 */
	function isCondRange( $s )
	{
		if ( isCond( $s ) && isRange( $s ) )
			return true;
		else
			return false;
	}

	/**
	 * @access public
	 */
	function isCondVal( $s )
	{
		if ( isCond( $s ) && !isRange( $s ) )
			return true;
		else
			return false;
	}

	/**
	 * @access public
	 */
	function rangeVals( $s )
	{
		return explode('-', $s);
	}

	/**
	 * @access public
	 */
	function expandRange( $l, $h = "" )
	{
		// expand range from M-F -> "-M-T-W-R-F-" and 1-5 -> "-1-2-3-4-5-"

		if ( is_array( $l ) )
			list( $l, $h ) = $l;

		if ( $this->isDigit( $l ) )
		{
			if ( $this->isAlpha( $h ) )
				return PEAR::raiseError( "Invalid range. Can't mix letters and numbers." );
			else if ( !$this->isDigit( $h ) )
				return PEAR::raiseError( "Invalid value in range: " . $h );

			// currently there is no possible reason to need to do a range beyond 0-59 for anything
			if ( $l < 0 )
				$l = 0;
			else if ( $l > 59 )
				$l = 59;
			
			if ( $h < 0 )
				$h = 0;
			else if ( $h > 59 )
				$h = 59;

			if ( $l > $h )
			{
				$tmp = $l;
				$l   = $h;
				$h   = $tmp;
				
				unset( $tmp );
			}

			// for some reason range() is fucking up w/o the explicit intval()s. weird.
			return '-' . join( '-', range( intval( $l ), intval( $h ) ) ) . '-';
		}
		else if ( $this->isAlpha( $l ) )
		{
			if ( $this->isDigit( $h ) )
				return PEAR::raiseError( "Invalid range. Can't mix letters and numbers. );
			else if (!$this->isAlpha($h))
				return PEAR::raiseError( "Invalid value in range: " . $h );

			$d1 = $this->dayValue( $l );
			$d2 = $this->dayValue( $h );

			if ( $d1 > $d2 )
			{
				$tmp = $d1;
				$d1  = $d2;
				$d2  = $tmp;
				
				unset( $tmp );
			}

			$r = '-';

			for ( $i = $d1; $i <= $d2; $i++ )
				$r .= $this->legalDays[$i] . '-';
			
			return $r;
		}
		else
		{
			return PEAR::raiseError( "Invalid value in range: " . $l );
		}
	}

	/**
	 * @access public
	 */
	function dayValue( $s )
	{
		for ( $i = 0; $i < sizeof( $this->legalDays ); $i++ )
		{
			if ( $this->legalDays[$i] == $s )
				return $i;
		}

		return -1;
	}

	/**
	 * @access public
	 */
	function dayValue( $s )
	{
		for ( $i = 0; $i < sizeof( $this->legalDays ); $i++ )
		{
			if ( $this->legalDays[$i] == $s )
				return $i;
		}

		return -1;
	}

	/**
	 * @access public
	 */
	function isDigit( $s )
	{
		if ( preg_match( '/^\d+$/', $s ) )
			return true;
		else
			return false;
	}

	/**
	 * @access public
	 */
	function isAlpha( $s )
	{
		if ( $this->isLegalDay( $s ) )
			return true;
		else
			return false;
	}

	/**
	 * @access public
	 */
	function isLegalDay( $s )
	{
		if ( in_array( $s, $this->legalDays ) )
			return true;
		else
			return false;
	}

	/**
	 * @access public
	 */
	function generallyDecentSyntax( $s )
	{
		if ( $s == '*' || preg_match( '/^\d+(-\d+)?([!&][A-Z\*]+(-[A-Z\*]+)?)?(,\d+(-\d+)?([!&][A-Z\*]+(-[A-Z\*]+)?)?)*$/', $s ) )
			return true;
			
		return false;
	}
} // END OF SchedulerDate

?>
