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
 * Static helper functions.
 *
 * @package util
 */
 
class BinUtil
{
	/**
	 * @access public
	 * @static
	 */
	function binaryAdd( $num1, $num2 ) 
	{	 
		$carry = $num1 & $num2; 

		do 
		{ 
    		$carry = $carry << 1; 
    		$num1  = $num1 ^ $num2; 
    		$sum   = $num1 ^ $carry; 
    		$num2  = $carry; 
    		$carry = $num1 & $num2; 
  		} while ( $carry != 0 ); 

		return $sum; 
	} 

	/**
	 * @access public
	 * @static
	 */
	function binarySubtract( $num1,$num2 ) 
	{ 
		// compute Two's Compliment 
		$num2 = ~ $num2; 
		$num2 = BinUtil::binaryAdd( $num2, 1 ); 
 		$diff = BinUtil::binaryAdd( $num1, $num2 ); 

		return $diff;
	}
} // END OF BinUtil

?>
