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


using( 'util.VectorForwardIterator' );
using( 'util.VectorReverseIterator.' );


/**
 * Vector Class
 *
 * Usage:
 *
 * $vector = new Vector();
 * $vector->add( 1 );
 * $vector->add( 2 );
 * $vector->add( 3 );
 * $vector->add( 4 );
 * $vector->add( 5 );
 *
 * echo 'Iterating Through Elements Forwards:<br>';
 *
 * $iterator = $vector->begin();
 * while ( !$iterator->isDone() ) 
 * {
 *		$value = $iterator->getCurrent();
 *		echo $value . ' ';
 *		$iterator->next();
 * }
 *
 * echo '<br>';
 * echo 'Iterating Through Elements Backwards:<br>';
 *
 * $iterator = $vector->end();
 * while ( !$iterator->isDone() ) 
 * {
 *		$value = $iterator->getCurrent();
 *		echo $value . ' ';
 *		$iterator->next();
 * }
 *
 * echo '<br>';
 * echo 'Iterating Through Both at the same time:<br>';
 *
 * $forwardIterator = $vector->begin();
 * $reverseIterator = $vector->end();
 *
 * while ( !$forwardIterator->isDone() ) 
 * {
 *		$forwardValue = $forwardIterator->getCurrent();
 *		$reverseValue = $reverseIterator->getCurrent();
 *		echo 'F: ' . $forwardValue . ' R: ' . $reverseValue . '<br>';
 *		$forwardIterator->next();
 *		$reverseIterator->next();
 * }
 *
 * @package util
 */

class Vector extends PEAR
{
	/**
	 * @access public
	 */
	var $collection = array();
	
	/**
	 * @access public
	 */
	var $size = 0;

	
	/**
	 * Constructs with an optional array of values.
	 *
	 * @param  $collection array. An array of values
	 * @access public
	 */
	function Vector( $collection = array() ) 
	{
		$this->addArray( $collection );
	}

	
	/**
	 * Appends an array of values.
	 *
	 * @param  $array array. An array of values
	 * @return void
	 * @access public
	 */
	function addArray( $array ) 
	{
		$this->collection = array_merge( $this->collection, $array );
		$this->size += count( $array );
	}

	/**
	 * Appends another Vector.
	 *
	 * @param  $vector Vector. A Vector object
	 * @return void
	 * @access public
	 */
	function addVector( $vector ) 
	{
		$this->addArray( $vector->toArray() );
	}

	/**
	 * Equality test for two vectors. Equal element order, as well as equal
	 * elements, are required for two vectors to be equal.
	 *
	 * @param  $vector Vector. A Vector object
	 * @return boolean
	 * @access public
	 */
	function equals( $vector ) 
	{
		if ( $this->size() != $vector->size() )
			return false;

		$selfIt  = $this->begin();
		$vecIt   = $vector->begin();
		$selfEnd = $this->end();

		while ( $selfIt != $selfEnd ) 
		{
			if ( $selfIt->getValue() != $vecIt->getValue() )
				return false;
			
			$selfIt->next();
			$vecIt->next();
		}

		return true;
	}

	/**
	 * Appends a single element.
	 *
	 * @param  $element mixed. An object or built-in type
	 * @return void
	 * @access public
	 */
	function add( $element ) 
	{
		$this->collection[] = $element;
		$this->size++;
	}

	/**
	 * Appends 'n' copies of an element.
	 *
	 * @param  $element mixed. An object or built-in type
	 * @param  $ncopies int. The number of copies to be inserted
	 * @return void
	 * @access public
	 */
	function addMany( $element, $ncopies ) 
	{
		for ( $i = 0; $i < $ncopies; $i++ )
			$this->add( $element );
	}

	/**
	 * Removes an element from the Vector.
	 *
	 * @param  $element mixed. An object or built-in type
	 * @return void
	 * @access public
	 */
	function remove( $elementToRemove ) 
	{
		foreach ( $this->collection as $key => $element ) 
		{
			if ( $element == $elementToRemove ) 
			{
				$this->size--;
				unset( $this->collection[ $key ] );
			}
		}
	}

	/**
	 * Clears the Vector of all elements.
	 *
	 * @return void
	 * @access public
	 */
	function clear() 
	{
		unset( $this->collection );
		$this->collection = array();
		$this->size = 0;
	}
	
	/**
	 * Containment test for an element.
	 *
	 * @param  $element mixed. An object or built-in type
	 * @return boolean
	 * @access public
	 */
	function contains( $element ) 
	{
		$it  = $this->begin();
		$end = $this->end();

		while ( $it != $end ) 
		{
			if ( $it->getValue() == $element )
				return true;
			
			$it->next();
		}

		return false;
	}

	/**
	 * Containment test for all elements in an array.
	 *
	 * @param  $array array. An array of elements
	 * @return boolean
	 * @access public
	 */
	function containsAll( $array ) 
	{
		foreach ( $array as $element ) 
		{
			if ( !$this->contains( $element ) )
				return false;
		}

		return true;
	}

	/**
	 * Returns the element at the specified index, false otherwise.
	 *
	 * @param  $index int. Index of the element desired
	 * @return mixed
	 * @access public
	 */
	function elementAt( $index ) 
	{
		if ( isset( $this->collection[ $index ] ) )
			return $this->collection[ $index ];
		else
			return false;
	}

	/**
	 * Returns the element at the specified index.
	 *
	 * @param  $index int. Index of the element desired
	 * @return mixed
	 * @access public
	 */
	function get( $index ) 
	{
		return $this->elementAt( $index );
	}

	/**
	 * Returns the first element of the Vector.
	 *
	 * @return mixed
	 * @access public
	 */
	function firstElement() 
	{	
	}

	/**
	 * Returns the last element of the Vector.
	 *
	 * @return mixed
	 * @access public
	 */
	function lastElement() 
	{
	}

	/**
	 * Returns the index of the specified element. Searching begins at the
	 * specified index.
	 *
	 * @param  $element mixed. An object or built-in type
	 * @param  $startIndex int. Index of the element desired
	 * @return int
	 * @access public
	 */
	function indexOf( $element, $startIndex = 0 ) 
	{
		for ( $index = $startIndex; $index < $this->size(); $index++ ) 
		{
			if ( $this->collection[ $index ] == $element )
				return $index;
		}
		
		return false;
	}

	/**
	 * Returns true if this vector is empty, false otherwise.
	 *
	 * @return boolean
	 * @access public
	 */
	function isEmpty() 
	{
		return $this->size == 0;
	}

	/**
	 * Reverses the contents of this vector.
	 *
	 * @return void
	 * @access public
	 */
	function reverse() 
	{
		$reverse = array();
		$it      = $this->end();
		$begin   = $this->begin();

		while ( $it != $begin ) 
		{
			$it->prev();
			$reverse[] = $it->getValue();
		}

		$this->clear();
		$this->addArray( $reverse );
	}

	/**
	 * Sets the specified element at the specified index.
	 *
	 * @param  $index int. Index of the element desired
	 * @param  $element mixed. An object or built-in type
	 * @return void
	 * @access public
	 */
	function set( $index, $element ) 
	{
		$this->collection[ $index ] = $element;
	}

	/**
	 * Returns the number of elements in this vector.
	 *
	 * @return int
	 * @access public
	 */
	function size() 
	{
		return $this->size;
	}

	/**
	 * Swaps the contents of two vectors.
	 *
	 * @return void
	 * @access public
	 */
	function swap( &$vector ) 
	{
		$a1 = $this->toArray();
		$a2 = $vector->toArray();

		$this->clear();
		$vector->clear();
		$this->addArray( $a2 );
		$vector->addArray( $a1 );
	}

	/**
	 * Returns an array representation of the elements contained in this vector.
	 *
	 * @return array
	 * @access public
	 */
	function toArray() 
	{
		return $this->collection;
	}

	
	// vector iterator methods

	/**
	 * Returns a VectorForwardIterator to the beginning of this vector.
	 *
	 * @return VectorForwardIterator
	 * @access public
	 */
	function begin() 
	{
		return new VectorForwardIterator( $this->collection );
	}

	/**
	 * Returns a VectorForwardIterator to the beginning of this vector.
	 *
	 * @return VectorForwardIterator
	 * @access public
	 */
	function createIterator() 
	{
		return $this->begin();
	}

	/**
	 * Returns a VectorReverseIterator to the end of this vector.
	 *
	 * @return VectorReverseIterator
	 * @access public
	 */
	function end() 
	{
		return new VectorReverseIterator( $this->collection );
	}
} // END OF Vector

?>
