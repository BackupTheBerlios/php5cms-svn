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


using( 'util.VectorIterator' );


/**
 * @package util
 */
 
class VectorReverseIterator extends VectorIterator 
{
	/**
	 * Constructs with the given Vector reference.
	 *
	 * @param  &$list Vector. A Vector reference
	 * @access public
	 */
	function VectorReverseIterator( &$list ) 
	{
		$this->VectorIterator( &$list );
		
		$this->setIndex( $this->size - 1 );
	}

	
	/**
	 * Sets the list iterator to the next element.
	 *
	 * @return void
	 * @access public
	 */
	function next() 
	{
		if ( $this->index >= 0 )
			$this->index--;
	}

	/**
	 * Sets the list iterator to the previous element.
	 *
	 * @return void
	 * @access public
	 */
	function prev() 
	{
		if ( !$this->isDone() )
			$this->index++;
	}

	/**
	 * Returns true when the current element has arrived to a non-existant
	 * element and false otherwise.
	 *
	 * @return boolean
	 * @access public
	 */
	function isDone() 
	{
		if ( $this->index < 0 ) 
			return true;
		else 
			return false;
	}
} // END OF VectorReverseIterator

?>
