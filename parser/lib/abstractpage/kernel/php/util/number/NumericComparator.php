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
 * Comparator for numbers.
 *
 * @package util_number
 */
 
class NumericComparator extends Comparator 
{
	/**
	 * Test for inequality.
	 *
	 * @access  public
	 */
	function less( $lhs, $rhs ) 
	{
		if ( $lhs < $rhs )
			return true;
		else
			return false;
	}

	/**
	 * Test for equality.
	 *
	 * @access  public
	 */
	function equal( $lhs, $rhs ) 
	{
		if ($lhs == $rhs)
			return true;
		else
			return false;
	}
} // END OF NumericComparator

?>
