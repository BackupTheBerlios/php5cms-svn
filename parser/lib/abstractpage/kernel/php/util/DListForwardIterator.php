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


using( 'util.DListIterator' );


/**
 * @package util
 */
 
class DListForwardIterator extends DListIterator 
{
	/**
	 * Constructor
	 * Initializes the iterator with a DList reference.
	 *
	 * @param &$dlist DList. A reference to a DList object
	 * @access public
	 */
	function DListForwardIterator( &$dlist ) 
	{
		$this->DListIterator( $dlist );
		$this->setNode( $dlist->getFirstNode() );
	}

	
	/**
	 * Increments the current node pointer, moving closer to the end of the list.
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
	 * Decrements the current node pointer, moving closer to the front of the list.
	 *
	 * @return void
	 * @access public
	 */
	function prev() 
	{
		if ( !$this->atBegin() )
			$this->node = &$this->node->getPrev();
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
} // END OF DListForwardIterator

?>
