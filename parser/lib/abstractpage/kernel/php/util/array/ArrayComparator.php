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


using( 'util.Comparator' );


/**
 * Comparator for arrays.
 *
 * @package util_array
 */

class ArrayComparator extends Comparator 
{
	/**
	 * Test if one object is less than another.
	 *
	 * @access  public
	 */
	function less( $lhs, $rhs ) 
	{
		return PEAR::raiseError( "Less() is undefined for arrays." );
	}

	/**
	 * Test for equality.
	 *
	 * @access  public
	 */
	function equal( $lhs, $rhs ) 
	{
		/**
		 * 1. Compare keys (order-sensitive)
		 * 2. Compare values
		 */
		$A  = array_keys( $lhs );
		$B  = array_keys( $rhs );
		$nA = count( $A );
		$nB = count( $B );

		if ( $nA != $nB ) 
			return false;

		for ( $i = 0; $i < $nA; $i++ ) 
		{
			if ( $A[$i] !== $B[$i] ) 
				return false;
		
			if ( $lhs[$A[$i]] !== $rhs[$B[$i]] ) 
				return false;
		}
	}
} // END OF ArrayComparator

?>
