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
 * @package util_datetime
 */
 
class DateConversion extends PEAR 
{
	/**
	 * @access public
	 */
	function now() 
	{
		return $this->formatUnixTimestamp( 'us-1' );
	}

	/**
	 * @access public
	 */
	function formatUnixTimestamp( $style = 'eu-2', $unixTimestamp = null ) 
	{
		$format = $this->getFormatForStyle( $style );
		
		if ( is_null( $unixTimestamp ) )
			return date( $format );
		else
			return date( $format, $unixTimestamp );
	}
	
	/**
	 * @access public
	 */
	function formatArray( $style = 'eu-2', $array = null ) 
	{
		$format = $this->getFormatForStyle( $style );
		
		if ( is_null( $array ) ) 
			return date( $format );
		else 
			return str_replace( array( 'Y', 'm', 'd', 'H', 'i', 's' ), $array, $format );
	}

	/**
	 * @access public
	 */	
	function getFormatForStyle( $style = 'eu-2' ) 
	{
		$style = strtolower( $style );
		
		switch ( $style ) 
		{
			case 'eu-1':
				$format = "d.m.Y H:i:s";
				break;
				
			case 'eu-2':
				$format = "d.m.Y H:i";
				break;
				
			case 'eu-3':
				$format = "d.m.Y";
				break;
				
			case 'eu-4':
				$format = "H:i:s";
				break;
			
			case 'eu-5':
				$format = "H:i";
				break;
				
			case 'us-1':
				$format = "Y/m/d H:i:s";
				break;
				
			case 'us-2':
				$format = "Y/m/d H:i";
				break;
				
			case 'us-3':
				$format = "Y/m/d";
				break;
				
			case 'us-4':
				$format = "H:i:s";
				break;
				
			case 'us-5':
				$format = "H:i";
				break;
				
			case 'sql-1':
				$format = "Y-m-d H:i:s";
				break;
				
			case 'sql-2':
				$format = "Y-m-d H:i";
				break;
				
			case 'sql-3':
				$format = "Y-m-d";
				break;
				
			case 'sql-4':
				$format = "H:i:s";
				break;
				
			case 'sql-5':
				$format = "H:i";
				break;
				
			case 'ts':
			
			case 'ts-1':
				$format = "YmdHis";
				break;
				
			default:
				$format = "d.m.Y H:i";
		}
		
		return $format;
		
	}

	/**
	 * @access public
	 */
	function formatUsDatetime( $style = 'eu-2', $usDatetime = null ) 
	{
		if ( ( $usDatetime != null ) && ( $usDatetime != '' ) ) 
			return $this->formatUnixTimestamp( $style, $this->usDatetimeToUnixTimestamp( $usDatetime ) );
		else 
			return $this->formatUnixTimestamp( $style );
	}

	/**
	 * @access public
	 */
	function formatEuDatetime( $style = 'eu-2', $euDatetime = null ) 
	{
		if ( ( $euDatetime != null ) && ( $euDatetime != '' ) ) 
			return $this->formatUnixTimestamp( $style, $this->euDatetimeToUnixTimestamp( $euDatetime ) );
		else 
			return $this->formatUnixTimestamp( $style );
	}
	
	/**
	 * @access public
	 */
	function formatSqlDatetime( $style = 'eu-2', $sqlDatetime = null ) 
	{
		if ( ( $sqlDatetime != null ) && ( $sqlDatetime != '' ) )
			return $this->formatUnixTimestamp( $style, $this->sqlDatetimeToUnixTimestamp( $sqlDatetime ) );
		else 
			return $this->formatUnixTimestamp( $style );
	}

	/**
	 * @access public
	 */
	function formatSqlTimestamp( $style = 'eu-2', $sqlTimestamp = null ) 
	{
		if ( ( $sqlTimestamp != null ) && ( $sqlTimestamp != '' ) ) 
			return $this->formatUnixTimestamp( $style, $this->sqlTimestampToUnixTimestamp( $sqlTimestamp ) );
		else 
			return $this->formatUnixTimestamp( $style );
	}

	/**
	 * @access public
	 */
	function sqlTimestampToUnixTimestamp( $sqlTimestamp ) 
	{
		$s = &$sqlTimestamp;
		
		return mktime( 
			$this->_mid(  $s,  9, 2 ), 
			$this->_mid(  $s, 11, 2 ), 
			$this->_mid(  $s, 13, 2 ), 
			$this->_mid(  $s,  5, 2 ), 
			$this->_mid(  $s,  7, 2 ), 
			$this->_left( $s,  4 ) );
	}

	/**
	 * @access public
	 */	
	function timeToUnixTimestamp( $time ) 
	{
		return strtotime( $time );
	}
	
	/**
	 * @access public
	 */
	function usDatetimeToUnixTimestamp( $usDatetime ) 
	{
		return strtotime( $usDatetime );
	}

	/**
	 * @access public
	 */
	function usDateToUnixTimestamp( $usDate ) 
	{
		return strtotime( $usDate );
	}

	/**
	 * @access public
	 */	
	function usTimeToUnixTimestamp( $usTime ) 
	{
		return $this->timeToUnixTimestamp( $usTime );
	}

	/**
	 * @access public
	 */	
	function sqlDatetimeToUnixTimestamp( $sqlDatetime ) 
	{
		return strtotime( $sqlDatetime );
	}

	/**
	 * @access public
	 */	
	function sqlDateToUnixTimestamp( $sqlDate ) 
	{
		return strtotime( $sqlDate );
	}

	/**
	 * @access public
	 */	
	function sqlTimeToUnixTimestamp( $sqlTime ) 
	{
		return $this->timeToUnixTimestamp( $sqlTime );
	}

	/**
	 * @access public
	 */	
	function euDatetimeToUnixTimestamp( $euDatetime ) 
	{
		$euDatetime = trim( $euDatetime );
		$strlenEuDatetime = strlen( $euDatetime );
		
		if ( ( $strlenEuDatetime >= 12 ) && ( $strlenEuDatetime <= 19 ) && ( strpos( $euDatetime, " ", 6 ) >= 6 ) ) 
		{
			$t = explode( " ", $euDatetime );
			
			if ( is_array( $t ) ) 
			{
				if ( sizeof( $t ) == 2 ) 
				{
					$partDate = trim( $t[0] );
					$partTime = trim( $t[1] );
				} 
				else if ( sizeof( $t ) > 2) 
				{
					$tStep = false;
					
					while ( list( $k, $v ) = each( $t ) ) 
					{
						if ( strlen( $v ) >= 4 ) 
						{
							if ( $tStep ) 
							{
								$partTime = trim( $v );
								break;
							} 
							else 
							{
								$partDate = trim( $v );
								$tStep    = true;
							}
						}
					}
				} 
				else 
				{
					return -1;
				}
			} 
			else 
			{
				return -1;
			}

			$timestampOnlyDate = $this->euDateToUnixTimestamp( $partDate );
			
			if ( $timestampOnlyDate == -1 ) 
				return -1;
				
			$usDate = $this->formatUnixTimestamp( 'us-3', $timestampOnlyDate );
			
			if ( $usDate == -1 ) 
				return -1;
				
			return $this->usDatetimeToUnixTimestamp( $usDate . " " . $partTime );
		} 
		else if ( ( $strlenEuDatetime >= 6 ) && ( $strlenEuDatetime <= 10 ) ) 
		{
			return $this->euDateToUnixTimestamp( $euDatetime );
		} 
		else 
		{
			return -1;
		}
	}

	/**
	 * @access public
	 */
	function euDateToArray( $euDate ) 
	{
		$ret = array(
			'year'  => '0000', 
			'month' => '00', 
			'day'   => '00', 
			'hour'  => '00', 
			'min'   => '00', 
			'sec'   => '00'
		);
		
		if ( strlen( $euDate ) >= 6 ) 
		{
			if ( strlen( $euDate ) == 10 ) 
			{
				$ret['year']  = $this->_right( $euDate, 4 );
				$ret['month'] = $this->_mid( $euDate, 4, 2 );
				$ret['day']   = $this->_left( $euDate, 2 );
			} 
			else 
			{
				$separator = $this->getSeparator( $euDate );
				
				if ( $separator === false ) 
					return false;
					
				$array = explode( $separator, $euDate );
				
				if ( sizeof( $array ) == 3 ) 
				{
					$ret['day']   = $array[0];
					$ret['month'] = $array[1];
					$ret['year']  = $array[2];
					
					$ret = $this->cleanDateArray( $ret );
				} 
				else 
				{
					return false;
				}
			}
			
			if ( checkdate( $ret['month'], $ret['day'], $ret['year'] ) ) 
				return $ret;
			else 
				return false;
		} 
		else 
		{
			return false;
		}
	}
	
	/**
	 * @access public
	 */
	function sqlDateToArray( $sqlDate ) 
	{
		$ret = array(
			'year'  => '0000', 
			'month' => '00', 
			'day'   => '00', 
			'hour'  => '00', 
			'min'   => '00', 
			'sec'   => '00'
		);
		
		if ( strlen( $sqlDate ) >= 6 ) 
		{
			if ( strlen( $sqlDate ) == 10 ) 
			{
				$ret['year']  = $this->_left( $sqlDate, 4 );
				$ret['month'] = $this->_mid( $sqlDate, 6, 2 );
				$ret['day']   = $this->_right( $sqlDate, 2 );
			} 
			else 
			{
				$separator = $this->getSeparator( $sqlDate );
				
				if ( $separator === false ) 
					return false;
					
				$array = explode( $separator, $sqlDate );
				
				if ( sizeof( $array ) == 3 ) 
				{
					$ret['day']   = $array[2];
					$ret['month'] = $array[1];
					$ret['year']  = $array[0];
					
					$ret = $this->cleanDateArray( $ret );
				} 
				else 
				{
					return false;
				}
			}

			if ( checkdate( $ret['month'], $ret['day'], $ret['year'] ) )
				return $ret;
			else
				return false;
		} 
		else 
		{
			return false;
		}
	}

	/**
	 * @access public
	 */
	function getSeparator( $date ) 
	{
		if ( strpos( $date, '.' ) ) 
			return '.';
		else if ( strpos( $date, '/' ) ) 
			return '/';
		else if ( strpos( $date, '-' ) ) 
			return '-'; 
		else if ( strpos( $date, ' ' ) ) 
			return ' ';

		return false;
	}

	/**
	 * @access public
	 */
	function cleanDateArray( $arr ) 
	{
		if ( !is_array( $arr ) ) 
		{
			return array(
				'year'  => '0000', 
				'month' => '00', 
				'day'   => '00', 
				'hour'  => '00', 
				'min'   => '00', 
				'sec'   => '00'
			);
		}
		
		if ( !isset( $arr['year'] ) )  
			$arr['year'] = '0000';
			
		if ( !isSet( $arr['month'] ) ) 
			$arr['month'] = '00';
			
		if ( !isset( $arr['day'] ) )
			$arr['day'] = '00';
			
		if ( !isset( $arr['hour'] ) )  
			$arr['hour'] = '00';
			
		if ( !isset( $arr['min'] ) )
			$arr['min'] = '00';
			
		if ( !isset( $arr['sec'] ) )   
			$arr['sec'] = '00';
			
		if ( strlen( $arr['day'] ) == 1 ) 
			$arr['day'] = '0' . $arr['day'];
			
		if ( strlen( $arr['month'] ) == 1 ) 
			$arr['month'] = '0' . $arr['month'];
			
		if ( strlen( $arr['year'] ) == 2 ) 
		{
			if ( $arr['year'] < 30 ) 
			{
				if ( strlen( $arr['year'] ) == 0 ) 
					$arr['year'] = '2000';
				else if ( strlen( $arr['year'] ) == 1 ) 
					$arr['year'] = '200' . $arr['year'];
				else 
					$arr['year'] = '20' . $arr['year']; 
			} 
			else 
			{
				$arr['year']  = "19" . $arr['year'];
			}
		}
		
		return $arr;
	}
	
	/**
	 * @access public
	 */
	function euDateToUnixTimestamp( $euDate ) 
	{
		if ( strlen( $euDate ) >= 6 ) 
		{
			if ( strlen( $euDate ) == 10 ) 
			{
				$myYear  = $this->_right( $euDate, 4 );
				$myMonth = $this->_mid( $euDate, 4, 2 );
				$myDay   = $this->_left( $euDate, 2 );
			} 
			else 
			{
				$array = explode( ".", $euDate );
				
				if ( sizeof( $array ) == 3 ) 
				{
					$myDay   = $array[0];
					$myMonth = $array[1];
					$myYear  = $array[2];
					
					if ( strlen( $myDay ) == 1 ) 
						$myDay = "0" . $myDay;
						
					if ( strlen( $myMonth ) == 1 ) 
						$myMonth = "0" . $myMonth;
						
					if ( strlen( $myYear ) == 2 ) 
					{
						if ( $myYear < 30 ) 
						{
							if ( strlen( $myYear ) == 0 ) 
								$myYear = '2000';
							else if ( strlen( $myYear ) == 1 ) 
								$myYear = '200' . $myYear;
							else 
								$myYear = '20' . $myYear;
						} 
						else 
						{
							$myYear  = "19" . $myYear;
						}
					}
				} 
				else 
				{
					return -1;
				}
			}
			
			if ( checkdate( $myMonth, $myDay, $myYear ) ) 
				return mktime( 0, 0, 0, $myMonth, $myDay, $myYear );
			else 
				return -1;
		} 
		else 
		{
			return -1;
		}
	}
	
	/**
	 * @access public
	 */
	function euTimeToUnixTimestamp( $euTime ) 
	{
		return $this->timeToUnixTimestamp( $euTime );
	}
	
	/**
	 * @access public
	 */
	function usDatetimeToEuDatetime( $usDatetime = '' ) 
	{
		if ( $usDatetime == '' ) 
			return -1;
			
		$timestamp = $this->usDatetimeToUnixTimestamp( $usDatetime );
		
		if ( PEAR::isError( $timestamp ) ) 
			return $timestamp;
		
		return $this->formatUnixTimestamp( 'eu-1', $timestamp );
	}

	/**
	 * @access public
	 */
	function usDateToEuDate( $usDate = '' ) 
	{
		if ( $usDatetime == '' ) 
			return -1;
			
		$timestamp = $this->usDateToUnixTimestamp( $usDate );
		
		if ( $timestamp == -1 ) 
			return -1;
			
		return $this->formatUnixTimestamp( 'eu-3', $timestamp );
	}

	/**
	 * @access public
	 */
	function usDatetimeToSqlDatetime( $usDatetime = '' ) 
	{
		if ( $usDatetime == '' ) 
			return -1;
			
		$timestamp = $this->usDatetimeToUnixTimestamp( $usDatetime );
		
		if ( $timestamp == -1 ) 
			return -1;
			
		return $this->formatUnixTimestamp( 'sql-1', $timestamp );
	}
	
	/**
	 * @access public
	 */
	function usDateToSqlDate( $usDate = '' ) 
	{
		if ( $usDate == '' ) 
			return -1;
			
		$timestamp = $this->usDateToUnixTimestamp( $usDate );
		
		if ( $timestamp == -1 ) 
			return -1;
		
		return $this->formatUnixTimestamp( 'sql-3', $timestamp );
	}

	/**
	 * @access public
	 */
	function euDatetimeToUsDatetime( $euDatetime = '' ) 
	{
		if ( $euDatetime == '' ) 
			return -1;
			
		$timestamp = $this->euDatetimeToUnixTimestamp( $euDatetime );
		
		if ( $timestamp == -1 ) 
			return -1;
			
		return $this->formatUnixTimestamp( 'us-1', $timestamp );	
	}

	/**
	 * @access public
	 */	
	function euDateToUsDate( $euDate = '' ) 
	{
		if ( $euDate == '' ) 	
			return -1;
			
		$timestamp = $this->euDateToUnixTimestamp( $euDate );
		
		if ( $timestamp == -1 ) 
			return -1;
			
		return $this->formatUnixTimestamp( 'us-3', $timestamp );
	}

	/**
	 * @access public
	 */
	function euDatetimeToSqlDatetime( $euDatetime = '' ) 
	{
		if ( $euDatetime == '' ) 
			return -1;
			
		$timestamp = $this->euDatetimeToUnixTimestamp( $euDatetime );
		
		if ( $timestamp == -1 ) 
			return -1;
			
		return $this->formatUnixTimestamp( 'sql-1', $timestamp );
	}

	/**
	 * @access public
	 */
	function euDateToSqlDate( $euDate = '' ) 
	{
		if ( $euDate == '' ) 
			return -1;
			
		$timestamp = $this->euDateToUnixTimestamp( $euDate );
		
		if ( $timestamp == -1 ) 
			return -1;
			
		return $this->formatUnixTimestamp( 'sql-3', $timestamp );
	}

	/**
	 * @access public
	 */
	function sqlDatetimeToUsDatetime( $sqlDatetime = '' ) 
	{
		if ( $sqlDatetime == '' ) 
			return -1;
			
		$timestamp = $this->sqlDatetimeToUnixTimestamp( $sqlDatetime );
		
		if ( $timestamp == -1 ) 
			return -1;
		
		return $this->formatUnixTimestamp( 'us-1', $timestamp );
	}

	/**
	 * @access public
	 */
	function sqlDateToUsDate( $sqlDate = '' ) 
	{
		if ( $sqlDate == '' ) 
			return -1;
			
		$timestamp = $this->sqlDateToUnixTimestamp( $sqlDate );
		
		if ( $timestamp == -1 ) 
			return -1;
			
		return $this->formatUnixTimestamp( 'us-3', $timestamp );
	}

	/**
	 * @access public
	 */
	function sqlDatetimeToEuDatetime( $sqlDatetime ) 
	{
		if ( $sqlDatetime == '' ) 
			return -1;
			
		$timestamp = $this->sqlDatetimeToUnixTimestamp( $sqlDatetime );
		
		if ( $timestamp == -1 ) 
			return -1;
		
		return $this->formatUnixTimestamp( 'eu-1', $timestamp );
	}

	/**
	 * @access public
	 */
	function sqlDateToEuDate( $sqlDate = '' ) 
	{
		if ( $sqlDate == '' ) 
			return -1;
			
		$timestamp = $this->sqlDateToUnixTimestamp( $sqlDate );
		
		if ( $timestamp == -1 ) 
			return -1;
		
		return $this->formatUnixTimestamp( 'eu-3', $timestamp );
	}

	/**
	 * @access public
	 */
	function monthStringToNumber( $month, $zeroFill = false ) 
	{
		switch ( strtolower( substr( $month, 0, 3 ) ) ) 
		{
			case 'jan':

			case 'gen':

			case 'ene':
				if ( $zeroFill ) 
					return '01';
					
				return 1;
				break;
				
			case 'feb':
			
			case 'fév':

			case 'fev':
				if ( $zeroFill ) 
					return '02';
					
				return 2;
				break;
				
			case 'mar':
			
			case 'mär':

			case 'maa':
				if ( $zeroFill ) 
					return '03';
				
				return 3;
				break;
				
			case 'apr':
			
			case 'avr':

			case 'abr':
				if ( $zeroFill ) 
					return '04';
					
				return 4;
				break;
				
			case 'may':

			case 'mai':

			case 'mei':

			case 'maj':

			case 'mag':
				if ( $zeroFill ) 
					return '05';
					
				return 5;
				break;
				
			case 'jun':

			case 'jui':

			case 'giu':
				if ( $zeroFill ) 
					return '06';
					
				return 6;
				break;
				
			case 'jul':

			case 'jui':

			case 'lug':
				if ( $zeroFill ) 
					return '07';
					
				return 7;
				break;
			
			case 'aug':
			
			case 'aoû':

			case 'aou':

			case 'ago':
				if ( $zeroFill ) 
					return '08';
					
				return 8;
				break;
				
			case 'sep':
			
			case 'set':
				if ( $zeroFill ) 
					return '09';
					
				return 9;
				break;
				
			case 'oct':
			
			case 'okt':

			case 'ott':

			case 'out':
				if ( $zeroFill ) 
					return '10';
					
				return 10;
				break;
				
			case 'nov':
				if ( $zeroFill ) 
					return '11';
					
				return 11;
				break;
				
			case 'dec':

			case 'déc':

			case 'dez':

			case 'dic':
				if ( $zeroFill ) 
					return '12';
					
				return 12;
				break;
				
			default:
				return 0;
		}
	}

	/**
	 * @access public
	 */
	function monthToInt( $month, $zeroFill = false ) 
	{
		return $this->monthStringToNumber( $month, $zeroFill );
	}

	/**
	 * @access public
	 */
	function monthNumberToString( $month, $lang = 'en', $type = 'long' ) 
	{
		switch ( $lang ) 
		{
			case 'en':
				switch ( $month ) 
				{
					case 1:
						return ( $type == 'long' )? 'January' : 'Jan';
						break;
						
					case 2:
						return ( $type == 'long' )? 'February' : 'Feb';
						break;
						
					case 3:
						return ( $type == 'long' )? 'March' : 'Mar';
						break;
						
					case 4:
						return ( $type == 'long' )? 'April' : 'Apr';
						break;
						
					case 5:
						return ( $type == 'long' )? 'May' : 'May';
						break;
						
					case 6:
						return ( $type == 'long' )? 'June' : 'Jun';
						break;
						
					case 7:
						return ( $type == 'long' )? 'July' : 'Jul';
						break;
						
					case 8:
						return ( $type == 'long' )? 'August' : 'Aug';
						break;
					
					case 9:
						return ( $type == 'long' )? 'September' : 'Sep';
						break;
					
					case 10:
						return ( $type == 'long' )? 'October' : 'Oct';
						break;
						
					case 11:
						return ( $type == 'long' )? 'November' : 'Nov';
						break;
						
					case 12:
						return ( $type == 'long' )? 'December' : 'Dec';
						break;
					
					default:
						return false;
				}
				
				break;
				
			default: 
				return false;
		}
	}
	
	
	// private methods
	
	/**
	 * Returns $num chars from the left side of $haystack.
	 *
	 * @access private
	 */
	function _left( $haystack, $num = 0 ) 
	{
		if ( ( $haystack == '' ) || ( $num <= 0 ) ) 
			return '';
			
		return substr( $haystack, 0, $num );
	}
	
	/**
	 * Returns $num chars from the right side of $haystack.
	 *
	 * @access private
	 */
	function _right( $haystack, $num = 0 ) 
	{
		if ( $num == '' ) 
			return $haystack;
			
		if ( ( $haystack == '' ) || ( $num == 0 ) ) 
			return '';
			
		return substr( $haystack, -$num );
	}
	
	/**
	 * Returns $num chars from the middle of $haystack, beginning with $start (included).
	 *
	 * @access private
	 */
	function _mid( $haystack, $start = 1, $num = 0 ) 
	{
		if ( $num == '' ) 
			return $haystack;
			
		if ( ( $haystack == '' ) || ( $num == 0 ) ) 
			return '';
			
		if ( $start == 0 ) 
			$start = 1;
			
		return substr( $haystack, $start -1, $num );
	}
} // END OF DateConversion

?>
