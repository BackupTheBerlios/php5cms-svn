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


using( 'util.SList' );
using( 'util.DListNode' );
using( 'util.DListForwardIterator' );
using( 'util.DListReverseIterator' );


/**
 * A Doubly-Linked List. Each element is stored in a node with a reference to
 * a preceding and following node. An empty DList consists of a single HEAD
 * and single TAIL element. Any added elements will exist between these two.
 * These elements act as virtual start and end markers for iterations across
 * the entire list and are not meant to be referenced directly.
 *
 * @package util
 */

class DList extends SList 
{
	/**
	 * Constructor
	 *
	 * Accepts a variable number of arguments as initial elements in the list.
	 *
	 * @access public
	 */
	function DList() 
	{
		$this->SList();
		
		$this->head = new DListNode( HEAD );
		$this->tail = new DListNode( TAIL );

		$this->head->setNext( $this->tail );
		$this->tail->setPrev( $this->head );

		$numArgs = func_num_args();
		for ( $index = 0; $index < $numArgs; $index++ )
			$this->add( func_get_arg( $index ) );
	}

	
	/**
	 * Returns a reference to the last node in the list (the node immediately
	 * preceding the TAIL node).
	 *
	 * @return DListNode
	 * @access public
	 */
	function &getLastNode() 
	{
		return $this->tail->getPrev();
	}

	/**
	 * Returns an iterator to the start of the list.
	 *
	 * @return DListForwardIterator
	 * @access public
	 */
	function begin() 
	{
		return new DListForwardIterator( $this );
	}

	/**
	 * Returns an iterator to the end of the list.
	 * 
	 * @return DListReverseIterator
	 * @access public
	 */
	function end() 
	{
		return new DListReverseIterator( $this );
	}

	/**
	 * Adds an element to the end of the list.
	 *
	 * @param  $element mixed. Any object or type
	 * @return void
	 * @access public
	 */
	function add( $element ) 
	{
		$newNode  = new DListNode( $element );
		$lastNode = &$this->getLastNode();
		$lastNode->setNext( $newNode );
		$newNode->setNext( $this->tail );
		$newNode->setPrev( $lastNode );
		$this->tail->setPrev( $newNode );
	}
} // END OF DList

?>
