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


/**
 * Class Iterator is an abstract base class for iterators.
 * <p>
 *   Class <code>Iterator</code> offers the interface of all iterators. The
 *   comments for each method decribe the exact behavior the method should
 *   implement.
 * </p>
 * <p>
 *   When an iterator is created for some object, it's imperative that the
 *   object doesn't change for the duration of the iteration. This may or may
 *   not lead to unexpected results, depending on the object iterated over.
 *   However, it is possible to modify the object returned by the
 *   <code>getCurrent()</code> method of the iterator. For example, when
 *   iterating over arrays with an <code>ArrayIterator</code>, no elements
 *   should be removed from or added to the array, but individual elements may
 *   be altered.
 * </p>
 * <p>
 *   Given an iterator <code>$it</code>, the iteration loop is run as follows:
 * </p>
 * <pre>
 *   for ($it->reset(); $it->isValid(); $it->next())
 *   {
 *       $object =& $it->getCurrent();
 *       doSomethingWith($object);
 *   }
 * </pre>
 * <p>
 *   Note that every iterator should reset (or: initialize) itself on 
 *   construction, so that the loop can also be run like: <code>for ($it =& new
 *   Iterator; $it->isValid(); $it->next()) ...</code>.
 * </p>
 *
 * @package util
 */
 
class Iterator extends PEAR
{
    /**
     * Initialize this iterator.
	 *
     * @return void
	 * @access public
     */
    function reset()
    {
    }
	
	/**
	 * Sets the list iterator to the next element.
	 *
	 * @return void
	 * @access public
	 */
	function next() 
	{
	}

	/**
	 * Sets the list iterator to the previous element.
	 *
	 * @return void
	 * @access public
	 */
	function prev() 
	{
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
	}

    /**
     * Check if the iterator is valid.
	 *
     * @return bool
	 * @access public
     */
    function isValid()
    {
    }
	
    /**
     * Return a reference to the current object. The behavior of this method
     * is undefined if <code>isValid()</code> returns <code>false</code>.
	 *
     * @return mixed
	 * @access public
     */
	function &getCurrent() 
	{
	}

	/**
	 * Sets the value of the element currently pointed to by this iterator.
	 *
	 * @return void
	 * @access public
	 */
	function setCurrent( $value ) 
	{
	}
} // END OF Iterator

?>
