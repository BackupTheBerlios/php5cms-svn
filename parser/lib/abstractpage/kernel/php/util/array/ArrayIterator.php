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
 * Class <code>ArrayIterator</code> offers an implementation of the
 * <code>Iterator</code> interface for iterating over the elements in an array.
 * <p>
 *   The advantage of this iterator over the built-in array iteration functions
 *   is two-fold:
 * </p>
 * <ol>
 *   <li>
 *     This is a genuine <code>Iterator</code>, and can therefore be used in
 *     generic algorithms.
 *   </li>
 *   <li>
 *     It's possible to run multiple iterations on the same array concurrently.
 *   </li>
 * </ol>
 * <p>
 *   This class uses the built-in array iteration functions <code>reset</code>,
 *   <code>next</code> and <code>key</code>. On the one hand this means the
 *   overhead in using this iterator is fairly small. On the other hand it also
 *   means that using two or more iterators on the same array at the same time
 *   is incredibly slow: when one iterator moves to another element in the
 *   array, all other iterators will be out of sync and must re-iterate the
 *   entire array (worst case) to find their correct positions again. However,
 *   this almost never happens; in most cases just one iterator is used on some
 *   array.
 * </p>
 *
 * @package util_array
 */
 
class ArrayIterator extends Iterator
{
    /**
     * The array to iterate over
     * @var  array
	 * @access public
     */
    var $array;

    /**
     * The current key
     * @var  string
	 * @access public
     */
    var $key;

    /**
     * The current value
     * @var  mixed
	 * @access public
     */
    var $value;
    

    /**
     * Constructor
	 *
     * @param  $array the array to create an iterator for
	 * @access public
     */
    function ArrayIterator( &$array )
    {
        $this->array =& $array;
        $this->reset();
    }

    
    /**
     * @return void
	 * @access public
     */
    function reset()
    {
        reset( $this->array );
        $this->key = key( $this->array );
		
        if ( "$this->key" != '' )
            $this->value =& $this->array[$this->key];
    }

    /**
     * @return void
	 * @access public
     */
    function next()
    {
        if ( $this->key != key( $this->array ) )
        {
            reset( $this->array );
            while ( key( $this->array ) != $this->key )
                next( $this->array );
        }
		
        next( $this->array );
        $this->key = key( $this->array );
		
        if ( "$this->key" != '' )
            $this->value =& $this->array[$this->key];
    }

    /**
     * @return bool
	 * @access public
     */
    function isValid()
    {
        return "$this->key" != '';
    }

    /**
     * @return mixed
	 * @access public
     */
    function &getCurrent()
    {
        return $this->value;
    }

    /**
     * Return the current key.
	 *
     * @return string
	 * @access public
     */
    function getKey()
    {
        return $this->key;
    }
} // END OF ArrayIterator

?>
