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


using( 'template.atl.util.ATL_Dictionary' );


/**
 * Hash iterator.
 *
 * This kind of iterators are used to walk throug Hash objects.
 *
 * @package template_atl_util
 */
 
class ATL_HashIterator extends PEAR
{
	/**
	 * @access private
	 */
    var $_src;
	
	/**
	 * @access private
	 */
    var $_values;
	
	/**
	 * @access private
	 */
    var $_keys;
	
	/**
	 * @access private
	 */
    var $_key;
	
	/**
	 * @access private
	 */
    var $_value;
	
	/**
	 * @access private
	 */
    var $_i = -1;
	
	/**
	 * @access private
	 */
    var $_end = false;

	
    /**
     * Constructor
     *
     * @param mixed $hash -- ATL_Dictionary object or associative php array.
	 *
	 * @throws Error  When $hash is not an array or an Hash object.
	 * @access public
     */
    function ATL_HashIterator( &$hash )
    {
        if ( is_array( $hash ) ) 
		{
            $this->_src = new ATL_Dictionary( $hash );
        } 
		else if ( is_a( $hash, 'atl_dictionary' ) ) 
		{
            $this->_src  = &$hash;
        } 
		else 
		{
			return PEAR::raiseError( "ATL_HashIterator requires associative array or ATL_Dictionary.", null, PEAR_ERROR_DIE );
        }
		
        $this->reset();
    }
    

    /**
     * Reset iterator to first element.
	 *
	 * @access public
     */
    function reset()
    {
        // store a copy of hash references so a modification of source data
        // won't affect iterator.
        $this->_values = $this->_src->_hash;
        $this->_keys   = $this->_src->keys();
        
        if ( count( $this->_keys ) == 0 ) 
		{
            $this->_end = true;
            return;
        }

        $this->_i     = 0;        
        $this->_end   = false;
        $this->_key   = $this->_keys[0];
        $this->_value = $this->_values[$this->_key];
    }

    /**
     * Test is end of iterator is not reached.
	 *
	 * @access public
     */
    function isValid()
    {
        return !$this->_end;
    }

    /**
     * Return next Hash item.
     *
     * This method also set _key and _value.
     *
     * @return mixed (by reference) or false if end reached
	 * @access public
     */
    function &next()
    {
        if ( $this->_end || ++$this->_i >= count( $this->_keys ) )
		{
            $this->_end = true;
            return null;
        }

        $this->_key   = $this->_keys[$this->_i];
        $this->_value = $this->_values[$this->_key];
        
		return $this->_value;
    }

    /**
     * Return current iterator key.
     *
     * @return string
	 * @access public
     */
    function key()
    {
        return $this->_key;
    }

    /**
     * Return current index (position in iteration).
     *
     * @return int
	 * @access public
     */
    function index()
    {
        return $this->_i;
    }

    /**
     * Return current iterator value.
     *
     * @return mixed
	 * @access public
     */
    function &value()
    {
        return $this->_value;
    }

    /**
     * Remove current iterated item from source.
	 *
	 * @access public
     */
    function remove()
    {
        $this->_src->remove( $this->_key );
    }
} // END OF ATL_HashIterator

?>
