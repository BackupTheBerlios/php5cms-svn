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
|         Thomas Stauffer <thomas.stauffer@deepsource.ch>              |
+----------------------------------------------------------------------+
*/


using( 'security.crypt.lib.Crypting' );


for ( $i = 0; $i < 10; $i++ )
	$GLOBALS['BCMATH_CONVERSION'][strval( $i )] = strval( $i ); 		// 0-9

for ( $i = 0; $i < 26; $i++ )
	$GLOBALS['BCMATH_CONVERSION'][strval( $i + 10 )] = chr( $i + 97 ); 	// a-z

for ( $i = 0; $i < 128; $i++ )
	$GLOBALS['BCMATH_CONVERSION'][strval( $i + 36 )] = chr( $i + 128 );	// #128 - #255
	

/**
 * @package util_math
 */
 
class BCMath extends PEAR
{
	/**
	 * @access public
	 * @static
	 */	
	function dec2x( $qNumber, $qBase )
	{
		$sString = '';

		while ( $qNumber != '0' )
		{
			$sString = $GLOBALS['BCMATH_CONVERSION'][bcmod( $qNumber, $qBase )] . $sString;
			$qNumber = bcdiv( $qNumber, $qBase );
		}

		return $sString;
	}

	/**
	 * @access public
	 * @static
	 */	
	function x2dec( $sString, $qBase )
	{
		$qNumber = '0';
		$istrlen = strlen( $sString );
	
		for ( $i = 0; $i < $istrlen; $i++ )
		{
			$iPos = array_search( $sString[$istrlen - $i - 1], $GLOBALS['BCMATH_CONVERSION'] );
			$qNumber = bcadd( $qNumber, bcmul( $iPos, bcpow( $qBase, $i ) ) );
		}

		return $qNumber;
	}

	/**
	 * @access public
	 * @static
	 */	
	function bin( $qOperand )
	{
		$qResult = '';
	
		while ( $qOperand != '0' )
		{
			$qResult  = bcmod( $qOperand, '2' ) . $qResult;
			$qOperand = bcdiv( $qOperand, '2' );
		}
	
		return $qResult;
	}

	/**
	 * 7 ^ 23 = 27368747340080916343
 	 * 27368747340080916343 mod 311 = 234
	 *
	 * @access public
	 * @static
	 */	
	function powmod( $b, $e, $m )
	{
		$b       = bcmod( $b, $m );
		$bin_e   = strrev( BCMath::bin( $e ) );
		$qResult = '1';
		$ae[0]   = $b;
	
		for ( $i = 1; $i < strlen( $bin_e ); $i++ )
			$ae[$i] = bcmod( bcpow( $ae[$i - 1], 2 ), $m );

		for ( $i = 0; $i < strlen( $bin_e ); $i++ )
		{
			if ( $bin_e[$i] == '1' )
		  		$qResult = bcmod( bcmul( $qResult, $ae[$i] ), $m );
		}

		return $qResult;
	}

	/**
	 * @access public
	 * @static
	 */	
	function inv( $a, $n )
	{
		$x = '';

		$g[0] = $n;
		$g[1] = $a;
		$v[0] = '0';
		$v[1] = '1';

		$i = 1;
		while ( $g[$i] != '0' )
		{
			$g[$i + 1] = bcmod( $g[$i - 1], $g[$i] );
			$y         = bcdiv( $g[$i - 1], $g[$i] );
			$t         = bcmul( $y, $v[$i] );
			$v[$i + 1] = $v[$i - 1];
			$v[$i + 1] = bcsub( $v[$i + 1], $t );
	
			$i++;
		}

		$x = $v[$i - 1];
	
		if ( bccomp( $x, '0' ) < 0 )
			$x = bcadd( $x, $n );

		return $x;
	}

	/**
	 * @access public
	 * @static
	 */	
	function gcd( $qOperand1, $qOperand2 )
	{
		do
		{
			$qModulo   = bcmod( $qOperand1, $qOperand2 );
			$qOperand1 = $qOperand2;
			$qOperand2 = $qModulo;
		} while ( $qModulo != '0' );
	
		return $qOperand1;
	}

	/**
	 * @access public
	 * @static
	 */	
	function not( $qOperand, $iBits )
	{
		$qOperand = BCMath::dec2x( $qOperand, 2 );
		$qOperand = str_pad( $qOperand, $iBits, '0', STR_PAD_LEFT );

		$qResult = '';
	
		for ( $i = 0; $i < $iBits; $i++ )
			$qResult .= ( $qOperand[$i] == '1' )? '0' : '1';
		
		return BCMath::x2dec( $qResult, 2 );
	}
	
	/**
	 * @access public
	 * @static
	 */	
	function shl( $qOperand, $qBits )
	{
		// 32 << 2 = 128
		return bcmul( $qOperand, bcpow( 2, $qBits ) );
	}

	/**
	 * @access public
	 * @static
	 */	
	function shr( $qOperand, $qBits )
	{
		// 32 >> 2 = 8
		return bcdiv( $qOperand, bcpow( 2, $qBits ) );
	}

	/**
	 * @access public
	 * @static
	 */	
	function and2( $qOperand1, $qOperand2 )
	{
		// 980299 And 289273 = 287049
		$qOperand1 = Crypting::intToOctetString( $qOperand1 );
		$qOperand2 = Crypting::intToOctetString( $qOperand2 );
		Crypting::stretch( $qOperand1, $qOperand2, chr( 0 ) );

		$qResult = '';
	
		for ( $i = 0; $i < strlen( $qOperand1 ); $i++ )
		{
			$iValue1  = ord( $qOperand1[$i] );
			$iValue2  = ord( $qOperand2[$i] );
			$qResult .= chr( $iValue1 & $iValue2 );
		}
	
		return Crypting::octetStringToInt( $qResult );
	}

	/**
	 * @access public
	 * @static
	 */	
	function bcand()
	{
		$qResult = func_get_arg(0);
	
		for ( $i = 1; $i < func_num_args(); $i++ )
		{
			$qOperand = func_get_arg( $i );
			$qResult  = BCMath::and2( $qResult, $qOperand );
		}
	
		return $qResult;
	}

		/**
		 * @access public
		 * @static
		 */	
	function or2( $qOperand1, $qOperand2 )
	{
		// 980299 Or 289273 = 982523
		$qOperand1 = Crypting::intToOctetString( $qOperand1 );
		$qOperand2 = Crypting::intToOctetString( $qOperand2 );
		Crypting::stretch( $qOperand1, $qOperand2, chr( 0 ) );

		$qResult = '';

		for ( $i = 0; $i < strlen( $qOperand1 ); $i++ )
		{
			$iValue1  = ord( $qOperand1[$i] );
			$iValue2  = ord( $qOperand2[$i] );
			$qResult .= chr( $iValue1 | $iValue2 );
		}
	
		return Crypting::octetStringToInt( $qResult );
	}

	/**
	 * @access public
	 * @static
	 */	
	function bcor()
	{
		$qResult = func_get_arg(0);
	
		for ( $i = 1; $i < func_num_args(); $i++ )
		{
			$qOperand = func_get_arg($i);
			$qResult  = BCMath::or2( $qResult, $qOperand );
		}
	
		return $qResult;
	}

	/**
	 * @access public
	 * @static
	 */	
	function xor2( $qOperand1, $qOperand2 )
	{
		// 980299 Xor 289273 = 695474
		$qOperand1 = Crypting::intToOctetString( $qOperand1 );
		$qOperand2 = Crypting::intToOctetString( $qOperand2 );
		Crypting::stretch( $qOperand1, $qOperand2, chr( 0 ) );

		$qResult = '';
	
		for ( $i = 0; $i < strlen( $qOperand1 ); $i++ )
		{
			$iValue1  = ord( $qOperand1[$i] );
			$iValue2  = ord( $qOperand2[$i] );
			$qResult .= chr( $iValue1 ^ $iValue2 );
		}
	
		return Crypting::octetStringToInt( $qResult );
	}

	/**
	 * @access public
	 * @static
	 */	
	function bcxor()
	{
		$qResult = func_get_arg( 0 );
	
		for ( $i = 1; $i < func_num_args(); $i++ )
		{
			$qOperand = func_get_arg( $i );
			$qResult  = BCMath::xor2( $qResult, $qOperand );
		}
	
		return $qResult;
	}
} // END OF BCMath

?>
