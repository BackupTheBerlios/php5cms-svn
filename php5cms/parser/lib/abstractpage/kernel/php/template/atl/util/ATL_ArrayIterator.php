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


using( 'template.atl.util.ATL_Iterator' );
using( 'template.atl.util.ATL_Array' );


/**
 * ATL_Array itertor class.
 *
 * This kind of iterators are used to walk throug a ATL_Array object.
 *
 * @package template_atl_util
 */
 
class ATL_ArrayIterator extends ATL_Iterator
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
    var $_value;
	
	/**
	 * @access private
	 */
    var $_i;


    /**
     * Constructor
     *
     * @param  mixed  $oarray  ATL_Array object or php array.
	 * @access public
     */
    function ATL_ArrayIterator( &$oarray )
    {
        if ( is_array( $oarray ) ) 
		{
            $this->_src = new ATL_Array( $oarray );
        } 
		else if ( is_a( $oarray, 'atl_array' ) ) 
		{
            $this->_src = &$oarray;
        } 
		else 
		{
			return PEAR::raiseError( "ATL_ArrayIterator requires ATL_Array object or php array.", null, PEAR_ERROR_DIE );
        }
		
        $this->reset();
    }
    

    /**
     * Reset iterator to first array position.
	 *
	 * @access public
     */
    function reset()
    {
        $this->_i      = 0;
        $this->_end    = false;
        $this->_values = $this->_src->_array;
        
		if ( count( $this->_values ) == 0 ) 
		{
            $this->_end = true;
            return;
        }

        $this->_value = $this->_values[0];
    }

    /**
     * Returns next vector item value.
     *
     * @return mixed
     * @throws Error if iterator overpass the vector size.
	 * @access public
     */
    function &next()
    {
        if ( $this->_end || ++$this->_i >= count( $this->_values ) ) 
		{
            $this->_end = true;
            return null;
        }

        $this->_value = $this->_values[$this->_i];
        return $this->_value;
    }

    /**
     * Test if the iteractor has not reached its end.
	 *
     * @return boolean
	 * @access public
     */
    function isValid()
    {
        return !$this->_end;
    }

    /**
     * Retrieve current iterator index.
	 *
     * @return int
	 * @access public
     */
    function index()
    {
        return $this->_i;
    }

    /**
     * Retrieve the current iterator value.
	 *
     * @return mixed
	 * @access public
     */
    function &value()
    {
        return $this->_value;
    }
    
    /**
     * Delete current index.
	 *
	 * @access public
     */
    function remove()
    {
        $this->_src->remove( $this->_value );
    }
} // END OF ATL_ArrayIterator

?>
