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


using( 'util.ListNode' );


/**
 * @package util
 */
 
class SListNode extends ListNode 
{
	var $nextNode;


	/**
	 * Constructs the node with an element.
	 *
	 * @param  $element mixed. Any object or type
	 * @access public
	 */
	function SListNode( $element = 0 ) 
	{
		$this->ListNode( $element );
		
		$this->nextNode = 0;
	}


	/**
	 * Sets the next node.
	 *
	 * @param  &$nextNode DListNode. A reference to the following node
	 * @return void
	 * @access public
	 */
	function setNext( &$nextNode ) 
	{
		$this->nextNode = &$nextNode;
	}

	/**
	 * Returns a reference to the next node.
	 *
	 * @return DListNode
	 * @access public
	 */
	function &getNext() 
	{
		return $this->nextNode;
	}
} // END OF SListNode

?>
