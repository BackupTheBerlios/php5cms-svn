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
 
class VectorIterator extends Iterator 
{
	/**
	 * @access public
	 */
	var $list;
	
	/**
	 * @access public
	 */
	var $index;
	
	/**
	 * @access public
	 */
	var $size;

	
	/**
	 * Constructs with the given Vector reference.
	 *
	 * @param  &$list Vector. A Vector reference
	 * @access public
	 */
	function VectorIterator( &$list ) 
	{
		$this->list	= &$list;
		$this->size	= count( $this->list );
	}
	

	/**
	 * Sets the index.
	 *
	 * @access public
	 */
	function setIndex( $index ) 
	{
		$this->index = $index;
	}

	/**
	 * Returns the element currently pointed to by this iterator.
	 *
	 * @return mixed
	 * @access public
	 */
	function getCurrent() 
	{
		if ( isset( $this->list[ $this->index ] ) )
			return $this->list[ $this->index ];
		else
			return false;
	}

	/**
	 * Returns a reference to the element currently pointed to by this iterator.
	 *
	 * @return mixed
	 * @access public
	 */
	function &getCurrentRef() 
	{
		if ( isset( $this->list[ $this->index ] ) )
			return $this->list[ $this->index ];
		else
			return false;
	}

	/**
	 * Sets the value of the element currently pointed to by this iterator.
	 *
	 * @param  $value mixed. The new value pointed to by this iterator
	 * @return void
	 * @access public
	 */
	function setCurrent( $value ) 
	{
		if ( isset( $this->list[ $this->index ] ) )
			$this->list[ $this->index ] = $value;
	}
} // END OF VectorIterator

?>
