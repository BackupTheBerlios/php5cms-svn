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


/**
 * Iterator for index. 
 * All objects in index will be traversed in key ascending order.
 *
 * @package db_dybase
 */
 
class DybaseIndexIterator 
{
    var $storage;
    var $iterator;

	
	/**
	 * Constructor
	 */
    function DybaseIndexIterator( &$storage, $index, $low = null, $lowInclusive = false, $high = null, $highInclusive = false, $ascent = true ) 
	{ 
        $this->storage  = &$storage;
        $this->iterator = dybase_createiterator( $storage->db, $index, $low, $lowInclusive, $high, $highInclusive, $ascent );
    }

    function &next()
	{ 
        return $this->storage->_lookupObject( dybase_iteratornext( $this->iterator ) );
    }
        
    function close() 
	{
        dybase_freeiterator( $this->iterator );
    }
} // END OF DybaseIndexIterator

?>
