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
 * @package util
 */
 
class OrderedPair extends PEAR
{
	/**
	 * @access public
	 */
	var $name;
	
	/**
	 * @access public
	 */
	var $value;

	
	/**
	 * Constructs the ordered pair with a name, value and type.
	 *
	 * @param  $name string. The name of the OrderedPair
	 * @param  $value mixed. The value of the OrderedPair
	 * @param  $type int. The OrderedPair type. Any combination of NOT_NULL, AUTO_INCREMENT, PRIMARY, FOREIGN, URL, and FINAL.
	 * @access public
	 */
	function OrderedPair( $name, $value ) 
	{
		$this->set( $name, $value );
	}

	
	/**
	 * Returns the name.
	 *
	 * @return string
	 * @access public
	 */
	function getName() 
	{
		return $this->name;
	}

	/**
	 * Returns the name, slashed.
	 *
	 * @return string
	 * @access public
	 */
	function getSlashedName() 
	{
		return addslashes( $this->name );
	}

	/**
	 * Sets the name.
	 *
	 * @return void
	 * @access public
	 */
	function setName( $name ) 
	{
		$this->name = $name;
	}

	/**
	 * Returns the value.
	 *
	 * @return string
	 * @access public
	 */
	function getValue() 
	{
		return $this->value;
	}

	/**
	 * Sets the value.
	 *
	 * @return void
	 * @access public
	 */
	function setValue( $value ) 
	{
		$this->value = $value;
	}

	/**
	 * Sets the name, value, and type.
	 *
	 * @param  $name string. The name of the OrderedPair
	 * @param  $value mixed. The value of the OrderedPair
	 * @return void
	 * @access public
	 */
	function set( $name, $value ) 
	{
		$this->setName( $name );
		$this->setValue( $value );
	}
} // END OF OrderedPair

?>
