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


using( 'util.Iterator' );


/**
 * @package util
 */
 
class SListIterator extends Iterator 
{
	/**
	 * @access public
	 */
	var $slist;
	
	/**
	 * @access public
	 */
	var $node;

	
	/**
	 * Constructs the iterator with a SList reference.
	 *
	 * @param  &$dlist SList. A reference to a SList object
	 * @access public
	 */
	function SListIterator( &$slist ) 
	{
		$this->slist = &$slist;
		$this->setNode( $slist->getFirstNode() );
	}


	/**
	 * Returns the element currently referenced.
	 *
	 * @return mixed
	 * @access public
	 */
	function getCurrent() 
	{
		return $this->node->get();
	}

	/**
	 * Sets the value of the element currently referenced.
	 *
	 * @param  $value mixed. Any object or type
	 * @return void
	 * @access public
	 */
	function setCurrent( $value ) 
	{
		$this->node->set( $value );
	}

	/**
	 * Returns true if the current node pointer points to the TAIL element,
	 * false otherwise.
	 *
	 * @return boolean
	 * @access public
	 */
	function atEnd() 
	{
		return $this->node->isTail();
	}

	/**
	 * Returns true if the current node pointer points to the TAIL element,
	 * false otherwise.
	 *
	 * @return boolean
	 * @access public
	 */
	function isDone() 
	{
		return $this->atEnd();
	}

	/**
	 * Increments the current node pointer, moving closer to the end of the
	 * list.
	 *
	 * @return void
	 * @access public
	 */
	function next() 
	{
		if ( !$this->atEnd() ) 
			$this->node = &$this->node->getNext();
	}

	/**
	 * Sets the node reference.
	 *
	 * @param &$node DListNode. Reference to a DListNode
	 * @return void
	 * @access public
	 */
	function setNode( &$node ) 
	{
		$this->node = &$node;
	}
} // END OF SListIterator

?>