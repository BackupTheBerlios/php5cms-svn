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


/**
 * Iterator for DBResult object.
 *
 * This class is an implementation of the Iterator Interface
 * for DBResult objects produced by the usage of DB package.
 *
 * @package template_atl
 */
 
class ATL_DBResultIterator extends ATL_Iterator
{
	/**
	 * @access private
	 */
    var $_src;
	
	/**
	 * @access private
	 */
	var $_value;

	/**
	 * @access private
	 */	
    var $_index = -1;
	
	/**
	 * @access private
	 */
    var $_end = false;
    
	
    /**
     * Constructor
     *
     * @param  DBResult $result
     *         The query result.
	 * @access public
     */
    function ATL_DBResultIterator( &$result )
    {
        $this->_src =& $result;
        $this->reset();
    }

	
	/**
	 * @access public
	 */
    function reset()
    {
        if ( $this->size() == 0 ) 
		{
            $this->_end = true;
            return;
        }

        $this->_index = 0;        
        $this->_end   = false;
        
		unset( $this->_value );
        $this->_value = $this->_src->fetchRow();        
    }
    
    /**
     * Return the number of rows in this result.
     *
     * @return int
	 * @access public
     */
    function size()
    {
        if ( !isset( $this->_size ) )
            $this->_size = $this->_src->numRows();
        
        return $this->_size;
    }

    /**
     * Returns true if end of iterator has not been reached yet.
	 *
	 * @access public
     */
    function isValid()
    {
        return !$this->_end;
    }

    /**
     * Return the next row in this result.
     *
     * This method calls fetchRow() on the DBResult, the return type depends
     * of the DBresult->fetchmod. Please specify it before executing the 
     * template.
     *
     * @return mixed
	 * @access public
     */
    function &next()
    {
        if ( $this->_end || ++ $this->_index >= $this->size() ) 
		{
            $this->_end = true;
            return false;
        }

        unset( $this->_value );
        $this->_value = $this->_src->fetchRow();
        
		return $this->_value;
    }
    
    /**
     * Return current row.
     *
     * @return mixed
	 * @access public
     */
    function &value()
    {
        return $this->_value;
    }

    /**
     * Return current row index in resultset.
     *
     * @return int
	 * @access public
     */
    function index()
    {
        return $this->_index;
    }
} // END OF ATL_DBResultIterator

?>
