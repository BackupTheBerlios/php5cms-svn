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


/**
 * Iterator interface.
 *
 * This class provides common methods for Iterator objects. 
 *
 * An iterator is a 'pointer' to a data item extracted from some collection of
 * resources.
 *
 * The aim of Iterator is to allow some abstraction between your program
 * resources and the way you fetch these resources.
 *
 * Thus, you can loop over a file content and later replace this file with a
 * database backend whithout changing the program logic.
 *
 * @package template_atl_util
 */
 
class ATL_Iterator extends PEAR
{
    /**
     * Reset iterator to first item.
     *
     * This method should throw an exception for once only iterators.
     * 
     * @throws Error
	 * @access public
     */
    function reset()
    {
        return PEAR::raiseError( "Method not implemented." );
    }
    
    /**
     * Test if current item is not the end of iterator.
     *
     * @return boolean
	 * @access public
     */
    function isValid()
    {
        return PEAR::raiseError( "Method not implemented." );
    }
    
    /**
     * Iterate on the next element and returns the next item.
     * 
     * @return mixed (by reference)
	 * @access public
     */
    function &next()
    {
        return PEAR::raiseError( "Method not implemented." );
    }

    /**
     * Retrieve the current item index.
     * 
     * @return int
	 * @access public
     */
    function index()
    {
        return PEAR::raiseError( "Method not implemented." );
    }
    
    /**
     * Retrieve current item value.
     * 
     * @return mixed (by reference)
	 * @access public
     */
    function &value()
    {
       	return PEAR::raiseError( "Method not implemented." );
    }
    
    /**
     * (optional) Remove the current value from container.
     *
     * Implement this method only when the iterator can do a secured remove
     * without breaking other iterators works.
	 *
	 * @access public
     */
    function remove()
    {
        return PEAR::raiseError( "Method not implemented." );
    }
	
    /**
     * (optional) Additional index for hashes.
     * 
     * @return string
	 * @access public
     */
    function key()
    {
        return $this->index();
    }
} // END OF ATL_Iterator

?>
