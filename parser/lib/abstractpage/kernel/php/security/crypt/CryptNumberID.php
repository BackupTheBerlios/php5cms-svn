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
 * Creates a handy ID of a Number.
 *
 * This is not a real encryption class, but it can be useful.
 *
 * Especialy for eshops it's handy that the user gets another order nr than
 * the normal used order ID. A regular customer can see if the store is successfull
 * just by looking at the orderid's.
 * This class created an order number that can be used as a referal for the customer.
 * Intern you can still use the order ID.
 * The numbers are unique as long ID is below 1000000.
 *
 * @package security_crypt
 */
 
class CryptNumberID extends PEAR
{
	/**
	 * Translate an ID into a number.
	 *
	 * @param long $ID	- Identifier
	 * @return string containing the number
	 * @access public	
	 */
	function toNumber( $ID )
	{
		mt_srand( time() );
		
		$str  = sprintf( "%02d", date( "m" ) * 9 );
		$str .= sprintf( "%02d", date( "s" ) );
		$str .= right( sprintf( "%04d", date( "z" ) * mt_rand( 0, 16 ) ), 4 );
		$str .= right( sprintf( "%06d", $ID), 6 );
	
		$newstr  = $str[12] . $str[6] . $str[8] . $str[1] . $str[4]  . $str[11] . $str[2];
		$newstr .= $str[7]  . $str[9] . $str[3] . $str[5] . $str[10] . $str[0]  . $str[13];
	
		return $newstr;
	}
	
	/**
	 * Translate an number into an ID.
	 *
	 * @param string nr - number create by toNumber function
	 * @return long ID - your original ID
	 * @access public
	 */
	function toId( $nr )
	{
		return $nr[2] . $nr[8] . $nr[11] . $nr[5] . $nr[0] . $nr[13];
	}	
} // END OF CryptNumberID                            

?>
