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


define( 'HEAD', md5( 'HEAD' ) );
define( 'TAIL', md5( 'TAIL' ) );


/**
 * @package util
 */
 
class ListNode extends PEAR
{
	/**
	 * @access public
	 */
	var $element;

	
	/**
	 * Constructs the node with an element.
	 *
	 * @param  $element mixed. Any object or type
	 * @access public
	 */
	function ListNode( $element ) 
	{
		$this->element = $element;
	}

	
	/**
	 * Sets the element.
	 *
	 * @param  $element mixed. Any object or type
	 * @return void
	 * @access public
	 */
	function set( $element ) 
	{
		$this->element = $element;
	}

	/**
	 * Returns the element.
	 *
	 * @return mixed
	 * @access public
	 */
	function get() 
	{
		return $this->element;
	}

	/**
	 * Returns true if this is the HEAD element.
	 *
	 * @return boolean
	 * @access public
	 */
	function isHead() 
	{
		return $this->element == HEAD;
	}

	/**
	 * Returns true if this is the TAIL element.
	 *
	 * @return boolean
	 * @access public
	 */
	function isTail() 
	{
		return $this->element == TAIL;
	}
} // END OF ListNode

?>
