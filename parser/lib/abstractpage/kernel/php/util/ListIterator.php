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
 * A ListIterator for lists that allows the programmer to traverse the list in
 * either direction, modify the list during iteration, and obtain the
 * iterator's current position in the list. A ListIterator has no current
 * element; its cursor position always lies between the element that would be
 * returned by a call to previous() and the element that would be returned by a
 * call to next(). In a list of length n, there are n+1 valid index values,
 * from 0 to n, inclusive.
 *
 *           Element(0)   Element(1)   Element(2) ... Element(n)
 *         ^            ^            ^             ^             ^
 * Index:  0            1            2             3            n+1
 *
 * Note that the remove() and set() methods are not defined in terms of the
 * cursor position; they are defined to operate on the last element returned by
 * a call to next() or previous().
 *
 * @package util
 */

class ListIterator extends PEAR
{
	/**
	 * @var	array
	 * @access private
	 */
	var $_values;

	/**
	 * @var	integer
	 * @access private
	 */
	var $_index;

	/**
	 * @var	integer
	 * @access private
	 */
	var $_element;


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	$values
	 */
	function ListIterator( &$values )
	{
		if ( is_array( $values ) ) 
			$this->_values = &$values;
		else
			$this->_values = array();
		
		$this->_element = $this->_index = 0;
	}
	
	
	/**
	 * Check if the ListIterator has more elements when traversing the list in
	 * the forward direction.
	 *
	 * @access	public
	 * @return	boolean
	 */
	function hasNext()
	{
		return ( $this->_index < count( $this->_values ) );
	}
	
	/**
	 * Check if the ListIterator has more elements when traversing the list in
	 * the reverse direction.
	 *
	 * @access	public
	 * @return	boolean
	 */
	function hasPrevious()
	{
		return ( $this->_index > 0 );
	}
	
	/**
	 * Get the next element in the list.
	 *
	 * @access	public
	 * @return	mixed
	 */
	function next()
	{
		if ( $this->hasNext() ) 
		{
			$value = $this->_values[$this->_index++];
			$this->_element = $this->_index - 1;
		}
		
		return $value;
	}
	
	/**
	 * Get the index of the element that would be returned by a subsequent call
	 * to next().
	 *
	 * @access	public
	 * @return	mixed
	 */
	function nextIndex()
	{
		return $this->_index;
	}
	
	/**
	 * Get the previous element in the list.
	 *
	 * @access	public
	 * @return	mixed
	 */
	function previous()
	{
		if ( $this->hasPrevious() ) 
		{
			$value = $this->_values[--$this->_index];
			$this->_element = $this->_index;
		}
		
		return $value;
	}
	
	/**
	 * Get the index of the element that would be returned by a subsequent call
	 * to prev().
	 *
	 * @access	public
	 * @return	mixed
	 */
	function previousIndex()
	{
		return ( $this->_index - 1 );
	}
	
	/**
	 * Remove from the list the last element that was returned by next() or
	 * previous().
	 *
	 * @access	public
	 */
	function remove()
	{
		array_splice( $this->_values, $this->_element, 1 );
	}
	
	/**
	 * Replace the last element returned by next() or previous() with the
	 * specified element.
	 *
	 * @access	public
	 * @param	mixed	$value
	 */
	function set( $value )
	{
		$this->_values[$this->_element] = $value;
	}
	
	/**
	 * Insert the specified element into the list.
	 *
	 * @access	public
	 * @param	mixed	$value
	 */
	function add( $value )
	{
		array_push( $this->_values, $value );
	}
} // END OF ListIterator

?>
