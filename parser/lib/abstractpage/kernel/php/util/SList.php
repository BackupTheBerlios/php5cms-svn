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
using( 'util.SListIterator' );


/**
 * A Singly-Linked List. Each element is stored in a node with a reference to
 * a following node. An empty SList consists of a single HEAD
 * and single TAIL element. Any added elements will exist between these two.
 * These elements act as virtual start and end markers for iterations across
 * the entire list and are not meant to be referenced directly.
 * 
 * Usage:
 * $sl = new SList( 1, 2, 3, 4, 5, 6 );
 * $sli = $sl->begin();
 * while ( !$sli->isDone() ) 
 * {
 *		echo $sli->getCurrent() . "<br>";
 *		$sli->next();
 * }
 *
 * @package util
 */

class SList extends PEAR
{
	/**
	 * @access public
	 */
	var $head;
	
	/**
	 * @access public
	 */
	var $last;
	
	/**
	 * @access public
	 */
	var $tail;

	
	/**
	 * Constructor
	 *
	 * Accepts a variable number of arguments as initial elements in the list.
	 *
	 * @access public
	 */
	function SList() 
	{
		$this->head = new SListNode( HEAD );
		$this->tail = new SListNode( TAIL );
		$this->head->setNext( $this->tail );
		$this->last = &$this->head;

		$numArgs = func_num_args();
		for ( $index = 0; $index < $numArgs; $index++ )
			$this->add( func_get_arg( $index ) );
	}

	
	/**
	 * Returns a reference to  the first node in the list (the node immediately
	 * following the HEAD node).
	 *
	 * @return DListNode
	 * @access public
	 */
	function &getFirstNode() 
	{
		return $this->head->getNext();
	}

	/**
	 * Returns an iterator to the start of the list.
	 *
	 * @return DListForwardIterator
	 * @access public
	 */
	function begin() 
	{
		return new SListIterator( $this );
	}

	/**
	 * Adds an element to the end of the list.
	 *
	 * @param  mixed. Any object or type
	 * @return void
	 * @access public
	 */
	function add( $element ) 
	{
		$newNode = new SListNode( $element );
		$this->last->setNext( $newNode );
		$newNode->setNext( $this->tail );
		$this->last = &$newNode;
	}
} // END OF SList

?>
