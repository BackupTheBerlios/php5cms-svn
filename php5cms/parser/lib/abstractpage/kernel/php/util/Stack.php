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
 
class Stack extends PEAR
{
	/**
	 * @access public
	 */	
	var $objects = null;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stack()
	{
		$this->objects = array();
	}
	
	
	/**
	 * @access public
	 */	
	function push( &$object )
	{
		array_push( $this->objects, $object );
	}
   
	/**
	 * @access public
	 */	
	function elementAt( $index )
	{
		if ( $index >= 0 && $index < count( $this->objects ) )
			return $this->objects[$index];
     
		return null;
	}

	/**
	 * @access public
	 */	   
	function &topElement()
	{
		return $this->objects[count( $this->objects )];
	}
   
   	/**
	 * @access public
	 */	
	function &pop()
	{
		return array_pop( $this->objects );
	}

	/**
	 * @access public
	 */	   
	function size()
	{
		count( $this->objects );
	}
	
	/**
	 * @access public
	 */	
	function isEmpty()
	{
		return count( $this->objects ) > 0;
	}
   
   	/**
	 * @access public
	 */	
	function &remove() 
	{
		return $this->pop();
	}
} // END OF Stack

?>
