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


using( 'util.math.BCMath' );


/**
 * Implementation of the Message Digest 4 Algorithm.
 *
 * @package security_checksum
 */

class MD4 extends PEAR
{
	/**
	 * @access private
	 */
	var $A;
	
	/**
	 * @access private
	 */
	var $B;
	
	/**
	 * @access private
	 */
	var $C;
	
	/**
	 * @access private
	 */
	var $D;
	
	
	/**
	 * Get md4 hash of string.
	 *
	 * @param  string $str
	 * @access public
	 */
	function hash( $str )
	{
		$in = array();
		
		for ( $i = 0; $i < strlen( $str ); $i++ )
			$in[$i] = ord( $str[$i] );

		$n   = count( $in );
		$b   = $n * 8;
		$M   = array();
		$out = array();

		$this->A = 1732584193;
		$this->B = 4023233417;
		$this->C = 2562383102;
		$this->D = 271733878;

		$p = 0;
		while ( $n > 64 )
		{
			$this->_copy64( $M, $in, $p );
			$this->chunk( $M );
			
			$p += 64;
			$n -= 64;
		}

		for ( $i = 0; $i < 128; $i++ )
			$buf[$i] = 0;

		for ( $i = 0; $i < $n; $i++ )
			$buf[$i] = $in[$i];

		$buf[$n] = 128;

		if ( $n <= 55 )
		{
			$this->_copy4( $buf, $b, 56 );
			$this->_copy64( $M, $buf );
			$this->chunk( $M );
		}
		else
		{
			$this->_copy4( $buf, $b, 120 );
			$this->_copy64( $M, $buf );
			$this->chunk( $M );
			$this->_copy64( $M, $buf, 64 );
			$this->chunk( $M );
		}

		$this->_copy4( $out, $this->A     );
		$this->_copy4( $out, $this->B,  4 );
		$this->_copy4( $out, $this->C,  8 );
		$this->_copy4( $out, $this->D, 12 );

		$sout = '';
		
		for ( $i = 0; $i < 16; $i++ )
			$sout .= chr( $out[$i] );
			
		return $sout;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _f( $X, $Y, $Z )
	{
		return BCMath::bcor( BCMath::bcand( $X, $Y ), BCMath::bcand( BCMath::not( $X, 32 ), $Z ) );
	}

	/**
	 * @access private
	 */
	function _g( $X, $Y, $Z )
	{
		return BCMath::bcor( BCMath::bcor( BCMath::bcand( $X, $Y ), BCMath::bcand( $X, $Z ) ), BCMath::bcand( $Y, $Z ) );
	}

	/**
	 * @access private
	 */
	function _h( $X, $Y, $Z )
	{
		return BCMath::bcxor( BCMath::bcxor( $X, $Y ), $Z );
	}

	/**
	 * @access private
	 */
	function _lShift( $X, $S )
	{
		$X = BCMath::bcand( $X, '4294967295' );
		return BCMath::bcor( BCMath::bcand( BCMath::shl( $X, $S ), '4294967295' ), BCMath::shr( $X, bcsub( 32, $S ) ) );
	}

	/**
	 * @access private
	 */
	function _round1( &$a, $b, $c, $d, $k, $s, $X )
	{
		$a = $this->_lShift( bcadd( bcadd( $a, $this->_f( $b, $c, $d ) ), $X[$k] ), $s );
	}

	/**
	 * @access private
	 */
	function _round2( &$a, $b, $c, $d, $k, $s, $X )
	{
		$a = $this->_lShift( bcadd( bcadd( bcadd( $a, $this->_g( $b, $c, $d ) ), $X[$k] ), '1518500249' ), $s );
	}

	/**
	 * @access private
	 */
	function _round3( &$a, $b, $c, $d, $k, $s, $X )
	{
		$a = $this->_lShift( bcadd( bcadd( bcadd( $a, $this->_h( $b, $c, $d ) ), $X[$k] ), '1859775393' ), $s );
	}

	/**
	 * @access private
	 */
	function _copy64( &$M, $in, $p = 0 )
	{
		for ( $i = 0; $i < 16; $i++ )
			$M[$i] = BCMath::bcor( BCMath::bcor( BCMath::bcor( BCMath::shl( $in[$i * 4 + 3 + $p], 24 ), BCMath::shl( $in[$i * 4 + 2 + $p], 16 ) ), BCMath::shl( $in[$i * 4 + 1 + $p], 8 ) ), BCMath::shl( $in[$i * 4 + 0 + $p], 0 ) );
	}

	/**
	 * @access private
	 */
	function _copy4( &$out, $x, $p = 0 )
	{
		$out[0 + $p] = BCMath::bcand( $x, 255 );
		$out[1 + $p] = BCMath::bcand( BCMath::shr( $x,  8 ), 255 );
		$out[2 + $p] = BCMath::bcand( BCMath::shr( $x, 16 ), 255 );
		$out[3 + $p] = BCMath::bcand( BCMath::shr( $x, 24 ), 255 );
	}

	/**
	 * @access private
	 */
	function chunk( &$M )
	{
		for ( $i = 0; $i < 16; $i++ )
			$X[$i] = $M[$i];

		$this->AA = $this->A;
		$this->BB = $this->B;
		$this->CC = $this->C;
		$this->DD = $this->D;

		$this->_round1( $this->A, $this->B, $this->C, $this->D,   0,   3, $X );
		$this->_round1( $this->D, $this->A, $this->B, $this->C,   1,   7, $X );
		$this->_round1( $this->C, $this->D, $this->A, $this->B,   2,  11, $X );
		$this->_round1( $this->B, $this->C, $this->D, $this->A,   3,  19, $X );
		$this->_round1( $this->A, $this->B, $this->C, $this->D,   4,   3, $X );
		$this->_round1( $this->D, $this->A, $this->B, $this->C,   5,   7, $X );
		$this->_round1( $this->C, $this->D, $this->A, $this->B,   6,  11, $X );
		$this->_round1( $this->B, $this->C, $this->D, $this->A,   7,  19, $X );
		$this->_round1( $this->A, $this->B, $this->C, $this->D,   8,   3, $X );
		$this->_round1( $this->D, $this->A, $this->B, $this->C,   9,   7, $X );
		$this->_round1( $this->C, $this->D, $this->A, $this->B,  10,  11, $X );
		$this->_round1( $this->B, $this->C, $this->D, $this->A,  11,  19, $X );
		$this->_round1( $this->A, $this->B, $this->C, $this->D,  12,   3, $X );
		$this->_round1( $this->D, $this->A, $this->B, $this->C,  13,   7, $X );
		$this->_round1( $this->C, $this->D, $this->A, $this->B,  14,  11, $X );
		$this->_round1( $this->B, $this->C, $this->D, $this->A,  15,  19, $X );

		$this->_round2( $this->A, $this->B, $this->C, $this->D,   0,   3, $X );
		$this->_round2( $this->D, $this->A, $this->B, $this->C,   4,   5, $X );
		$this->_round2( $this->C, $this->D, $this->A, $this->B,   8,   9, $X );
		$this->_round2( $this->B, $this->C, $this->D, $this->A,  12,  13, $X );
		$this->_round2( $this->A, $this->B, $this->C, $this->D,   1,   3, $X );
		$this->_round2( $this->D, $this->A, $this->B, $this->C,   5,   5, $X );
		$this->_round2( $this->C, $this->D, $this->A, $this->B,   9,   9, $X );
		$this->_round2( $this->B, $this->C, $this->D, $this->A,  13,  13, $X );
		$this->_round2( $this->A, $this->B, $this->C, $this->D,   2,   3, $X );
		$this->_round2( $this->D, $this->A, $this->B, $this->C,   6,   5, $X );
		$this->_round2( $this->C, $this->D, $this->A, $this->B,  10,   9, $X );
		$this->_round2( $this->B, $this->C, $this->D, $this->A,  14,  13, $X );
		$this->_round2( $this->A, $this->B, $this->C, $this->D,   3,   3, $X );
		$this->_round2( $this->D, $this->A, $this->B, $this->C,   7,   5, $X );
		$this->_round2( $this->C, $this->D, $this->A, $this->B,  11,   9, $X );
		$this->_round2( $this->B, $this->C, $this->D, $this->A,  15,  13, $X );

		$this->_round3( $this->A, $this->B, $this->C, $this->D,   0,   3, $X );
		$this->_round3( $this->D, $this->A, $this->B, $this->C,   8,   9, $X );
		$this->_round3( $this->C, $this->D, $this->A, $this->B,   4,  11, $X );
		$this->_round3( $this->B, $this->C, $this->D, $this->A,  12,  15, $X );
		$this->_round3( $this->A, $this->B, $this->C, $this->D,   2,   3, $X );
		$this->_round3( $this->D, $this->A, $this->B, $this->C,  10,   9, $X );
		$this->_round3( $this->C, $this->D, $this->A, $this->B,   6,  11, $X );
		$this->_round3( $this->B, $this->C, $this->D, $this->A,  14,  15, $X );
		$this->_round3( $this->A, $this->B, $this->C, $this->D,   1,   3, $X );
		$this->_round3( $this->D, $this->A, $this->B, $this->C,   9,   9, $X );
		$this->_round3( $this->C, $this->D, $this->A, $this->B,   5,  11, $X );
		$this->_round3( $this->B, $this->C, $this->D, $this->A,  13,  15, $X );
		$this->_round3( $this->A, $this->B, $this->C, $this->D,   3,   3, $X );
		$this->_round3( $this->D, $this->A, $this->B, $this->C,  11,   9, $X );
		$this->_round3( $this->C, $this->D, $this->A, $this->B,   7,  11, $X );
		$this->_round3( $this->B, $this->C, $this->D, $this->A,  15,  15, $X );

		$this->A += $this->AA;
		$this->B += $this->BB;
		$this->C += $this->CC;
		$this->D += $this->DD;

		$this->A = BCMath::bcand( $this->A, '4294967295' ); // 0xFFFFFFFF
		$this->B = BCMath::bcand( $this->B, '4294967295' ); // 0xFFFFFFFF
		$this->C = BCMath::bcand( $this->C, '4294967295' ); // 0xFFFFFFFF
		$this->D = BCMath::bcand( $this->D, '4294967295' ); // 0xFFFFFFFF
	}
} // END OF MD4

?>
