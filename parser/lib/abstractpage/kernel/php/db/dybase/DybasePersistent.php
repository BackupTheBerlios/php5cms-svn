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
 * Base class for all persistent capable objects. 
 * 
 * It is not required to derive all peristent
 * capable objects from DybasePersistent class, but in this case 
 * you will have to invoke store/load methods of the Storage class.
 *
 * @package db_dybase
 */
  
class DybasePersistent 
{ 
	var $__raw__;
    var $__dirty__;
    var $__oid__;
    var $__storage__;
    
	
	/**
	 * Constructor
	 */
    function DybasePersistent( $oid = null )
	{ 
        $this->__oid__     = $oid;
        $this->__raw__     = false;
        $this->__storage__ = null;
    }
	
	
	/**
     * If object is in raw state than load object from the storage.
     */
    function load() 
	{
        if ( $this->__raw__ )
            $this->__storage__->loadObject( $this );
    }

    /**
     *  Check if object is already loaded or explicit invocation of load() method is required.
     */
    function isLoaded() 
	{ 
        return !$this->__raw__;
    }

    /**
     *  Check if object is persistent (assigned persistent OID).
     */
    function isPersistent() 
	{ 
        return $this->__oid__ != null;
    }

    /**
     * Check if object was modified during current transaction.
     */
    function isModified() 
	{
        return $this->__dirty__ != null;
    }

    /**
     * Mark object as modified. This object will be automaticaly stored to the database
     * during transaction commit.
     */
    function modify()
	{
        if ( $this->__storage__ != null && $this->__dirty__ ) 
           $this->__storage__->modifyObject( $this );
    }

    /**
     * If object is not yet persistent, then make it persistent and store in the storage.
     */
    function store()
	{ 
        if ( $this->__storage__ != null ) 
            $this->__storage__->storeObject( $this );
    }   

    /**
     * Get storage in which object is stored, None if object is not yet persistent.
     */
    function &getStorage()
	{
        return $this->__storage__;
    }

    /**
     * Remove object from the storage.
     */
    function deallocate() 
	{
        if ( $this->__storage__ != null ) 
		{
            $this->__storage__->deallocateObject( $this );
            unset( $this->__storage__ );
        }
    }

    /**
     * Override this method if you want to provibit implicit loading of all object referenced from this object
     */
    function recursiveLoading()
	{
        return true;
    }

    /**
     * Function called after loading of the object from the storage.
     */
    function onLoad()
	{ 
    }
} // END OF DybasePersistent

?>
