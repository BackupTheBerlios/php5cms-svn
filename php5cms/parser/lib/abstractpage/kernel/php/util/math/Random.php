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


using( 'util.math.MersenneTwister' );
using( 'util.math.BCMath' );


/**
 * @package util_math
 */
 
class Random extends PEAR
{
	/**
	 * @access public
	 */
	var $mTwister;

	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	function Random()
	{
		$this->mTwister = new MersenneTwister();
	}

	
	/**
	 * @access public
	 */
	function seed( $qNumber )
	{
		while ( $qNumber != '0' )
		{
			$aInit[] = bcmod( $qNumber, '4294967296' );
			$qNumber = bcdiv( $qNumber, '4294967296' );
		}
		
		$this->mTwister->initByArray( $aInit );
	}

	/**
	 * @access public
	 */
	function rndm( $iBits = 128 )
	{
		$iDoubleWords = ceil( $iBits / 32 );
		$qNumber = '0';
		
		for ( $i = 0; $i < $iDoubleWords; $i++ )
			$qNumber = bcadd( $qNumber, BCMath::shl( $this->mTwister->genRandInt32(), $i * 32 ) );

		$qNumber = bcmod( $qNumber, BCMath::shl( 1, $iBits ) );
		return $qNumber;
	}
} // END OF Random

?>
