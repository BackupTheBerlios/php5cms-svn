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


define( "MATH_HEX",     16 );
define( "MATH_BINARY",   2 );
define( "MATH_OCT",      8 );
define( "MATH_BASE10",  10 );
define( "MATH_DECIMAL", 10 );
		

/**
 * Static utility functions.
 *
 * @package util_math
 */
 
class MathUtil extends PEAR
{
	// arithmetical methods
	
	/**
 	 * Determines the sign of a number.
 	 * -1 if it is negative, +1 in any other case
	 *
	 * @access public
	 * @static
 	 */
	function sign( $n ) 
	{
		return ( $n >= 0 )? 1 : -1;
	}

	/**
 	 * Determines if a number is odd.
 	 * returns 1 on true, 0 on false
	 *
	 * @access public
	 * @static
 	 */
	function isOdd( $n ) 
	{
		return ( $n & 1 );
	}


	// geometrical methods

	/**
	 * @access public
	 * @static
	 */	
	function printPoint( $point ) 
	{
		$str = implode( ", ", $point );
		return "( " . $str . " )";
	}

	/**
	 * @access public
	 * @static
	 */
	function printPointList( $pointlist ) 
	{
		for ( $i = 0; $i < count( $pointlist ); $i++ )
			$tmp .= MathUtil::printPoint( $pointlist[$i] ) . "\n";
	
		return $tmp;
	}

	/**
 	 * Definitions:
 	 * point = an array of coordinates, e.g. $point = array(x,y,z,...)
 	 * pointlist = an array of points
	 *
	 * @access public
	 * @static
 	 */
	function distance( $point1, $point2 ) 
	{
		// check that both points are of equal dimensionality
		if ( count( $point1 ) != count( $point2 ) ) 
			return PEAR::raiseError( 'Points are not of equal dimensionality.' );
		
		// calculate the cartesian distance
		for ( $i = 0; $i < count( $point1 ); $i++ )
			$sum2 +=  pow( ( doubleval( $point1[$i] ) - doubleval( $point2[$i] ) ), 2 );	
	
		return sqrt( $sum2 );
	}

	/**
	 * @access public
	 * @static
	 */
	function areWithinDistance( $point1, $point2, $cutoff )
	{
		$dist = MathUtil::distance( $point1, $point2 );
	
		if ( !PEAR::isError( $dist ) )
			return $dist <= doubleval( $cutoff );
		else
			return null;
	}

	/**
	 * @access public
	 * @static
	 */
	function pointsWithinDistance( $point, $pointlist, $cutoff ) 
	{
		$pcloser = array();
	
		for ( $i = 0; $i < count( $pointlist ); $i++ ) 
		{
			$bool = MathUtil::areWithinDistance( $point, $pointlist[$i], $cutoff );
		
			if ( !PEAR::isError( $bool ) ) 
			{
				if ( $bool )
					$pcloser[] = $pointlist[$i];
			} 
			else 
			{
				return null;
			}
		}
	
		return $pcloser;
	}

	/**
 	 * The following functions handle 2 and 3 dimensional points
 	 * and are completely untested
	 *
	 * @access public
	 * @static
 	 */
	function areColinear( $pointlist ) 
	{
		// check dimensions
		$dim = count( $pointlist[0] );
	
		switch ( $dim ) 
		{
			case 2 :
				if ( count( $pointlist ) < 2 )
					return PEAR::raiseError( 'Insuficient number of points to test colinearity.' );
					
				// get line coefficients
				// y = a + bx
				$p1 = $pointlist[0];
				$p2 = $pointlist[1];
				$b  = (float)( $p2[1] - $p1[1] ) / ( $p2[0] - $p1[0] );
				$a  = (float)$p1[1] - $p[0] * $b;
				
				for ( $i < 0; $i < count( $pointlist ); $i++ ) 
				{
					$tp = $pointlist[$i];
				
					if ( $tp[1] != $a + $b * $tp[0] )
						return false;
				}
			
				return true;
				break;

			case 3 :
				if ( count( $pointlist ) < 3 )
					return PEAR::raiseError( 'Insuficient number of points to test colinearity.' );
					
				// get line coefficients
				// z = a + bx + cy
				$p1 = $pointlist[0];
				$p2 = $pointlist[1];
				$p3 = $pointlist[2];

				$dz12 = $p2[2] - $p1[2];
				$dz13 = $p3[2] - $p1[2];
				$dy12 = $p2[1] - $p1[1];
				$dy13 = $p3[1] - $p1[1];
				$dx12 = $p2[0] - $p1[0];
				$dx13 = $p3[0] - $p1[0];
			
				$c = ( ( $dz13 * $dx12 ) - ( $dz12 * $dx13) ) / ( ( $dy13 * $dx12 ) - ( $dy12 * $dx13 ) );
				$b = ( $dz12 - ( $dy12 * $c ) ) / $dx12;
				$a = $p1[2] - ( $b * $p1[0] ) - ( $c * $p1[1] ); 
			
				for ( $i < 0; $i < count( $pointlist ); $i++ ) 
				{
					$tp = $pointlist[$i];
				
					if ( $p1[2] != $a + ( $b * $p1[0] ) + ( $c * $p1[1] ) )
						return false;
				}
			
				return true;
				break;

			default:
				return PEAR::raiseError( 'Not implemented for dimensions other that 2 or 3.' );
				break;
		}
	}
	
	
	// stat methods

	/**
	 * @access public
	 * @static
	 */	
	function sumN( $numarr, $n ) 
	{
		if ( count( $numarr ) > 1 )
			return pow( $numarr[0], $n ) + MathUtil::sumN( array_slice( $numarr, 1 ), $n );
		else
			return pow( $numarr[0], $n );
	}

	/**
	 * @access public
	 * @static
	 */
	function sum( $numarr ) 
	{
		return MathUtil::sumN( $numarr, 1 );
	}

	/**
	 * @access public
	 * @static
	 */
	function sum2( $numarr ) 
	{
		return MathUtil::sumN( $numarr, 2 );
	}

	/**
	 * @access public
	 * @static
	 */	
	function mean( $numarr ) 
	{
		$n = count( $numarr );
		return ( MathUtil::sum( $numarr ) / $n );
	}

	/**
	 * @access public
	 * @static
	 */
	function estVariance( $numarr ) 
	{
		$n = count( $numarr );
		$mean = MathUtil::mean( $numarr );
	
		return MathUtil::_sumvar( $numarr, $mean ) / ( $n - 1 );
	}

	/**
	 * @access public
	 * @static
	 */
	function estSd( $numarr ) 
	{
		return sqrt( MathUtil::estVariance( $numarr ) );
	}

	/**
	 * @access public
	 * @static
	 */
	function varianceWithMean( $numarr, $mean ) 
	{
		$n = count( $numarr );
		return MathUtil::_sumvar( $numarr, $mean ) / $n;
	}

	/**
	 * @access public
	 * @static
	 */
	function sdWithMean( $numarr, $mean ) 
	{
		return sqrt( MathUtil::varianceWithMean( $numarr, $mean ) );
	}

	/**
	 * @access public
	 * @static
	 */
	function absDeviation( $numarr ) 
	{
		$n = count( $numarr );
		$mean = MathUtil::mean( $numarr );
	
		return MathUtil::_sumabsdev( $numarr, $mean ) / $n;
	}

	/**
	 * @access public
	 * @static
	 */
	function absDeviationWithMean( $numarr, $mean ) 
	{
		$n = count( $numarr );
		return MathUtil::_sumabsdev( $numarr, $mean ) / $n;
	}

	/**
	 * @access public
	 * @static
	 */
	function momentN( $numarr, $power ) 
	{
		$n    = count( $numarr );
		$mean = MathUtil::mean( $numarr );
		$sd   = MathUtil::estSd( $numarr );
		
		for ( $i = 0; $i < $n; $i++ )
			$msum += pow( ( $numarr[$i] - $mean ) / $sd, $power );
	
		return $msum / $n;
	}

	/**
	 * @access public
	 * @static
	 */
	function skewness( $numarr ) 
	{
		$n = count( $numarr );
		$s = $n * $n * MathUtil::momentN( $numarr, 3 ) / ( ( $n - 1 ) * ( $n - 2 ) );
	
		return $s;
	}

	/**
	 * @access public
	 * @static
	 */
	function skewnessBig( $numarr ) 
	{
		return MathUtil::momentN( $numarr, 3 );
	}

	/**
	 * @access public
	 * @static
	 */
	function kurtosis( $numarr ) 
	{
		$n    = count( $numarr );
		$num  = $n * ( $n - 1 ) * MathUtil::_sumdiff( $numarr, 4 );
		$num -= 3  * ( $n - 1 ) * pow( MathUtil::_sumdiff( $numarr, 2 ), 2 );
		$den  = ( $n - 1 ) * ( $n - 2 ) * ( $n - 3 ) * MathUtil::estSd( $numarr );
	
		return $num / $den;
	}

	/**
	 * @access public
	 * @static
	 */
	function kurtosisBig( $numarr ) 
	{
		return MathUtil::momentN( $numarr, 4 ) - 3;
	}

	/**
	 * @access public
	 * @static
	 */
	function median( $numarr ) 
	{
		$n = count( $numarr );
		sort( $numarr );

		if ( $n & 1 )
			return $numarr [( $n - 1 ) / 2];
		else
			return ( $numarr [( $n - 1 ) / 2] + $numarr [$n / 2] ) / 2;
	}

	/**
	 * @access public
	 * @static
	 */
	function mode( $numarr ) 
	{
		for ( $i = 0;  $i < count( $numarr ); $i++ ) 
		{
			$key = (string)$numarr[$i];
			$countarr[$key]++;
		}
			
		arsort( $countarr );
		return key( $countarr );
	}

	
	// trigonometric methods

	/**
	 * @access public
	 * @static
	 */	
	function sec( $x ) 
	{
		return 1 / cos( $x );
	}

	/**
	 * @access public
	 * @static
	 */
	function csc( $x ) 
	{
		return 1 / sin( $x );
	}

	/**
	 * @access public
	 * @static
	 */
	function cot( $x ) 
	{
		return 1 / tan( $x );
	}

	
	// hyperbolic methods

	/**
	 * @access public
	 * @static
	 */
	function sinh ( $x ) 
	{
		return ( exp( $x ) - exp( -1 * $x ) ) / 2;
	}

	/**
	 * @access public
	 * @static
	 */
	function cosh( $x ) 
	{
		return ( exp( $x ) + exp( -1 * $x ) ) / 2;
	}

	/**
	 * @access public
	 * @static
	 */
	function csch( $x ) 
	{
		return 1 / sinh( $x );
	}

	/**
	 * @access public
	 * @static
	 */
	function sech( $x ) 
	{
		return 1 / cosh( $x );
	}

	/**
	 * @access public
	 * @static
	 */
	function tanh( $x ) 
	{
		return sinh( $x ) / cosh( $x );
	}

	/**
	 * @access public
	 * @static
	 */
	function coth( $x ) 
	{
		return cosh( $x ) / sinh( $x );
	}

	
	// inverse hyperbolic methods

	/**
	 * @access public
	 * @static
	 */
	function arcsinh( $x ) 
	{
		return log( $x + sqrt( $x * $x + 1 ) );
	}

	/**
	 * @access public
	 * @static
	 */
	function arccosh( $x, $sign = 1 ) 
	{
		return log( $x + $sign * sqrt( $x * $x - 1 ) );
	}

	/**
	 * @access public
	 * @static
	 */
	function arctanh( $x ) 
	{
		return 0.5 * log( ( 1 + $x ) / ( 1 - $x ) );
	}

	/**
	 * @access public
	 * @static
	 */
	function arccsch( $x ) 
	{
		return log( ( 1 + sqrt( 1 + $x * $x ) ) / $x );
	}

	/**
	 * @access public
	 * @static
	 */
	function arcsech( $x, $sign = 1 ) 
	{
		return log( ( 1 + $sign * sqrt( 1 - $x * $x ) ) / $x );
	}

	/**
	 * @access public
	 * @static
	 */
	function arccoth( $x ) 
	{
		return 0.5 * log( ( $x + 1 ) / ( $x - 1 ) );
	}
	
	
	// misc
	
	/**
	 * With numconv(.,.,.,.) you can convert a number from one numbersystem to another. Some examples:
	 * MathUtil::numconv(10,16,   255  , 4); returns     00ff: converts the decimal number 100 to the hexadecimal value. Output must be 4 characters long.
	 * MathUtil::numconv(16, 8,   abc34, 4); returns  2536064: converts the hexadecimal number abc34 to the octal value. Output must be 4 characters long.
	 * MathUtil::numconv( 2, 8,   11101, 0); returns       35: converts the binary number 11101 to the octal value. Output remains normal length.
	 * MathUtil::numconv( 6, 5,      20, 8); returns 00000022: converts the six-system value 20 to the five-system value. Output must be 8 characters long.
	 * MathUtil::numconv( 2,10,   11101, 6); returns   000029: converts the binary number 11101 to the decimal value. Output must be 6 characters long.
	 * MathUtil::numconv(10,30,59851949, 0); returns   2dqm4t: converts the decimal number 59851949 to the thirty-system value. Output remains normal length.
	 *
	 * @access public
	 * @static
	 */
	function numconv( $systemin, $systemout, $number, $length )
	{
    	if ( !is_numeric( $systemin ) || !is_numeric( $systemout ) || $systemin < 2 || $systemin > 36 || $systemout < 2 || $systemout > 36 )
			return PEAR::raiseError( 'Define system-in and system-out only in numeric value between 2 and 36.' );
    
		// don't need to calculate a decimal value from a decimal value
		if ( $systemin != 10 )
		{
      		$number    = strtolower( $number );
      		$numlength = strlen( $number ); 
      		$decnumber = 0; 
      
	  		for ( $i = 0; $i < $numlength; $i++ )
			{
        		$operand = substr( $number, -1 - $i, 1 );
        
				// alphabetic
				if ( ord( $operand ) > 96 && ord( $operand ) < 123 )
					$operand = ord( $operand ) - 87;
        
        		if ( $operand >= $systemin )
          			return PEAR::raiseError( 'Illegal number.' );
				
        
				$decnumber += $operand * pow( $systemin, $i );
      		}
    	}
    	else
		{
      		$decnumber = $number;
    	}
    
		if ( $decnumber > 214748367 )
			return PEAR::raiseError( 'Number may not be higher than decimal 214748367.' );
    
    	settype( $decnumber, "double" );
    	settype( $systemout, "double" );
    
		// don't need to calculate a decimal value to a decimal value
		if ( $systemout != 10 )
		{
      		$returnval = "";
      
	  		while ( $decnumber > 0 )
			{
        		$remainder = ( (double)$decnumber ) % $systemout;
        
				if ( $remainder < 10 )
          			$returnval = $remainder . $returnval;
        		else
          			$returnval = chr( $remainder + 87 ) . $returnval;
        
				$decnumber = floor( $decnumber / $systemout );
      		}
    	}
    	else
		{
      		$returnval = $decnumber;
    	}
		
		while ( strlen( $returnval ) < $length )
			$returnval = "0" . $returnval;
    
		return $returnval;
  	}

	/**
	 * @access public
	 * @static
	 */  
	function piSearch( $max ) 
    { 
        /* PI Search! - method 2*/ 
        // In the books: PI /2 = (2/1)*(2/3)*(4/3)*(4/5)*(6/5)(6/7) 
        // got from PHP doc -> PI: 3.14159265358979323846 
        // How much greater $max will, more exact the value will be 

        $dw   = 1; 
        $l    = 1; 
        $up   = 2; 
        $pi   = 4;
		
        for ( $x = 2; $x <= $max; $x++ ) 
        { 
            $l++; 
            
			if ( $l % 2 != 0 ) 
                $up = $x + 1; 
            else if ( $l % 2 == 0 ) 
                $dw += 2; 

            $pi = $pi * ( $up / $dw ); 

            if ( $l == 4 ) 
                $l = 0; 
        }
		
        return $pi; 
    } 
	
	/**
	 * The math expression must to be between '(' and ')'
	 * 
	 * Usage:
	 * 
     * $percent = 10;
     * $str     = "The result of [[40+15]*2] + $percent% IS (((40+15)*2)+ $percent%)";
	 * $result  = MathUtil::evalMathExpression($str);
	 * 
     * echo $result;
	  * 
	 * @access public
	 * @static
	 */
	function evalMathExpression( $inputString )
	{
		$matches = preg_match_all( "/\(+[0-9+\s+\-.*\/()%]+\)/", $inputString, $m_matches );
		
		for ( $i = 0; $i < $matches; $i++ )
		{
			$formula = str_replace( " ", "", $m_matches[0][$i] );
			$formula = preg_replace( "/([+-])([0-9]+)(%)/", "*(1\$1.\$2)", $formula );
			$formula = preg_replace( "/([0-9]+)(%)/", ".\$1", $formula );
			
			eval( "\$result=" . $formula . ";" );
			
			$pos = strpos( $inputString, $m_matches[0][$i] );
			$inputString = substr_replace( $inputString, $result, $pos, strlen( $m_matches[0][$i] ) );
		}
		
		return $inputString;
	}
	
	/**
	 * Takes two paramaters ($int1, $int2) and uses them to calculate the percentage.
	 *
	 * @access public
	 * @static
	 */
	function percentage( $int1, $int2 )
	{
		$per = $int1 / $int2;
		$res = $per * 100;
		
		return $res;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function getPrime( $floor, $limit )
	{
		$count = 0;
		$to_test = $floor;
		
		while ( $to_test < $limit )
		{
			$testdiv = 2;
			
			while ( true )
			{
				if ( $testdiv > sqrt( $to_test ) )
				{
					// print "$count - $to_test\n<br>";
					$count++;
					break;
				}
				
				if ( $to_test % $testdiv == 0 )
					break;
				
				$testdiv++;
			}
			
			$to_test++;
		}
	}
	
	/**
	 * Example:
	 * $result = MathUtil::checkPrimeNumber( 11 ); 
	 * echo( $result );
	 *
	 * @return boolean
	 * @access public
	 * @static
	 */
	function checkPrimeNumber( $f )
	{
        $no = 0; 

		for ( $b = 2 ; $b <= $f ; $b++ )
		{ 
			for( $d = 2 ; $d < $b ; $d++ )
			{ 
				$res = $b / $d; 
				
				if ( $res!=1 && intval( $res ) == $res )
				{ 
					$no = 1; 
					$d  = $b; 
				} 
			} 

			if ( $no != 1 )
				$result = $b; 
               
			$no = 0; 
        } 

		if ( $result == $f )
			return true; 
		else
			return false; 
	}
	
	/**
	 * Use to convert a number in any base from 2 to 36 to any other base from 2 to 36.
  	 * Example: MathUtil::convertBase("FF",16,10) returns 255. You can also use MATH_HEX, MATH_BINARY, MATH_BASE10, MATH_DECIMAL and MATH_OCT in place of numeric
	 * values for the bases. ie: MathUtil::convertBase("FF",MATH_HEX,MATH_BINARY) returns 11111111.
	 *
	 * @access public
	 * @static
	 */
  	function convertBase( $number, $fromBase = 10, $toBase = 2 )
	{
		// check base validity
  		if ( ( $toBase > 36 || $toBase < 2 ) || ( $fromBase > 36 || $fromBase < 2 ) )
			return false;
    
		@list( $number, $decimal ) = explode( ".", $number );
		
		// convert to base 10
		for ( $i = 0; $i < strlen( $number ); $i++ )
		{
			$digit = substr( $number, $i, 1 );
			
			if ( eregi( "[a-z]", $digit ) )
			{
				$x = ord( $digit ) - 65 + 10;
				
				if ( $x > $fromBase )
					$x -= 32;
				
				$digit = $x;
			}
			
			@$base10 += $digit * ( pow( $fromBase, strlen( $number ) - $i - 1 ) );
		}
		
		$number = $base10;
		
		if ( $toBase == 10 )
			return $number;
		
		$q = $number;
		
		// convert base 10 equivalent to specified base
		while ( $q != 0 )
		{
			$r = $q % $toBase;
			$q = floor( $q / $toBase );
			
			if ( $r > 9 )
				$r = chr( ( $r - 9 ) + 64 );
				
			@$baseres = "$r" . "$baseres";
		}
		
		return $baseres;
	}

	
	// Fibonacci
	
	/**
	 * fibo1 is recursive
	 * Example:
	 * for ( $n = 0; $n <= $argv[1]; ++$n ) { echo MathUtil::fibo1( $n ) . ","; }
	 *
	 * @access public
	 * @static
	 */
	function fibo1( $i )
	{
		if ( $i == 0 )
			return true;
			
		if ( $i == 1 )
			return true;
			
		return MathUtil::fibo1( $i - 1 ) + MathUtil::fibo1( $i - 2 );
	}

	/**
	 * fibo2 is non-recursive
	 *
	 * @access public
	 * @static
	 */
	function fibo2( $i )
	{
		if ( $i == 0 )
			return true;
	
		if ( $i == 1 )
			return true;
			
		$total = 2;
		$left  = $right = 1;
		$depth = 2;
		
		while ( $i >= $depth )
		{
			$total = $left + $right;
			$depth++;
			$right = $left;
			$left  = $total;
		}
		
		return $total;
	}
	
	
	/**
	 * Function that run on mean or average and skewness and kurtosis.
	 *
	 * Example:
	 * $samplearray = array( 1.8, 1.9, 1.2, 1.5, 1.7 ); 
	 * MathUtil::skewnessandkurtosis( $samplearray, $skew, $kurt ); 
	 * echo "<p>Data average = ". MathUtil::mean( $samplearray ) . "</p>"; 
	 * echo "<p>skewness = $skew</p>"; 
	 * echo "<p>kurtosis = $kurt</p>";
	 *
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
	function skewnessandkurtosis( $array, &$skew, &$kurt )
	{ 
		$skew   = "N/A"; 
		$kurt   = "N/A"; 
		$amount = count( $array ); 

		if ( $amount > 2 )
		{ 
			for ( $i = 0, $m2 = 0, $m3 = 0, $m4 = 0; $i < $amount; $i++ )
			{ 
				$array[$i] -= MathUtil::mean( $array ); 
				$m2 += pow( $array[$i], 2 ); 
				$m3 += pow( $array[$i], 3 ); 
				$m4 += pow( $array[$i], 4 ); 
			}
			 
			$m2 /= $amount; 
			$m3 /= $amount; 
			$m4 /= $amount; 
			
			$skew = $m3 / pow( $m2, 1.5 ); 
			$skew *= sqrt( $amount * ( $amount - 1 ) ) / ( $amount - 2 ); 

			if ( $amount > 3 )
			{ 
				$kurt  = ( $m4/ pow( $m2, 2 ) ) - 3; 
				$kurt  = ( ( $amount + 1 ) * $kurt ) + 6; 
				$kurt *= ( $amount - 1 ) / ( ( $amount - 2 ) * ( $amount - 3 ) ); 
			}
		} 
	}
	
	/**
	 * Forces the integer $theInt into the boundaries of $min and $max. If the $theInt is "false" then the $zeroValue is applied.
	 *
	 * @access public
	 * @static
	 */
	function intInRange( $theInt, $min, $max = 2000000000, $zeroValue = 0 )	
	{
		// Returns $theInt as an integer in the integerspace from $min to $max
		$theInt = intval( $theInt );
		
		// If the input value is zero after being converted to integer, zeroValue may set another default value for it.
		if ( $zeroValue && !$theInt )
			$theInt = $zeroValue;	
					
		if ( $theInt < $min )
			$theInt = $min;
		
		if ( $theInt>$max )
			$theInt = $max;
		
		return $theInt;
	}
	
	/**
	 * Converts an integer to the corresponding letter string.. e.g 1=A, 26=Z, 27=AA.
	 *
	 * @access public
	 * @static
	 */
	function intToLetterCode( $num ) 
	{
		$num = (int)$num;
	
		if ( $num <= 0 ) 
			return "";
	
		$ord = $num % 26;
	
		if ( !$ord )
			$ord = 26;
	
		$l = chr( 64 + $ord );
	
		if ( $num > 26 )
			$l = MathUtil::intToLetterCode( $num / 26 ) . $l;
		
		return $l;
	}

	/**
	 * @access public
	 * @static
	 */
	function dec2roman( $number )
	{
		// Making input compatible with script.
		$number = floor( $number );
		
		if ( $number < 0 )
		{
			$linje  = "-";
			$number = abs( $number );
		}
	
		// defining arrays
		$romanNumbers = array( 1000, 500, 100, 50, 10, 5, 1 );
		$romanLettersToNumbers = array( "M" => 1000, "D" => 500, "C" => 100, "L" => 50, "X" => 10, "V" => 5, "I" => 1 );
		$romanLetters = array_keys( $romanLettersToNumbers );
	
		// Looping through and adding letters.
		while ( $number )
		{
			for ( $pos = 0; $pos <= 6; $pos++ )
			{
				// Dividing the remaining number with one of the roman numbers.
				$dividend = $number / $romanNumbers[$pos];

				// If that division is >= 1, round down, and add that number of letters to the string.
				if ( $dividend >= 1 )
				{
					$linje .= str_repeat( $romanLetters[$pos], floor( $dividend ) );

					// Reduce the number to reflect what is left to make roman of.
					$number -= floor( $dividend ) * $romanNumbers[$pos];
				}
			}
		}

		// If I find 4 instances of the same letter, this should be done in a different way.
		// Then, subtract instead of adding (smaller number in front of larger).
		$numberOfChanges = 1;
		
		while ( $numberOfChanges )
		{
			$numberOfChanges = 0;

			for ( $start = 0; $start < strlen( $linje ); $start++ )
			{
				$chunk = substr( $linje, $start, 1 );
				
				if ( $chunk == $oldChunk && $chunk != "M" )
				{
					$appearance++;
				}
				else
				{
					$oldChunk = $chunk;
					$appearance = 1;
				}

				// Was there found 4 instances.
				if ( $appearance == 4 )
				{
					$firstLetter = substr( $linje, $start - 4, 1 );
					$letter = $chunk;
					
					$sum = $firstNumber + $letterNumber * 4;
					$pos = MathUtil::array_search( $letter, $romanLetters );

					// Are the four digits to be calculated together with the one before? (Example yes: VIIII = IX Example no: MIIII = MIV
					// This is found by checking if the digit before the first of the four instances is the one which is before the digits
					// in the order of the roman number. I.e. MDCLXVI.

					if ( $romanLetters[$pos - 1] == $firstLetter )
					{
						$oldString = $firstLetter . str_repeat( $letter, 4 );
						$newString = $letter . $romanLetters[$pos - 2];
					}
					else
					{
						$oldString = str_repeat( $letter, 4 );
						$newString = $letter . $romanLetters[$pos - 1];
					}
				
					$numberOfChanges++;
					$linje = str_replace( $oldString, $newString, $linje );
				}
			}
		}
		
		return $linje;
	}

	/**
	 * @access public
	 * @static
	 */
	function roman2dec( $linje )
	{
		// Fixing variable so it follows my convention.
		$linje = strtoupper( $linje );
	
		// Removing all not-roman letters
		$linje = ereg_replace( "[^IVXLCDM]", "", $linje );

		// efining variables
		$romanLettersToNumbers = array(
			"M" => 1000,
			"D" => 500,
			"C" => 100,
			"L" => 50,
			"X" => 10,
			"V" => 5,
			"I" => 1
		);

		$oldChunk = 1001;

		// looping through line
		for ( $start = 0; $start < strlen( $linje ); $start++ )
		{
			$chunk = substr( $linje, $start, 1 );
			$chunk = $romanLettersToNumbers[$chunk];
		
			if ( $chunk <= $oldChunk )
				$calculation .= " + $chunk";
			else
				$calculation .= " + " . ( $chunk - ( 2 * $oldChunk ) );
	
			$oldChunk = $chunk;
		}
	
		// summing it up
		eval( "\$calculation = $calculation;" );
		return $calculation;
	}

    /**
	 * Calculates the Greatest Common Divisor (gcd) of two numbers.
	 * int a first number, int b second number
     * return int MathUtil::gcd(a,b)
	 *
	 * @access public
	 * @static
	 */
    function gcd( $a, $b )
    {
        if ( $b > $a )
            list( $a, $b ) = array( $b, $a );

        $c = 1;

        // the magic loop (thanks, Euclid :-)
        while ( $c > 0 )
		{
            $c = $a % $b;
            $a = $b;
            $b = $c;
        }

        return $a;
    }
	
	/** 
	 * Returns the standard deviation of an array of numerical values where $std is the array name.
	 *
	 * $values = "1,2,2,4,4,4,5,5,6,6,3,3,4,8,7"; 
	 * $std    = explode( ",", $values ); 
	 * print MathUtil::standardDeviation( $std );
	 *
	 * @access public
	 * @static
	 */
	function standardDeviation( $std ) 
	{
		$total;
		 
		while ( list( $key, $val ) = each $std ) ) 
        	$total += $val; 
          
		reset( $std ); 
		$mean = $total / count( $std );
		 
		while ( list( $key, $val ) = each( $std ) ) 
			$sum += pow( ( $val - $mean ) , 2 ); 
          
		$var = sqrt( $sum / ( count( $std ) - 1 ) ); 
		return $var;
	}
	
	/**
	 * Calculates the checkdigit, cdv 10, for either a integer or string value.
	 * Cdv10 are often used in account-numbers etc.
	 *
	 * @access public
	 * @static
	 */
	function cdv10( $str ) 
	{ 
		if ( is_string( $str ) ) 
        { 
			for ( $i = 0; $i < strlen( $str ); $i++ ) 
				$out = $out . Ord( substr( $str, $i, 1 ) ); 
        } 
        else
		{ 
            $out = $str; 
		}
		
        // is the length odd or even 
        if ( (int)( strlen( $out ) / 2 ) == (int)( ( strlen( $out ) / 2 ) + 0.9 ) ) 
            $m = 0; 
        else 
            $m = 1; 

        // sum the values for each digit, take care of values > 9 
        for ( $i = 0; $i < strlen( $out ); $i++ ) 
        { 
            $m = ( $m ==1 )? 2 : 1; 
            $v = $m * substr( $out, $i, 1 ); 
            
			if ( $v > 9 ) 
                $v = ( substr( $v, 0, 1 ) + substr( $v, 1, 1 ) ); 
            
			$sum = $sum + $v; 
        } 

        // what is the check digit?? 
        $cd = ( round( $sum / 10 + 0.49 ) * 10 ) - $sum; 
		return $cd; 
	}
	
	/**
	 * Performs the FFT on the *complex* array $data.
	 * Presumes that count($data) is an integer power of two.
	 * $data[even] holds the real portion
	 * $data[odd] hold the imaginary portion
	 * Example: (1 + 2i) ->  $data[0] = 1; $data[1] = 2;
	 * $sign = 1  performs the Fourier Transform
	 * $sign = -1 performs the Inverse Fourier Transform
	 * Use: $fourier_array = MathUtil::fourier( $inputarray, 1 );
	 *
	 * @ccess public
	 * @static
	 */
	function fourier( $input, $isign )
	{
		$data[0] = 0; 
		
		for ( $i = 0; $i < count( $input ); $i++ )
			$data[( $i + 1 )] = $input[$i];

		$n = count( $input );
		$j = 1;

		for ( $i = 1; $i < $n; $i += 2 )
		{
      		if ( $j > $i )
			{
         		list( $data[( $j + 0 )], $data[( $i + 0 )]) = array( $data[( $i + 0 )], $data[( $j + 0 )] );
         		list( $data[( $j + 1 )], $data[( $i + 1 )]) = array( $data[( $i + 1 )], $data[( $j + 1 )] );
      		}

			$m = $n >> 1;

			while ( ( $m >= 2 ) && ( $j > $m ) )
			{
         		$j -= $m;
         		$m = $m >> 1;
      		}

      		$j += $m;
   		}

   		$mmax = 2;

		// outer loop executed log2(nn) times
   		while ( $n > $mmax )
		{
			$istep = $mmax << 1;
			$theta = $isign * 2 * pi() / $mmax;
			$wtemp = sin(0.5 * $theta);
			$wpr   = -2.0 * $wtemp * $wtemp;
			$wpi   = sin( $theta );

			$wr = 1.0;
			$wi = 0.0;
			
			// here are the two nested inner loops
			for ( $m = 1; $m < $mmax; $m += 2 )
			{
				for ( $i = $m; $i <= $n; $i += $istep )
				{
					$j = $i + $mmax;

					$tempr = $wr * $data[$j]     - $wi * $data[( $j + 1 )];
					$tempi = $wr * $data[($j+1)] + $wi * $data[$j];

					$data[$j]      = $data[$i]     - $tempr;
					$data[($j+1)]  = $data[($i+1)] - $tempi;

					$data[$i]     += $tempr;
					$data[($i+1)] += $tempi;
				}
				
				$wtemp = $wr;
				$wr = ( $wr * $wpr ) - ( $wi    * $wpi ) + $wr;
				$wi = ( $wi * $wpr ) + ( $wtemp * $wpi ) + $wi;
			}
			
			$mmax = $istep;
		}

		for ( $i = 1; $i < count( $data ); $i++ )
		{ 
			// Normalize the data.
			$data[$i] *= sqrt( 2 / $n );
			
			// Let's round small numbers to zero.
			if ( abs( $data[$i] ) < 1E-8 )
				$data[$i] = 0;
			
			// We need to shift array back (see beginning).
			$input[( $i - 1 )] = $data[$i];
		}

		return $input;
	}

	/**
	 * @access public
	 * @static
	 */
	function isNumericLarge( $s ) 
	{
		$v = true;
	
		for ( $i = 0; $i < strlen( $s ); $i++ ) 
		{
			if ( ord( substr( $s, $i, 1 ) ) < 48 || ord( substr( $s, $i, 1 ) ) > 57 ) 
				return false;
		}

		return $v;
	}

	/**
	 * @access public
	 * @static
	 */
	function roundNoTrim( $value, $precision = 2 ) 
	{
		$ret  = round( $value, $precision );
		$fill = 0;
	
		do 
		{
			if ( $precision > 0 ) 
			{
				$dotPos = strpos( $ret, '.' );
			
				if ( $dotPos === false ) 
				{
					$ret  .= '.';
					$fill  = $precision;
				
					break;
				}

				$fill = $precision - strlen( substr( $ret, $dotPos + 1 ) );
			
				if ( $fill < 0 ) 
					$fill = 0;
			}
		} while ( false );
	
		for ( $i = 0; $i < $fill; $i++ )
			$ret .= '0';

		return (string)$ret;
	}

	/**
	 * @access public
	 * @static
	 */
	function hexToBin( $source ) 
	{
		$strlen = strlen( $source );
	
		for ( $i = 0; $i < strlen( $source ); $i = $i + 2 ) 
			$bin .= chr( hexdec( substr( $source, $i,2 ) ) );

		return $bin;
	}

	/**
	 * @access public
	 * @static
	 */
	function decimalToFraction( $number ) 
	{
		list( $whole, $numerator ) = explode( '.', $number );
		$denominator = 1 . str_repeat( 0, strlen( $numerator ) );
		$GCD = MathUtil::gcd( $numerator, $denominator );
		$numerator   /= $GCD;
		$denominator /= $GCD;
	
		return sprintf( '%d <sup>%d</sup>/<sub>%d</sub>', $whole, $numerator, $denominator );
	}

	/**
	 * @access public
	 * @static
	 */
    function randomNumber( $min = 0, $max = 100000000000000 )
	{
        return ( mt_rand( $min, $max ) );
    }
	
	/**
	 * This function return a gaussian distribution of floating-point
	 * numbers.  This implementation is recommended by D. Knuth.
	 *
	 * @access public
	 * @static
	 */
	function gaussrand()
	{
		$phase = 0;
	
		if ( phase == 0 ) 
		{
			while ( $S >= 1 || $S == 0 )
			{
				$U1 = (double)rand() / 2147483647;
				$U2 = (double)rand() / 2147483647;
				$V1 = 2 * $U1 - 1;
				$V2 = 2 * $U2 - 1;
				$S = $V1 * $V1 + $V2 * $V2;
			}
		
			$X = $V1 * sqrt( -2 * log( $S ) / $S );
	   	} 
	   	else 
		{
	       	$X = $V2 * sqrt( -2 * log( $S ) / $S );
	   	}
   
	   	$phase = 1 - $phase;
	   	return $X;
	}
	
	/**
	 * @access public
	 * @static
	 */
    function baseConvert( $num, $from )
	{
        $result["binary"]  = base_convert( $num, $from,  2 );
        $result["octal"]   = base_convert( $num, $from,  8 );
        $result["decimal"] = base_convert( $num, $from, 10 );
        $result["hex"]     = base_convert( $num, $from, 16 );
		
        return $result;
    }

	/**
	 * @access public
	 * @static
	 */	
	function hexToBin( $s )
	{
		$n = strlen( $s );
	
		if ( $n % 2 != 0 )
			return;
		
		for ( $x = 1; $x <= $n / 2; $x++ )
			$t .= chr( hexdec( substr( $s, 2 * $x - 2, 2 ) ) );

		return $t;
	}
	
	
	// private methods

	/**
	 * @access private
	 * @static
	 */	
	function _sumdiff( $numarr, $power ) 
	{
		$mean = MathUtil::mean( $numarr );
	
		for ( $i = 0; $i < count( $numarr ); $i++ )
			$sum += pow( ( $numarr[$i] - $mean ), $power );
	
		return $sum;
	}
	
	/**
	 * @access private
	 * @static
	 */	
	function _sumabsdev( $numarr, $mean ) 
	{
		for ( $i = 0; $i < count( $numarr ); $i++ )
			$sumabsdev += abs( $numarr[$i] - $mean );
	
		return $sumabsdev;
	}

	/**
	 * @access private
	 * @static
	 */		
	function _sumvar( $numarr, $mean ) 
	{
		for ( $i = 0; $i < count( $numarr ); $i++ )
			$svar += pow( ( $numarr[$i] - $mean ), 2 );
	
		return $svar;
	}
	
	/**
	 * Implementation of the array_search function. Works only with numerical arrays.
	 *
	 * @access private
	 * @static
	 */
	function _array_search( $searchString, $array )
	{
		foreach ( $array as $content )
		{
			if ( $content == $searchString )
				return $pos;
		
			$pos++;
		}
	}
} // END OF MathUtil

?>
