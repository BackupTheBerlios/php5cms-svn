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


using( 'util.math.BCMath' );


/**
 * 32 Bit Random Number Generator.
 *
 * @package util_math
 */

class MersenneTwister extends PEAR
{
	/**
	 * @access public
	 */
	var $N;
	
	/**
	 * @access public
	 */
	var $M;
	
	/**
	 * @access public
	 */
	var $MATRIX_A;
	
	/**
	 * @access public
	 */
	var $UPPER_MASK;
	
	/**
	 * @access public
	 */
	var $LOWER_MASK;
	
	/**
	 * @access public
	 */
	var $mt;
	
	/**
	 * @access public
	 */
	var $mti;
	
	/**
	 * @access public
	 */
	var $mag01;

	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	function MersenneTwister()
	{
		$this->N = 624;
		$this->M = 397;
		
		$this->MATRIX_A   = '2567483615'; //0x9908b0df
		$this->UPPER_MASK = '2147483648'; //0x80000000
		$this->LOWER_MASK = '2147483647'; //0x7fffffff

		$this->mt  = array();
		$this->mti = $this->N + 1;

		$this->mag01 = array( '0', $this->MATRIX_A );
	}

	
	/**
	 * @access public
	 */
	function initGenRand( $s )
	{
		$this->mt[0] = BCMath::bcand( $s, '4294967295' ); // 0xffffffff
		
		for ( $this->mti = 1; $this->mti < $this->N; $this->mti++ )
		{
			$d1 = BCMath::shr( $this->mt[$this->mti - 1], '30' );
			$d2 = BCMath::bcxor( $this->mt[$this->mti - 1], $d1  );
			$d3 = bcmul( '1812433253', $d2 );
			$d4 = bcadd( $this->mti, $d3 );
			$d5 = BCMath::bcand( $d4, '4294967295' ); // 0xffffffff

			$this->mt[$this->mti] = $d5;
		}
	}

	/**
	 * @access public
	 */
	function initByArray( $init_key )
	{
		$key_length = count( $init_key );

		$this->initGenRand( '19650218' );

		$i = 1;
		$j = 0;
		$k = $this->N > $key_length? $this->N : $key_length;

		for (; $k; $k-- )
		{
			$d1 = BCMath::shr( $this->mt[$i - 1], '30' );
			$d2 = BCMath::bcxor( $this->mt[$i - 1], $d1  );
			$d3 = bcmul( '1664525', $d2 );
			$d4 = BCMath::bcxor( $this->mt[$i], $d3 );
			$d5 = bcadd( $init_key[$j], $d4 );
			$d6 = bcadd( $j, $d5 );
			$d7 = BCMath::bcand( $d6, '4294967295' ); // 0xffffffff

			$this->mt[$i] = $d7;

			$i++;
			$j++;

			if ( $i >= $this->N )
			{
				$this->mt[0] = $this->mt[$this->N - 1];
				$i = 1;
			}

			if ( $j >= $key_length )
				$j = 0;
		}

		for ( $k = ( $this->N - 1 ); $k; $k-- )
		{
			$d1 = BCMath::shr( $this->mt[$i - 1], '30' );
			$d2 = BCMath::bcxor( $this->mt[$i - 1], $d1  );
			$d3 = bcmul( '1566083941',  $d2 );
			$d4 = BCMath::bcxor( $this->mt[$i], $d3 );
			$d5 = bcsub( $d4, $i );
			$d6 = BCMath::bcand( $d5, '4294967295' ); // 0xffffffff

			$this->mt[$i] = $d6;

			$i++;

			if ( $i >= $this->N )
			{
				$this->mt[0] = $this->mt[$this->N - 1];
				$i = 1;
			}
		}

		$this->mt[0] = '2147483648'; // 0x80000000

	}

	/**
	 * @access public
	 */
	function genRandInt32()
	{
		if ( $this->mti >= $this->N )
		{
			if ( $this->mti == $this->N + 1 )
				$this->initGenRand( 5489 );
			
			for ( $kk = 0; $kk < ( $this->N - $this->M ); $kk++ )
			{
				$d1 = BCMath::bcand( $this->mt[$kk],     $this->UPPER_MASK );
				$d2 = BCMath::bcand( $this->mt[$kk + 1], $this->LOWER_MASK );

				$y = BCMath::bcor( $d1, $d2 );

				$d1    = BCMath::shr( $y, '1' );
				$index = intval( BCMath::bcand( $y, '1' ) );
				$d2    = $this->mag01[$index];
				$d3    = BCMath::bcxor( $this->mt[$kk + $this->M], $d1 );
				$d4    = BCMath::bcxor( $d3, $d2 );

				$this->mt[$kk] = $d4;
			}

			for ( $kk = $kk; $kk < ( $this->N - 1 ); $kk++ )
			{
				$d1 = BCMath::bcand( $this->mt[$kk],     $this->UPPER_MASK );
				$d2 = BCMath::bcand( $this->mt[$kk + 1], $this->LOWER_MASK );

				$y = BCMath::bcor( $d1, $d2 );

				$d1    = BCMath::shr( $y, '1' );
				$index = intval( BCMath::bcand( $y, '1' ) );
				$d2    = $this->mag01[$index];
				$d3    = BCMath::bcxor( $this->mt[$kk + ( $this->M - $this->N )], $d1 );
				$d4    = BCMath::bcxor( $d3, $d2 );

				$this->mt[$kk] = $d4;
			}

			$d1 = BCMath::bcand( $this->mt[$this->N - 1], $this->UPPER_MASK );
			$d2 = BCMath::bcand( $this->mt[0], $this->LOWER_MASK );

			$y = BCMath::bcor( $d1, $d2 );

			$d1    = BCMath::shr( $y, '1' );
			$index = intval( BCMath::bcand( $y, '1' ) );
			$d2    = $this->mag01[$index];
			$d3    = BCMath::bcxor( $this->mt[$this->M - 1], $d1 );
			$d4    = BCMath::bcxor( $d3, $d2 );

			$this->mt[$this->N - 1] = $d4;

			$this->mti = 0;
		}

		$y = $this->mt[$this->mti++];

		$d1 = BCMath::shr( $y,  '11' );
		$y  = BCMath::bcxor( $y,  $d1  );

		$d1 = BCMath::shl( $y,  '7' );
		$d2 = BCMath::bcand( $d1, '2636928640' ); // 0x9d2c5680
		$y  = BCMath::bcxor( $y,  $d2 );

		$d1 = BCMath::shl( $y,  '15' );
		$d2 = BCMath::bcand( $d1, '4022730752' ); // 0xefc60000
		$y  = BCMath::bcxor( $y,  $d2 );

		$d1 = BCMath::shr( $y, '18' );
		$y  = BCMath::bcxor( $y, $d1  );

		return $y;  
	}
} // END OF MersenneTwister

?>
