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
 * @package util
 */
 
class SortUtil extends PEAR
{
	/**
	 * @access public
	 * @static
	 */		
	function bubblesort( $a1, $a2 ) 
	{ 
		for ( $i = sizeof( $a1 ); $i >= 1; $i-- ) 
		{ 
			for ( $j = 1; $j <= $i; $j++ )
			{ 
				if ( $a1[$j-1] > $a1[$j] ) 
				{ 
					$t  = $a1[$j-1];
					$t2 = $a2[$j-1];
					
					$a1[$j-1] = $a1[$j];
					$a2[$j-1] = $a2[$j];
					
					$a1[$j] = $t;
					$a2[$j] = $t2;
				} 
			} 
		} 
	}
	
	/**
	 * Case insensitive alphabetical array sorter.
	 * This function is to be used with php´s built in usort().
	 * It will sort an array alphabetically and case insensitively,
	 * something I found the normal sort() function didn't do.
	 * Example: usort($array, 'isort');
	 *
	 * @access public
	 * @static
	 */
	function usort( $a, $b )
	{ 
		if ( ord( substr( strtolower( $a ), 0, 1 ) ) == ord( substr( strtolower( $b ), 0, 1 ) ) )
			return false;

		return ( ord( substr( strtolower( $a ), 0, 1 ) ) < ord( substr( strtolower( $b ), 0, 1 ) ) )? -1 : 1;
	}

	/**
	 * @access public
	 * @static
	 */		
	function quicksort( &$rowdata, $sortBy, $first, $last )
	{
		$lo    = $first;
		$up    = $last;
		$i     = $first + $last;
		$bound = strval( $rowdata[( $i - $i % 2 ) / 2][$sortBy] );

		while ( $lo <= $up )
		{
    		while ( ( $lo <= $last - 1) && ( strval( $rowdata[$lo][$sortBy] ) < $bound ) )
				$lo++;
    
			while ( ( $up >= 1 ) && ( $bound < strval( $rowdata[$up][$sortBy] ) ) )
				$up--;
    
			if ( $lo < $up )
    		{
      			$tmp = $rowdata[$up];
      			$rowdata[$up] = $rowdata[$lo];
      			$rowdata[$lo] = $tmp;
				
      			$up--;
      			$lo++;
    		}
    		else
    		{
      			$lo++;
    		}
  		}
  
	  	if ( $first < $up )
			SortUtil::quicksort( $rowdata, $sortBy, $first, $up );
  	
		if ( $up + 1 < $last )
			SortUtil::quicksort( $rowdata, $sortBy, $up + 1, $last );
	}

	/**
	 * @access public
	 * @static
	 */		
	function flipsort( &$ArrayIn, $Ascending )
	{
		for ( $j = sizeof( $ArrayIn ); $j >= 1; $j-- )
		{
			$pLargest = 0;
		
			for ( $i = 0; $i < $j; $i++ )
			{
				if ( $ArrayIn[$i] > $ArrayIn[$pLargest] )
					$pLargest = $i;
			}
		
			SortUtil::_flip( $ArrayIn, $pLargest );
			SortUtil::_flip( $ArrayIn, $j - 1 );
		}

		if ( !( $Ascending ) )
			SortUtil::_flip( $ArrayIn, sizeof( $ArrayIn ) - 1 );
	}	

	
	// private methods

	/**
	 * @access private
	 * @static
	 */		
	function _flip( &$ArrayIn, $FlipRatio )
	{
		$j = 0;
	
		for ( $i = $FlipRatio; $i >= 0; $i-- )
		{
			$temp[$j] = $ArrayIn[$i]; 
			$j++;
		}
		
		for ( $i = $FlipRatio + 1; $i < sizeof( $ArrayIn ); $i++ )
			$temp[$i] = $ArrayIn[$i];    
		
		$ArrayIn = $temp;	
	}
} // END OF SortUtil

?>
