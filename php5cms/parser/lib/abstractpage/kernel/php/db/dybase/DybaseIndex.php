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


using( 'db.dybase.DybasePersistent' );
using( 'db.dybase.DybaseIndexIterator' );


/**
 * Indexed collection of persistent object        
 * This collection is implemented using B+Tree.
 *
 * @package db_dybase
 */
 
class DybaseIndex extends DybasePersistent 
{
    var $index;

	
    /**
     * Delete index.
     */
    function drop()
	{
        dybase_dropindex( $this->__storage__->db, $this->index );
    }
    
    /**
     * Remove all entries from the index.
     */
    function clear() 
	{
        dybase_clearindex( $this->__storage__->db, $this->index );
    }
    
    /** 
     * Insert new entry in the index.
     */
    function insert( $key, &$value ) 
	{
        $this->__storage__->makeObjectPersistent( $value );
        return dybase_insertinindex( $this->__storage__->db, $this->index, $key, $value->__oid__, false );
    }

    /** 
     * Set object for the specified key, if such key already exists in the index, 
     * previous association of this key will be replaced.
     */
    function set( $key, &$value ) 
	{
        $this->__storage__->makeObjectPersistent( $value );
        dybase_insertinindex( $this->__storage__->db, $this->index, $key, $value->__oid__, true );
    }

    /**
     * Remove entry from the index. If index is unique, then value can be omitted.
     */
    function remove( $key, $value = null ) 
	{        
        if ( $value == null ) 
            $oid = 0;
        else
            $oid = $value->__oid__;
        
        return dybase_removefromindex( $this->__storage__->db, $this->index, $key, $oid );
    }

    /**
     * Find object in the index with specified key. 
     * If no key is found null is returned;
     * If one entry is found then the object associated with this key is returned;
     * Othersise list of selected object is returned.
     */    
    function &get( $key ) 
	{
        $result = &$this->find( $key, true, $key, true );
		
        if ( $result != null && sizeof( $result ) == 1 )
            return $result[0];
        
        return $result;
    }

    /**
     * Find objects in the index with key belonging to the specified range.
     * high and low paremeters can be assigned null value, in this case there is no
     * correpondent boundary. Each boundary can be exclusive or inclusive.  
     * Returns null if no object is found or list of selected objects
     */
    function &find( $low = null, $lowInclusive = true, $high = null, $highInclusive = true ) 
	{
        $result = &dybase_searchindex( $this->__storage__->db, $this->index, $low, $lowInclusive, $high, $highInclusive );
		
        if ( $result != null ) 
		{ 
            foreach ( $result as $i=>$oid ) 
                $result[$i] = &$this->__storage__->_lookupObject( $oid );
        } 
		else 
		{ 
            return PEAR::raiseError( "Search failed." );
        }
		
        return $result;
    }

    /**
     * Get iterator for index. Iterator will traverse all objects in the in key ascending order.
     */
    function &iterator() 
	{ 
        return new DybaseIndexIterator( $this->__storage__, $this->index );
    }
} // END OF DybaseIndex

?>
