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


using ( 'util.Vector' );
using ( 'util.DictionaryIterator' );


/**
 * @package util
 */
 
class Dictionary extends PEAR
{
	/**
	 * @access public
	 */
	var $collection;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Dictionary( $collection = array() ) 
	{
		$this->collection = $collection;
	}

	
	/**
	 * Returns the number of keys in this dictionary.
	 *
	 * @return int
	 * @access public
	 */
	function size() 
	{
		return count( $this->collection );
	}

	/**
	 * Tests if this dictionary maps no keys to values.
	 *
	 * @return bool
	 * @access public
	 */
	function isEmpty() 
	{
		if ( $this->size() == 0 ) 
			return true;
		
		return false;
	}

	/**
	 * Returns the value of the key if it exists. Returns false otherwise.
	 *
	 * @param  $key mixed.  The key to an object in the dictionary
	 * @return mixed
	 * @access public
	 */
	function get( $key ) 
	{
		if ( $this->hasValue( $key ) )
			return $this->collection[ $key ];
		
		return false;
	}

	/**
	 * Sets the key/value pair in the dictionary.
	 *
	 * @param  $key mixed.  The key to an object in the dictionary
	 * @param  $value mixed.  The value of the key
	 * @return void
	 * @access public
	 */
	function set( $key, $value ) 
	{
		$this->collection[ $key ] = $value;
	}

	/**
	 * Removes the value of $key from the dictionary.
	 *
	 * @param  $key mixed.  The key to an object in the dictionary
	 * @return void
	 * @access public
	 */
	function remove( $key ) 
	{
		unset( $this->collection[ $key ] );
	}

	/**
	 * Tests if there is a key that maps into the specified value in this 
	 * hashtable.
	 *
	 * @param  $value mixed.  The value of the key
	 * @return boolean
	 * @access public
	 */
	function hasKey( $valueToSearch ) 
	{
		foreach ( $this->toArray() as $key => $value ) 
		{
			if ( $value == $valueToSearch )
				return true;
		}

		return false;
	}

	/**
	 * Tests if there is a key that maps into the specified value in this 
	 * hashtable.
	 *
	 * @param  $value mixed.  The value of the key
	 * @return boolean
	 * @access public
	 */
	function hasValue( $keyToSearch ) 
	{
		if ( isset( $this->collection[ $keyToSearch ] ) ) 
			return true;
		
		return false;
	}

	/**
	 * @access	public
	 * @param	mixed	$values
	 */
	function setAll( $values )
	{
		if ( is_array( $values ) ) 
		{
			foreach ( $values as $key => $value )
				$this->set( $key, $value );
		}
	}
	
	/**
	 * Returns an array of the all keys and values.
	 *
	 * @return mixed
	 * @access public
	 */
	function toArray() 
	{
		return $this->collection;
	}

	/**
	 * Returns a Vector of the keys in this dictionary. 
	 *
	 * @return Vector
	 * @access public
	 */
	function keys() 
	{
		$keys = new Vector();
		
		foreach ( $this->toArray() as $key => $value )
			$keys->add( $key );

		return $keys;
	}

	/**
	 * Returns a Vector of the values in this dictionary. Use the Enumeration 
	 * methods on the returned object to fetch the elements sequentially. 
	 *
	 * @return Vector
	 * @access public
	 */
	function elements() 
	{
		$elements = new Vector();

		foreach ( $this->toArray() as $value )
			$elements->add( $value );

		return $elements;
	}

	/**
	 * Clears the hash table of all key/values.
	 *
	 * @return void
	 * @access public
	 */
	function clear() 
	{
		unset( $this->collection );
		$this->collection = array();
	}

	/**
	 * Returns a new Iterator to this dictionary object.
	 *
	 * @return Iterator
	 * @access public
	 */
	function createIterator() 
	{
		return new DictionaryIterator( $this->collection, $this->keys() );
	}

	/**
	 * Returns a new Iterator to this dictionary object.
	 *
	 * @return Iterator
	 * @access public
	 */
	function begin() 
	{
		return $this->createIterator();
	}
} // END OF Dictionary

?>
