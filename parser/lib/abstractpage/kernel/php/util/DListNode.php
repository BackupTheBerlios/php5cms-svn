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


using( 'util.SListNode' );


/**
 * @package util
 */
 
class DListNode extends SListNode 
{
	/**
	 * @access public
	 */
	var $prevNode;


	/**
	 * Constructs the node with an element.
	 *
	 * @param  $element mixed. Any object or type
	 * @access public
	 */
	function DListNode( $element = 0 ) 
	{
		$this->SListNode( $element );
		$this->prevNode = 0;
	}


	/**
	 * Sets the previous node.
	 *
	 * @param  &$prevNode DListNode. A reference to the preceding node
	 * @return void
	 * @access public
	 */
	function setPrev( &$prevNode ) 
	{
		$this->prevNode = &$prevNode;
	}

	/**
	 * Returns a reference to the previous node.
	 *
	 * @return DListNode
	 * @access public
	 */
	function &getPrev() 
	{
		return $this->prevNode;
	}
} // END OF DListNode

?>
