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
 * @package format_csv
 */
 
class CSVUtil
{
	/**
	 * @access public
	 * @static
	 */
	function csvFileToArray( $fullPath, $separator = ';', $trim = 'none', $removeHeader = false, $removeEmptyLines = false, $checkMultiline = false ) 
	{
		$fileContent = @file( $fullPath );
		
		if ( !$fileContent ) 
			return false;
			
		while ( list( $k ) = each( $fileContent ) ) 
		{
			if ( ( substr( $fileContent[$k], -1 ) == "\r" ) || ( substr( $fileContent[$k], -1 ) == "\n" ) )
				$fileContent[$k] = substr( $fileContent[$k], 0, -1 );
		}
		
		reset( $fileContent );
		
		if ( $checkMultiline ) 
			$fileContent = CSVUtil::_checkMultiline( $fileContent );
			
		return CSVUtil::csvArrayToArray( $fileContent, $separator, $trim, $removeHeader, $removeEmptyLines );	
	}
	
	/**
	 * @access public
	 * @static
	 */
	function csvStringToArray( $string, $separator = ';', $trim = 'none', $removeHeader = false, $removeEmptyLines = false, $checkMultiline = false ) 
	{
		if ( empty( $string ) ) 
			return array();
			
		$array = explode( "\n", $string );
		
		while ( list( $k ) = each( $array ) ) 
		{
			if ( substr( $array[$k], -1 ) == "\r" )
				$array[$k] = substr( $array[$k], 0, -1 );
		}

		reset( $array );
		
		if ( ( !is_array( $array ) ) || empty( $array ) ) 
			return array();
			
		if ( $checkMultiline ) 
			$array = CSVUtil::_checkMultiline( $array );
			
		return CSVUtil::csvArrayToArray( $array, $separator, $trim, $removeHeader, $removeEmptyLines );
	}

	/**
	 * @access public
	 * @static
	 */
	function csvArrayToArray( $array, $separator = ';', $trim = 'none', $removeHeader = false, $removeEmptyLines = false ) 
	{
		switch ( $trim ) 
		{
			case 'none':
				$trimFunction = false;
				break;
				
			case 'left':
				$trimFunction = 'ltrim';
				break;
			
			case 'right':
				$trimFunction = 'rtrim';
				break;
			
			default:
				$trimFunction = 'trim';
				break;
		}

		$sepLength = strlen( $separator );
		
		if ( $removeHeader ) 
			array_shift( $array );
			
		$ret = array();
		reset( $array );
		
		while ( list(,$line) = each( $array ) ) 
		{
			$offset    = 0;
			$lastPos   = 0;
			$lineArray = array();
			
			do 
			{
				$pos = strpos( $line, $separator, $offset );
				
				if ( $pos === false ) 
				{
					$lineArray[] = substr( $line, $lastPos );
					break;
				}

				$currentSnippet = substr( $line, $lastPos, $pos - $lastPos );
				$numQuotes = substr_count( $currentSnippet, '"' );
				
				if ( $numQuotes % 2 == 0 ) 
				{
					$lineArray[] = substr( $line, $lastPos, $pos-$lastPos );
					$lastPos = $pos + $sepLength;
				} 
				else 
				{
				}

				$offset = $pos + $sepLength;
			} while ( true );
			
			if ( $trimFunction !== false ) 
			{
				while ( list( $k ) = each( $lineArray ) ) 
					$lineArray[$k] = $trimFunction( $lineArray[$k] );

				reset( $lineArray );
			}
			
			while ( list( $k ) = each( $lineArray ) ) 
			{
				if ( ( substr( $lineArray[$k], 0, 1 ) == '"' ) && ( substr( $lineArray[$k], 1, 1 ) != '"' ) && ( substr( $lineArray[$k], -1 ) == '"' ) ) 
					$lineArray[$k] = substr( $lineArray[$k], 1, -1 );
				
				$lineArray[$k] = str_replace( '""', '"', $lineArray[$k] );
			}

			reset( $lineArray );
			$addIt = true;
			
			if ( $removeEmptyLines ) 
			{
				do 
				{
					while ( list( $k ) = each( $lineArray ) ) 
					{
						if ( !empty( $lineArray[$k] ) ) 
							break 2;
					}
					
					$addIt = false;
				} while ( false );
				
				reset( $lineArray );
			}

			if ( $addIt ) 
				$ret[] = $lineArray;
		}
		
		return $ret;
	}

	/**
	 * @access public
	 * @static
	 */	
	function arrayToCsvString( $array, $separator = ';', $trim = 'none', $removeEmptyLines = true ) 
	{
		if ( !is_array( $array ) || empty( $array ) ) 
			return '';
			
		switch ( $trim ) 
		{
			case 'none':
				$trimFunction = false;
				break;
			
			case 'left':
				$trimFunction = 'ltrim';
				break;
			
			case 'right':
				$trimFunction = 'rtrim';
				break;
			
			default:
				$trimFunction = 'trim';
				break;
		}
		
		$ret = array();
		reset( $array );
		
		if ( is_array( current( $array ) ) ) 
		{
			while ( list(,$lineArr) = each( $array ) ) 
			{
				if ( !is_array( $lineArr ) ) 
				{
					$ret[] = array();
				} 
				else 
				{
					$subArr = array();
					
					while ( list(,$val) = each( $lineArr ) ) 
					{
						$val      = CSVUtil::_valToCsvHelper( $val, $separator, $trimFunction );
						$subArr[] = $val;
					}
				}

				$ret[] = join( $separator, $subArr );
			}
			
			return join( "\n", $ret );
		} 
		else 
		{
			while ( list(,$val) = each( $array ) ) 
			{
				$val   = CSVUtil::_valToCsvHelper( $val, $separator, $trimFunction );
				$ret[] = $val;
			}

			return join( $separator, $ret );
		}
	}
	
	
	// private methods

	/**
	 * @access private
	 * @static
	 */	
	function _valToCsvHelper( $val, $separator, $trimFunction ) 
	{
		if ( $trimFunction ) 
			$val = $trimFunction( $val );
			
		$needQuote = false;
		
		do 
		{
			if ( strpos( $val, '"' ) !== false ) 
			{
				$val = str_replace( '"', '""', $val );
				$needQuote = true;
				
				break;
			}

			if ( strpos( $val, $separator ) !== false ) 
			{
				$needQuote = true;
				break;
			}

			if ( ( strpos( $val, "\n" ) !== false ) || ( strpos( $val, "\r" ) !== false ) ) 
			{
				$needQuote = true;
				break;
			}
		} while ( false );
		
		if ( $needQuote )
			$val = '"' . $val . '"';

		return $val;
	}

	/**
	 * @access private
	 * @static
	 */	
	function _checkMultiline( $in ) 
	{
		$ret = array();
		$stack = false;
		reset( $in );
		
		while ( list(,$line) = each( $in ) ) 
		{
			$c = substr_count( $line, '"' );
			
			if ( $c % 2 == 0 ) 
			{
				if ( $stack === false ) 
					$ret[] = $line;
				else 
					$stack .= "\n" . $line;
			} 
			else 
			{
				if ( $stack === false ) 
				{
					$stack = $line;
				} 
				else 
				{
					$ret[] = $stack . "\n" . $line;
					$stack = false;
				}
			}
		}
		
		return $ret;
	}
} // END OF CSVUtil

?>
