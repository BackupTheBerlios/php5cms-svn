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


using( 'util.ListIterator' );


/**
 * ArrayList of objects that can be administered and searched, while hiding the
 * internal implementation.
 *
 * @package util_array
 */
 
class ArrayList extends PEAR
{
	/**
	 * @var	array
	 * @access private
	 */
	var $_elements = array();


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	string	$elements
	 */
	function ArrayList( $elements = array() )
	{
		if ( !empty( $elements ) ) 
			$this->_elements = $elements;
	}
	
	
	/**
	 * Get a ListIterator for the ArrayList.
	 *
	 * @access	public
	 * @return	ListIterator
	 */
	function listIterator()
	{
		return new ListIterator( $this->_elements );
	}
	
	/**
	 * Appends the specified element to the end of this list.
	 *
	 * @access	public
	 * @param	mixed	$element
	 * @return	boolean
	 */
	function add( $element )
	{
		return ( array_push( $this->_elements, $element ) )? true : false;
	}
	
	/**
	 * Appends all of the elements in the specified ArrayList to the end of
	 * this list, in the order that they are returned by the specified
	 * ArrayList's ListIterator.
	 *
	 * @access	public
	 * @param	ArrayList	$list
	 * @return	boolean
	 */
	function addAll( $list )
	{
		$before = $this->size();
		
		if ( is_a( $list, get_class( $this ) ) ) 
		{
			$iterator = $list->listIterator();
			
			while ( $iterator->hasNext() )
				$this->add( $iterator->next() );
		}
		
		$after = $this->size();
		return ( $before < $after );
	}
	
	/**
	 * Removes all of the elements from this list.
	 *
	 * @access	public
	 */
	function clear()
	{
		$this->_elements = array();
	}
	
	/**
	 * Returns true if this list contains the specified element.
	 *
	 * @access	public
	 * @param	mixed	$element
	 * @return	boolean
	 */
	function contains( $element )
	{
		return ( array_search( $element, $this->_elements ) )? true : false;
	}
	
	/**
	 * Returns the element at the specified position in this list.
	 *
	 * @access	public
	 * @param	integer	$index
	 * @return	mixed
	 */
	function get( $index )
	{
		return $this->_elements[$index];
	}
	
	/**
	 * Searches for the first occurence of the given argument.
	 *
	 * @access	public
	 * @param	mixed	$element
	 * @return	mixed
	 */
	function indexOf( $element )
	{
		return array_search( $element, $this->_elements );
	}
	
	/**
	 * Tests if this list has no elements.
	 *
	 * @access	public
	 * @return	boolean
	 */
	function isEmpty()
	{
		return empty( $this->_values );
	}
	
	/**
	 * Returns the index of the last occurrence of the specified object in this
	 * list.
	 *
	 * @access	public
	 * @param	mixed	$element
	 * @return	mixed
	 */
	function lastIndexOf( $element )
	{
		for ( $i = ( count( $this->_elements ) - 1 ); $i > 0; $i-- ) 
		{
			if ( $element == $this->get( $i ) )
				return $i;
		}
	}
	
	/**
	 * Removes the element at the specified position in this list.
	 *
	 * @access	public
	 * @param	integer	$index
	 * @return	mixed
	 */
	function remove( $index )
	{
		$element = $this->get( $index );
		
		if ( !is_null( $element ) )
			array_splice( $this->_elements, $index, 1 );
			
		return $element;
	}
	
	/**
	 * Removes from this List all of the elements whose index is between start,
	 * inclusive and end, exclusive.
	 *
	 * @access	public
	 * @param	integer	$start
	 * @param	integer	$end
	 */
	function removeRange( $start, $end )
	{
		array_splice( $this->_elements, $start, $end );
	}
	
	/**
	 * Replaces the element at the specified position in this list with the
	 * specified element.
	 *
	 * @access	public
	 * @param	integer	$index
	 * @param	mixed	$element
	 * @return	mixed
	 */
	function set( $index, $element )
	{
		$previous = $this->get( $index );
		$this->_elements[$index] = $element;

		return $previous;
	}
	
	/**
	 * Returns the number of elements in this list.
	 *
	 * @access	public
	 * @return	integer
	 */
	function size()
	{
		return count( $this->_elements );
	}
	
	/**
	 * Returns an array containing all of the elements in this list in the
	 * correct order.
	 *
	 * @access	public
	 * @return	array
	 */
	function toArray()
	{
		return $this->_elements;
	}
} // END OF ArrayList

?>
