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
 
class Statistics extends PEAR
{
	/**
	 * @access public
	 * @static
	 */
	function mean( &$array ) 
	{ 
		$average = 0; 

		while ( list( $key, $val ) = each( $array ) ) 
			$average += $val; 

		reset( $array ); 
		$average /= count( $array ); 

		return $average; 
	} 

	/**
	 * @access public
	 * @static
	 */
	function skewnessAndKurtosis( $array ) 
	{ 
		$skew   = "N/A"; 
		$kurt   = "N/A"; 
		$amount = count( $array );

		if ( $amount > 2 ) 
		{ 
			for ( $i = 0, $m2 = 0, $m3 = 0, $m4 = 0; $i < $amount; $i++ ) 
			{ 
				$array [$i] -= Statistics::mean( $array ); 
				$m2 += pow( $array[$i], 2 ); 
				$m3 += pow( $array[$i], 3 ); 
				$m4 += pow( $array[$i], 4 ); 
			} 

			$m2 /= $amount; 
			$m3 /= $amount; 
			$m4 /= $amount; 
			
			$skew  = $m3 / pow( $m2, 1.5 ); 
			$skew *= sqrt( $amount * ( $amount - 1 ) ) / ( $amount - 2 ); 

			if ( $amount > 3 ) 
			{ 
				$kurt  = ( $m4 / pow( $m2, 2 ) ) - 3; 
				$kurt  = ( ( $amount + 1 ) * $kurt ) + 6; 
				$kurt *= ( $amount - 1 ) / ( ( $amount - 2 ) * ( $amount - 3 ) ); 
			} 
		}
		
		return array(
			skew     => $skew,
			kurtosis => $kurt
		);
	}
	
	/**
	 * This is a statistic method that accepts one or two unidimensional arrays of data. 
	 * 
	 * If only one array is sent, it will return the min, max, sum, median, average and standard 
	 * deviation for this array. If two arrays are sent, in addition to these values for both 
	 * arrays, a linear regression line is computed: m is the slope b is the Yaxis intersect, 
	 * r is the correlation, a measure of the quality of the data fit by the regression line (r=1 
	 * being a perfect fit) and t is the "t"test, to test whether the association between X and Y 
	 * data is real or merely apparent, I leave the user to interpret this test himself. (See the 
	 * tutorial and the table at: http://www.bmj.com/collections/statsbk/apptabb.html).
	 *
	 * The regression line will be calculated only if both arrays have the same number of samples. 
	 * Note that it is the responsibility of the user to make sure that his data are in linear form 
	 * (For the linear regression to work, Median and Average values should be close). 
	 * I tried to use the histogram class wrote by Jesus Castagnetto as an inheritance for 
	 * this script, but it was too complicated since that I needed two arrays. Probably the other 
	 * way around, using Stat1 as an inheritance for an histogram class, will be easier now. 
	 *
	 * @access public
	 * @static
	 */
    function getStats( $X, $Y ) 
	{ 
        // Check if we got a valid set of data
		if ( !( count( $X ) > 1 ) )
			return PEAR::raiseError( "Not enough data." );
			
        $N = count( $X )
		
        // initialize values
        $MINX = (float)min( $X ); 
        $MAXX = (float)max( $X );     
         
        // compute X Median
        // Sort values in array X 
        $XX = $X; 
        sort( $XX );
		 
        if ( $N % 2 == 0 ) 
            $MEDX = ( ( $XX[( $N ) / 2] ) + ( $XX[ -1 + ( $N / 2 )] ) ) / 2; 
        else 
            $MEDX = $XX[floor( $N / 2 )];
         
        $NY   = count( $Y ); 
        $MINY = (float)min( $Y ); 
        $MAXY = (float)max( $Y ); 
         
        // Compute Y Median 
        // Sort values in array Y 
        $YY = $Y; 
        sort( $YY );
		 
        if ( $NY % 2 == 0 ) 
            $MEDY = ( ( $YY[( $NY ) / 2] ) + ( $YY[ -1 + ( $NY / 2 )] ) ) / 2; 
        else 
            $MEDY = $YY[floor( $NY / 2 )]; 
        
        // stats
        for ( $i = 0; $i < $N ; $i++ ) 
		{ 
            $SUMX  += (float)$X[$i]; 
            $SUMX2 += (float)pow( $X[$i], 2 ); 
            $SUMXY += (float)$X[$i] * (float)$Y[$i]; 
        }
		
        $AVGX  = (float)$SUMX / (float)$N; 
        $STDVX = (float)sqrt( ( $SUMX2 - $N * pow( $AVGX, 2 ) ) / (float)( $N - 1 ) ); 
         
        for ( $i = 0; $i < $NY ; $i++ ) 
		{ 
            $SUMY  += (float)$Y[$i]; 
            $SUMY2 += (float)pow( $Y[$i], 2 ); 
        }
		
        $AVGY  = (float)$SUMY / (float)$NY; 
        $STDVY = (float)sqrt( ( $SUMY2 - $NY * pow( $AVGY, 2 ) ) / (float)( $NY - 1 ) ); 
         
        if ( $NY == $N )
		{ 
            $DEV = (float)( ( $SUMX2 * $N    ) - ( $SUMX  * $SUMX ) ); 
            $m   = (float)( ( $SUMXY * $N    ) - ( $SUMX  * $SUMY ) ) / $DEV; 
            $b   = (float)( ( $SUMX2 * $SUMY ) - ( $SUMXY * $SUMX ) ) / $DEV; 
            $r   = (float)( $SUMXY -( $N * $AVGX * $AVGY ) ) / ( ( $N - 1 ) * $STDVX * $STDVY ); 
            $t   = (float)$r * sqrt( ( $N - 2 ) / ( 1- pow( $r, 2 ) ) ); 
        } 

        return array ( 
			N     => $N, 
			NY    => $NY, 
			MINX  => $MINX, 
			MAXX  => $MAXX, 
			SUMX  => $SUMX, 
			MEDX  => $MEDX, 
			AVGX  => $AVGX, 
			STDVX => $STDVX, 
			MINY  => $MINY, 
			MAXY  => $MAXY, 
			SUMY  => $SUMY, 
			MEDY  => $MEDY, 
			AVGY  => $AVGY, 
			STDVY => $STDVY, 
			m     => $m, 
			b     => $b, 
			r     => $r, 
			t     => $t 
		); 
    } 
	
	/**
	 * @access public
	 * @static
	 */
	function maxSizeOfLevel( $array, $level ) 
	{
		if ( !is_array( $array ) ) 
			return 0;
		
		if ( $level == 1 ) 
			return sizeof( $array );
		
		$ret = 0;
		while ( list( $k ) = each( $array ) ) 
		{
			$t = sizeof( $array[$k] );
			
			if ( $t > $ret ) 
				$ret = $t;
		}

		return $ret;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function max( $value_arr )
	{
		$max = $value_arr[0];
		$i = 0;
		$arr_index = $i;
		
		foreach ( $value_arr as $val )
		{
            if ( $val > $max )
			{
                $max = $val;
                $arr_index = $i;
            }
			
            $i++;
        }
		
        $return["arr_index"] = $arr_index;
        $return["max_val"]   = $max;

        return $return;
    }
    
	/**
	 * @access public
	 * @static
	 */
    function min( $value_arr )
	{
        $min = $value_arr[0];
        $i = 0;
        $arr_index = $i;
        
		foreach ( $value_arr as $val )
		{
            if ( $val < $min )
			{
                $min = $val;
                $arr_index = $i;
            }
			
            $i++;
        }
		
        $return["arr_index"] = $arr_index;
        $return["min_val"]   = $min;
        
		return $return;
    }
	
	/**
	 * @access public
	 * @static
	 */
	function numericSum( $val_array = array() )
	{
        $sum = 0;
        
		foreach ( $val_array as $val )
		{
			if ( is_numeric( $val ) )
            	$sum += $val;
		}
		        
		return $sum;
    }
	
	/**
	 * Returns true if $array1 is identical to $array2.
	 *
	 * @access public
	 * @static
	 */
	function equalArrays( $array1, $array2 ) 
	{
		if ( !( is_array( $array1 ) || is_object( $array1 ) ) || !( is_array( $array1 ) || is_object( $array2 ) ) ) 
			return $array1 == $array2;
	
		reset( $array1 ); 
		reset( $array2 );
	
		while ( 1 ) 
		{
			$s1 = each( $array1 ); 
			$s2 = each( $array2 );
		
			if ( $s1 === false && $s2 === false ) 
				return true;
		
			if ( $s1 === false xor $s2 === false ) 
				return false;
		
			list( $k1, $v1 ) = $s1;
			list( $k2, $v2 ) = $s2;
		
			if ( $k1 != $k2 ) 
				return false;
		
			if ( ( $t = gettype( $v1 ) ) != gettype( $v2 ) ) 
				return false;
			
			switch ( $t ) 
			{
				case "array":
					if ( !Statistics::equalArrays( $v1, $v2 ) ) 
						return false;
				
				case "object":
					if ( !get_class( $v1 ) == get_class( $v2 ) ) 
						return false;
			
					if ( !Statistics::equalArrays( $v1, $v2 ) ) 
						return false;
		
				default:
					if ( $v1 != $v2 ) 
						return false;
					
					break;
			}
		}
  
  		return true;
	}
	
	/**
	 * Takes two plain arrays and compares their contents.
	 * It returns two arrays - a list of elements missing from
	 * second array, and a list of those missing from the first.
	 *
	 * @access public
	 * @static
	 */
	function arrayCompare( &$n, &$o ) 
	{
		if ( !is_array( $n ) ) 
			$n = array();
	
		if ( !is_array( $o ) ) 
			$o = array();
	
		$c = array_intersect( $n, $o );
		return array( array_diff( $n, $c ), array_diff( $o, $c ) );
	}
} // END OF Statistics

?>
