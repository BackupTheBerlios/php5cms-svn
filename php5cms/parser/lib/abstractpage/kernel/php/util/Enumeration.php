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
 
class Enumeration extends PEAR
{
	/**
	 * @access public
	 */
	var $index = 0;
	
	/**
	 * @access public
	 */
	var $value = null;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Enumeration()
	{
		$this->value = array();
	}
	

	/**
	 * @access public
	 */	
	function nextElement()
	{
		$index = $this->index;
		$this->index++;
		
		return ( isset( $this->value[$index] )? $this->value[$index] : null );
	}
	
	/**
	 * @access public
	 */
	function hasMoreElements()
	{
		return $this->index < count( $this->value );
	}

	/**
	 * @access public
	 */   
	function rewind()
	{
		$this->index = 0;
	}
} // END OF Enumeration

?>
