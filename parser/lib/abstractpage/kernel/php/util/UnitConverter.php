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


define( 'UNITCONVERTER_KB', 1024 );
define( 'UNITCONVERTER_MB', 1048576 );
define( 'UNITCONVERTER_GB', 1073741824 );
define( 'UNITCONVERTER_TB', 1099511627776 );
define( 'UNITCONVERTER_PB', 1125899906842624 );
define( 'UNITCONVERTER_EB', 1152921504606846976 );


/**
 * @package util
 */
 
class UnitConverter extends PEAR 
{
	/**
	 * @access public
	 */	
	function length( $from, $to, $value, $precision = 3 ) 
	{
		$data = array(
			'cables'        => '182.88',
			'cm'            => '0.01',
			'chains'        => '20.1168',
			'dm'            => '0.1',
			'ells'          => '0.875',
			'fathoms'       => '1.8288',
			'feet'          => '0.3048',
			'furlongs'      => '201.168',
			'hands'         => '0.106',
			'hm'            => '100',
			'inches'        => '0.0254',
			'km'            => '1000',
			'm'             => '1',
			'miles'         => '1609.344',
			'milesNautical' => '1852',
			'mm'            => '0.001',
			'nanometers'    => '1e-9',
			'yards'         => '0.9144'
		);
		
		do 
		{
			if ( !isset( $data[$from] ) ) 
				break;
			
			if ( !isset( $data[$to] ) )   
				break;
			
			$valFrom = $data[$from];
			$valTo   = $data[$to];
			$value   = round( $value / $valTo * $valFrom, $precision );
			
			return (string)$value;
		} while ( false );
		
		return false;
	}
	
	/**
	 * @access public
	 */	
	function temperature( $from, $to, $value, $precision = 3 ) 
	{
		switch ( $from ) 
		{
			case 'celsius':
				$value += 273.15;
				break;
				
			case 'fahrenheit':
				$value = 5 / 9 * ( $value + 459.67 );
				break;
			
			case 'kelvin':
				break;
				
			case 'rankine':	
				$value = 5 / 9 * $value;
				break;
			
			case 'reaumure':

			case 'réaumure':
				$value = ( 5 / 4 * $value ) + 273.15;
				break;
			
			default:
				return false;
		}

		switch ( $to ) 
		{
			case 'celsius':
				$value -= 273.15;
				break;
				
			case 'fahrenheit':
				$value = ( 9 / 5 * $value ) - 459.67;
				break;
				
			case 'kelvin':
				break;
				
			case 'rankine':
				$value = 9 / 5 * $value;
				break;
				
			case 'reaumure':
			
			case 'réaumure':
				$value = 4 / 5 * ( $value - 273.15 );
				break;
				
			default:
				return false;
		}

		return (string)round( $value, $precision );
	}

	/**
	 * @access public
	 */	
	function bitsAndBytes( $from, $to, $value ) 
	{
		$data = array(
			'bits'       => 0.125,
			'bytes'      => 1,
			'kilobits'   => 128,
			'kilobytes'  => UNITCONVERTER_KB,
			'megabits'   => 131072,
			'megabytes'  => UNITCONVERTER_MB,
			'gigabits'   => 134217728,
			'gigabytes'  => UNITCONVERTER_GB,
			'terabits'   => 137438953472,
			'terabytes'  => UNITCONVERTER_TB,
			'petabits'   => 140737488355328,
			'petabytes'  => UNITCONVERTER_PB,
			'exabits'    => 144115188075855872, // Eb
			'exabytes'   => UNITCONVERTER_EB
		);
		
		return (string)( $value * $data[$from] / $data[$to] );
	}

	/**
	 * @access public
	 */	
	function toUsefulBitAndByteString( $bytes ) 
	{
		$data = array(                                    
			'bytes'      => 1,
			'kilobytes'  => UNITCONVERTER_KB,
			'megabytes'  => UNITCONVERTER_MB,
			'gigabytes'  => UNITCONVERTER_GB,
			'terabytes'  => UNITCONVERTER_TB,
			'petabytes'  => UNITCONVERTER_PB,
			'exabytes'   => UNITCONVERTER_EB
		);
		
		if ( $bytes > $data['exabytes'] ) 
		{
			$val   = UnitConverter::bitsAndBytes( 'bytes', 'exabytes', $bytes );
			$short = ' EB';
		} 
		else if ( $bytes > $data['petabytes'] ) 
		{
			$val   = UnitConverter::bitsAndBytes( 'bytes', 'petabytes', $bytes );
			$short = ' PB';
		} 
		else if ( $bytes > $data['terabytes'] ) 
		{
			$val   = UnitConverter::bitsAndBytes( 'bytes', 'terabytes', $bytes );
			$short = ' TB';
		} 
		else if ( $bytes > $data['gigabytes'] ) 
		{
			$val   = UnitConverter::bitsAndBytes( 'bytes', 'gigabytes', $bytes );
			$short = ' GB';
		} 
		else if ( $bytes > $data['megabytes'] ) 
		{
			$val   = UnitConverter::bitsAndBytes( 'bytes', 'megabytes', $bytes );
			$short = ' MB';
		} 
		else if ( $bytes > $data['kilobytes'] ) 
		{
			$val   = UnitConverter::bitsAndBytes( 'bytes', 'kilobytes', $bytes );
			$short = ' KB';
		} 
		else 
		{
			$val   = $bytes;
			$short = ' B';
		}

		if ( is_numeric( $val ) && strpos( $val, '.' ) ) 
			$val = (double)$val;
		
		if ( is_double( $val ) )
			$val = round( $val, 2 );

		return $val . $short;
	}

	/**
	 * @access public
	 */	
	function unitStringToBytes( $unitStr ) 
	{
		if ( is_numeric( $unitStr ) ) {
			return round( $unitStr );

		if ( !is_string( $unitStr ) ) 
			return false;
		
		if ( !preg_match( '/([0-9.]*)\s*(.*)/', $unitStr, $regs ) ) 
			return false;
			
		$val  = $regs[1];
		$unit = $regs[2];
		
		switch ( strtoupper( $unit ) ) 
		{
			case 'K': 
			
			case 'KB': 
				$ret = UNITCONVERTER_KB * $val; 
				break;
			
			case 'M': 
			
			case 'MB': 
				$ret = UNITCONVERTER_MB * $val; 
				break;
			
			case 'G': 

			case 'GB': 
				$ret = UNITCONVERTER_GB * $val; 
				break;
				
			case 'T': 

			case 'TB': 
				$ret = UNITCONVERTER_TB * $val; 
				break;
			
			case 'P': 

			case 'PB': 
				$ret = UNITCONVERTER_PB * $val; 
				break;
				
			case 'E': 

			case 'EB': 
				$ret = UNITCONVERTER_EB * $val; 
				break;
				
			case '':
				$ret = $val; 
				break;
				
			default: 
				return false;
		}

		return $ret;
	}
} // END OF UnitConverter

?>
