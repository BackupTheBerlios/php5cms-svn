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
 * User-defined comparator.
 *
 * @package util
 */
 
class Comparator extends PEAR
{
	/**
	 * Test if one object is less than another.
	 *
	 * @accesss public
	 */
	function less( $lhs, $rhs ) 
	{
		return PEAR::raiseError( "Virtual method called." );
	}

	/**
	 * Test for equality.
	 *
	 * @accesss public
	 */
	function equal( $lhs, $rhs ) 
	{
		return PEAR::raiseError( "Virtual method called." );
	}

	/**
	 * Test if one object is greater than another.
	 *
	 * @accesss public
	 */
	function greater( $lhs, $rhs ) 
	{
		return !$this->le( $lhs, $rhs );
	}

	/**
	 * Test for equality.
	 *
	 * @accesss public
	 */
	function eq( $lhs, $rhs ) 
	{
		return $this->equal( $lhs, $rhs );
	}

	/**
	 * Test if one object is less than another.
	 *
	 * @accesss public
	 */
	function lt( $lhs, $rhs ) 
	{
		return $this->less( $lhs, $rhs );
	}

	/**
	 * Test if one object is greater than another.
	 *
	 * @accesss public
	 */
	function gt( $lhs, $rhs ) 
	{
		return $this->greater( $lhs, $rhs );
	}

	/**
	 * Test if two objects are not equal.
	 *
	 * @accesss public
	 */
	function ne( $lhs, $rhs ) 
	{
		return !$this->equal( $lhs, $rhs );
	}

	/**
	 * Test if one object is less than or equal to another.
	 *
	 * @accesss public
	 */
	function le( $lhs, $rhs ) 
	{
		return ( $this->less( $lhs, $rhs ) || $this->equal( $lhs, $rhs ) );
	}

	/**
	 * Test if one object is greater than or equal to another.
	 *
	 * @accesss public
	 */
	function ge( $lhs, $rhs ) 
	{
		return !$this->less( $lhs, $rhs );
	}
} // END OF Comparator

?>
