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
using( 'template.atl.util.ATL_HashIterator' );


/**
 * Wrapper for oriented object associative arrays.
 *
 * This class implements a simple interface quite like java to handle
 * associatives arrays (Hashtables).
 *
 * Note:
 *
 * Some problems may occurs with object references until php 5 to avoid
 * unwanted object copy, use setRef() method with objects or pass Ref objects
 * to set().
 *
 * @package template_atl_util
 */
 
class ATL_Dictionary extends PEAR
{
	/**
	 * @access private
	 */
    var $_hash;

	
    /**
     * Constructor
     *
     * @param  array  $array optional  An associative php array
	 * @access public
     */
    function ATL_Dictionary( $array = array() )
    {
        $this->_hash = array();
        $keys = array_keys( $array );
		
        foreach ( $keys as $key )
            $this->_hash[$key] &= $array[$key];
    }


    /**
     * Set a value in this hash.
     *
     * @param  string $key
     * @param  string $value
	 * @access public
     */
    function set( $key, &$value )
    {
        $this->_hash[$key] = $value;
    }

    /**
     * Reference set.
     *
     * Until php 5, it's the only way to avoid object/variable copy.
     *
     * @param  string $key
     * @param  reference $value
	 * @access public
     */
    function setRef( $key, &$value )
    {
        $this->_hash[$key] = $value;
    }

    /**
     * Set a map of values.
     *
     * @param  mixed $hash An Hash object or an associative php array.
	 * @access public
     */
    function setAll( &$hash )
    {
        if ( !is_array( $hash ) && is_a( $hash, 'atl_dictionary' ) )
            $hash = $hash->toHash();
        
        $keys = array_keys( $hash );
        
		foreach ( $keys as $key )
            $this->_hash[$key] = $hash[$key];
    }

    /**
     * Retrieve value associated to specified key.
     * 
     * @param  string $key
     * @return reference
	 * @access public
     */
    function &get( $key )
    {
        if ( $this->containsKey( $key ) )
            return $this->_hash[$key];
        
        return null;
    }

    /**
     * Remove element associated to specified key from hash.
     *
     * @param  string $key
	 * @access public
     */
    function remove( $key )
    {
        $keys = $this->keys();
        $i = array_search( $key, $keys );
		
        if ( $i !== false ) 
		{
            // unset hash element to fix many bugs that should appear while
            // iterating and using references to this element.
            unset( $this->_hash[$key] );
			
            // return array_splice( $this->_hash, $i, 1 );
        }
    }

    /**
     * Remove an element from the Hash.
     *
     * @param  mixed  $o  Element to remove from Hash
	 * @access public
     */
    function removeElement( &$o )
    {
        $i = 0;
        $found = false;
        
		foreach ( $this->_hash as $key => $value ) 
		{
            if ( $value == $o ) 
			{
                $found = $i;
                break;
            }
			
            $i++;
        }
		
        if ( $found !== false )
            return array_splice( $this->_hash, $found, 1 );
    }

    /**
     * Returns true is hashtable empty.
     * 
     * @return boolean
	 * @access public
     */
    function isEmpty()
    {
        return $this->size() == 0;
    }

    /**
     * Retrieve hash size (number of elements).
     * 
     * @return int
	 * @access public
     */
    function size()
    {
        return count( $this->_hash );
    }

    /**
     * Retrieve hash values array.
     * 
     * @return hashtable
	 * @access public
     */
    function &values()
    {
        $v = array();
        
		foreach ( $this->_hash as $key => $ref )
            $v[] =& $ref;
        
        return $v;
    }

    /**
     * Retrieve hash keys array.
     * 
     * @return array
	 * @access public
     */
    function keys()
    {
        return array_keys( $this->_hash );
    }

    /**
     * Retrieve an hash iterator ready to use.
     * 
     * @return ATL_HashIterator
	 * @access public
     */
    function getNewIterator()
    {
        return new ATL_HashIterator( $this );
    }

    /**
     * Test if this hash contains specified key.
     * 
     * @param  string $key
     * @return boolean
	 * @access public
     */
    function containsKey( $key )
    {
        return array_key_exists( $key, $this->_hash );
    }

    /**
     * Test if this hash contains specified value.
     * 
     * @param  mixed  $value  The value to search
     * @return boolean
	 * @access public
     */
    function containsValue( $value )
    {
        foreach ( $this->_hash as $k => $v ) 
		{
            if ( $v == $value ) 
                return true;
        }
		
        return false;
    }

    /**
     * Returns the php array (hashtable) handled by this object.
     * 
     * @return hashtable
	 * @access public
     */
    function &toHash()
    {
        $result = array();
        
		foreach ( $this->_hash as $key => $value )
            $result[$key] =& $value;
        
        return $result;
    }

    /**
     * Create an Hash object from a simple php array variable.
     *
     * This method assumes that the array is composed of serial key, value
     * elements. For each pair of array element, the former will be used as key
     * and the latter as value.
     *
     * @param array $array -- php array.
     *
     * @return Hash
     * @static
	 * @access public
     */
    function arrayToHash( $array )
    {
        $h = new ATL_Dictionary();
        
		while ( count( $array ) > 1 ) 
		{
            $key = array_shift( $array );
            $h->set( $key, array_shift( $array ) );
        }
		
        return $h;
    }

    /**
     * Generate and return a string representation of this hashtable.
     *
     * @return string
	 * @access public
     */
    function toString()
    {
        return ATL_Template::_hashToString( $this->toHash() );
    }

    /**
     * Sort hashtable on its keys.
	 *
	 * @access public
     */
    function sort()
    {
        ksort( $this->_hash );
    }
} // END OF ATL_Dictionary

?>
