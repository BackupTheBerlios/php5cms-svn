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
|Authors: Vincent Oostindië <eclipse@sunlight.tmfweb.nl>               |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'util.Iterator' );


/**
 * @package util
 */
 
class DictionaryIterator extends Iterator 
{
	/**
	 * @access public
	 */
	var $list;
	
	/**
	 * @access public
	 */
	var $keys;
	
	/**
	 * @access public
	 */
	var $iterator;

	
	/**
	 * Constructor
	 * Initializes with the given Dictionary reference.
	 *
	 * @param  &$list  Dictionary. A Dictionary reference
	 * @access public
	 */
	function DictionaryIterator( &$list, $keys ) 
	{
		$this->list	= &$list;
		$this->keys = &$keys;
		$this->iterator = $this->keys->begin();
	}

	
	/**
	 * Sets the list iterator to the next element.
	 *
	 * @return void
	 * @access public
	 */
	function next() 
	{
		$this->iterator->next();
	}

	/**
	 * Sets the list iterator to the previous element.
	 * 
	 * @return void
	 * @access public
	 */
	function prev() 
	{
		$this->iterator->prev();
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
		return $this->iterator->isDone();
	}

	/**
	 * Returns the element currently pointed to by this iterator.
	 *
	 * @return mixed
	 * @access public
	 */
	function getCurrent() 
	{
		return $this->list[ $this->iterator->getCurrent() ];
	}

	/**
	 * Returns the key to the element currently pointed to by this iterator.
	 *
	 * @return mixed
	 * @access public
	 */
	function getCurrentKey() 
	{
		return $this->iterator->getCurrent();
	}

	/**
	 * Sets the value of the element currently pointed to by this iterator.
	 *
	 * @return void
	 * @access public
	 */
	function setCurrent( $value ) 
	{
		$this->list[ $this->iterator->getCurrent() ] = $value;
	}
} // END OF DictionaryIterator

?>
