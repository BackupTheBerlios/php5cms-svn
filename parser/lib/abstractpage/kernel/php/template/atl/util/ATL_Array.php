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
|Authors: Laurent Bedubourg <laurent.bedubourg@free.fr>                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'template.atl.ATL_Template' );
using( 'template.atl.util.ATL_Iterable' );
using( 'template.atl.util.ATL_ArrayIterator' );


/** 
 * Class wrapper for oriented object arrays.
 *
 * This class implements a simple interface quite like java to handle array in
 * a good old oo way.
 *
 * @package template_atl_util
 */
 
class ATL_Array extends ATL_Iterable 
{
	/**
	 * @access private
	 */
    var $_array;
	
	/**
	 * @access private
	 */
    var $_size = 0;


    /**
     * Constructor
     *
     * @param array $array optional 
     *        The php array this vector will manage.
	 *
	 * @access public
     */
    function ATL_Array( $array = array() )
    {
        $this->_array = array();
        
		for ( $i = 0; $i < count( $array ); $i++ )
            $this->pushRef( $array[$i] );
    }

	
    /**
     * Retrieve an iterator ready to used.
	 *
     * @return ATL_ArrayIterator
	 * @access public
     */
    function getNewIterator()
    {
        return new ATL_ArrayIterator( $this );
    }

    /**
     * Returns the vector number of elements.
	 *
     * @return int
	 * @access public
     */
    function size()
    {
        return count( $this->_array );
    }

    /**
     * Returns true if this vector is empty, false otherwise.
	 *
     * @return boolean
	 * @access public
     */
    function isEmpty()
    {
        return $this->size() == 0;
    }

    /**
     * Retrieve element at specified index.
	 *
     * @return mixed
     * @throws Error
	 * @access public
     */
    function &get($i)
    {
        if ( $i > $this->size() || $i < 0 ) 
            return PEAR::raiseError( "Index out of bounds ($i)." );
        
        return $this->_array[$i];
    }

    /**
     * Retrieve index of specified element.
     *
     * @return int The element index of false if element not in vector.
	 * @access public
     */
    function indexOf( $element )
    {
        for ( $i = 0; $i < count( $this->_array ); $i++ ) 
		{
            if ( $this->_array[$i] === $element ) 
                return $i; 
        }
		
        return false;
    }

    /**
     * Set the item at specified index.
     *
     * @throws Error if index is greated that vector limit
     * @return Old element
	 * @access public
     */
    function &set( $i, $item )
    {
        return $this->setRef( $i, $item );
    }

    /**
     * Set the item at specified index (by reference).
     *
     * @throws Error if index is greated that vector limit
     * @return Old element
	 * @access public
     */
    function &setRef( $i, &$item )
    {
        if ( $i > $this->size() || $i < 0 ) 
            return PEAR::raiseError( "Index out of bounds ($i)." );
        
        $temp = $this->_array[$i];
        $this->_array[$i] = $item;
		
        return $temp;        
    }

    /**
     * Test if the vector contains the specified element.
     *
     * @param  mixed $item The item we look for.
	 * @access public
     */
    function contains( $o )
    {
        for ( $i = 0; $i < $this->size(); $i++ ) 
		{
            if ( $this->_array[$i] === $o ) 
				return true;
        }
		
        return false;
    }

    /**
     * Add an element to the vector.
     *
     * @param  mixed $o The item to add.
	 * @access public
     */
    function add( $o )
    {
        $this->push( $o );
    }

    /**
     * Remove object from this vector.
     *
     * @param  mixed $o Object to remove.
     * @return boolean true if object removed false if not found
	 * @access public
     */
    function remove( $o )
    {
        $i = $this->indexOf( $o );
		
        if ( $i !== false ) 
		{
            $this->removeIndex( $i );
            return true;
        }
		
        return false;
    }

    /**
     * Remove specified index from vector.
     *
     * @param  int $i Index
     * @throws Error
	 * @access public
     */
    function removeIndex( $i )
    {
        if ( $i > $this->size() || $i < 0 ) 
           	return PEAR::raiseError( "Index out of bounds ($i)." );
        
        // $this->_array = array_splice( $this->_array, $i, 1 );
        array_splice( $this->_array, $i, 1 );
    }

    /**
     * Clear vector.
	 *
	 * @access public
     */
    function clear()
    {
        $this->_array = array();
    }

    /**
     * Add an element at the end of the vector (same as add()).
     *
     * @param  mixed $o Item to append to vector.
	 * @access public
     */
    function push( &$o )
    {
        array_push( $this->_array, $o );
    }

    /**
     * Add an element at the end of the vector (same as add()).
     *
     * @param  mixed $o Item to append to vector.
	 * @access public
     */
    function pushRef( &$o )
    {
        $this->_array[] = $o;
    }

    /**
     * Retrieve vector values.
     *
     * The returned array contains references to internal data.
     *
     * @return array
	 * @access public
     */
    function &values()
    {
        $v = array();
        
		for ( $i = 0; $i < $this->size(); $i++ )
            $v[] =& $this->_array[$i];
        
        return $v;
    }

    /**
     * Remove the last element of the vector and returns it.
     *
     * @return mixed
	 * @access public
     */
    function &pop()
    {
        return array_pop( $this->_array );
    }

    /**
     * Extract and return first element of ATL_Array.
	 *
     * @return mixed
	 * @access public
     */
    function &shift()
    {
        return array_shift( $this->_array );
    }

    /**
     * Retrieve a php array for this vector.
     *
     * @return array
	 * @access public
     */
    function &toArray()
    {
        return $this->values();
    }

    /**
     * Retrieve a string representation of the array.
	 *
	 * @access public
     */
    function toString()
    {
        return ATL_Template::_arrayToString( $this->values() );
    }
} // END OF ATL_Array

?>
