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


using( 'db.dybase.DybaseIndex' );


/**
 * Main dybase API class.
 *
 * @package db_dybase
 */

class DybaseStorage
{ 
    var $db;
    var $pagePoolSize;
    var $objByOidMap;

	
    /**
     * Constructor
     *    
     * @param pagePoolSize    size of database page pool in bytes (larger page pool usually leads to better performance)
     */                      
    function DybaseStorage( $pagePoolSize = 4194304 ) 
	{
        $this->pagePoolSize = $pagePoolSize;
    }

	
    /**
     * Open database.
	 *    
     * @return true if database was successully open, false otherwise
     */
    function open( $path ) 
	{
        $this->db = dybase_open( $path, $this->pagePoolSize );
		
        if ( $this->db != 0 ) 
		{ 
            $this->objectCacheUsed = 0;
            $this->objByOidMap     = array();
            $this->modifiedList    = array();
            
			return true;
        } 
		else 
		{
            return false;
        }
    }

    /**
     * Close the storage.
     */
    function close()
	{
        dybase_close( $this->db );
    }

    /**
     * Commit current transaction.
     */
    function commit()
	{
        for ( $i = 0; $i < sizeof( $this->modifiedList ); $i++ )
             $this->storeObject( $this->modifiedList[$i] );
        
        $this->modifiedList = array();
        dybase_commit( $this->db );
    }

    /**
     * Rollback current transaction.
     */
    function rollback()
	{
        $this->modifiedList = array();
        dybase_rollback( $this->db );
        $this->resetHash();
    }

    /**
     * Get storage root object (null if root was not yet specified).
     */
    function &getRootObject()
	{
        return $this->_lookupObject( dybase_getroot( $this->db ) );
    }

    /**
     * Specify new storage root object.
     */
    function setRootObject( &$root ) 
	{
        $this->makeObjectPersistent( $root );
        dybase_setroot( $this->db, $root->__oid__ );
    }

    /**     
     * Deallocate object from the storage.
     */
    function deallocateObject( &$obj ) 
	{
        if ( $obj->__oid__ != null ) 
		{
            unset( $this->objByOidMap[$obj->__oid__] );
            dybase_deallocate( $this->db, $obj->__oid__ );
            $obj->__oid__ = null;
        }
    }

    /**
     * Make object peristent (assign OID to the object).
     */
    function makeObjectPersistent( &$obj ) 
	{
        if ( $obj->__oid__ == null ) 
            $this->storeObject( $obj );
    }

    /**
     * Mark object as modified. This object will be automaticaly stored to the database
     * during transaction commit.
     */
    function modifyObject( &$obj ) 
	{
        $obj->__dirty__ = true;
        $this->modifiedList[] = &$obj;
    }

    /**
     * Make object persistent (if it is not yet peristent) and save it to the storage.
     */
    function storeObject( &$obj ) 
	{
        if ( $obj->__oid__ == null ) 
		{ 
            $obj->__oid__ = dybase_allocate( $this->db );
            $obj->__storage__ = &$this;
            $this->objByOidMap[$obj->__oid__] = &$obj;
        }
		
        $obj->__dirty__ = false;
        $hnd = dybase_beginstore( $this->db, $obj->__oid__, get_class( $obj ) );
		
        if ( get_class( $obj ) == "DybaseIndex" ) 
		{
            dybase_storereffield( $hnd, "index", $obj->index );
        } 
		else 
		{ 
            foreach ( get_object_vars( $obj ) as $field => $value ) 
			{
                if ( $field[strlen( $field ) - 1] == '_' )
                    continue;
                
                if ( is_int( $value ) || is_float( $value ) || is_string( $value ) ) 
				{ 
                    dybase_storefield( $hnd, $field, $value );
                } 
				else if ( is_array( $value ) ) 
				{  
                    $arr = &$obj->$field;
                    dybase_storearrayfield( $hnd, $field, sizeof( $arr ) );
					
                    for ( $i = 0; $i < sizeof( $arr ); $i++ ) 
                        $this->_storeElement( $hnd, $arr[i] );
                } 
				else if ( $value == null ) 
				{ 
                    dybase_storereffield( $hnd, $field, 0 );
                } 
				else 
				{ 
                    if ( $value->__oid__ == null ) 
                        $this->storeObject( $obj->$field );
                    
                    dybase_storereffield( $hnd, $field, $obj->$field->__oid__ );
                }
            }
        }
		
        dybase_endstore( $hnd );
    }

    /**
     * Create index for keys of string type.
     */
    function &createStrIndex( $unique = true ) 
	{
        $idx = &new DybaseIndex();
        $idx->index = dybase_createstrindex( $this->db, $unique );
        $this->storeObject( $idx );

        return $idx;
    }

    /**
     * Create index for keys of int type.
     */
    function &createIntIndex( $unique = true ) 
	{
        $idx = &new DybaseIndex();
        $idx->index = dybase_createintindex( $this->db, $unique );
        $this->storeObject( $idx );

        return $idx;
    }

    /**
     * Create index for keys of boolean type.
     */
    function &createBoolIndex( $unique = true ) 
	{
        $idx = &new Index();
        $idx->index = dybase_createboolindex( $this->db, $unique );
        $this->storeObject( $idx );

        return $idx;
    }

    /**
     * Create index for keys of float type.
     */
    function &createRealIndex( $unique = true ) 
	{
        $idx = &new Index();
        $idx->index = dybase_createlrealindex( $this->db, $unique );
        $this->storeObject( $idx );

        return $idx;
    }

    /**
     * Resolve references in raw object.
     */
    function loadObject( &$obj ) 
	{
        $obj->__raw__ = false;
        
		foreach ( get_obj_vars( $obj ) as $field => $value ) 
		{
            if ( get_class( $value ) == "Persistent" )
                $obj->$field = &$this->_lookupObject( $value->__oid__, false );
            else if ( is_array( $value ) ) 
                $this->_loadArray( $obj->$field );
        }
		
        $obj->onLoad();
    }

    /**
     * Reset object hash. Each fetched object is stored in objByOidMap hash table.
     * It is needed to provide OID->instance mapping. Since placing object in hash increase its access counter, 
     * such object can not be deallocated by garbage collector. So after some time all peristent objects from 
     * the storage will be loaded to the memory. To solve the problem almost all languages with implicit
     * memory deallocation (garbage collection) provides weak references. But no PHP.
     * So to prevent memory overflow you should use resetHash() method. 
     * This method just clear hash table. After invocation of this method, you should not use any variable
     * referening persistent objects. Instead you should invoke getRootObject method and access all other 
     * persistent objects only through the root. 
     */
    function resetHash() 
	{ 
        unset( $this->objByOidMap );
    }        

    /**
     * Start garbage collection
     */
    function gc()
	{
        dybase_gc( $this->db );
    }

    /**
     * Set garbage collection threshold.
     * By default garbage collection is disable (threshold is set to 0).
     * If it is set to non zero value, GC will be started each time when
     * delta between total size of allocated and deallocated objects exeeds specified threshold OR
     * after reaching end of allocation bitmap in allocator. 
     * @param allocated_delta delta between total size of allocated and deallocated object since last GC 
     * or storage openning 
     */
    function setGcThreshold( $threshold ) 
	{
        dybase_setgcthreshold( $this->db, $threshold );
    }
     
	 
	// private methods
	   
	function _loadArray( &$arr ) 
	{
        for ( $i = 0; $i < sizeof( $arr ); $i++ ) 
		{ 
            $value = &$arr[$i];
            
			if ( get_class( $value ) == "Persistent" )
                $arr[$i] = &$this->_lookupObject( $value->__oid__, false );
            else if ( is_array( $value ) )
                $this->_loadArray( $value );
        }
    }

    function &_lookupObject( $oid, $recursive = true ) 
	{
        if ( $oid == 0 ) 
            return null;
        
        $obj = &$this->objByOidMap[$oid];

        if ( $obj == null ) 
		{ 
            $hnd = dybase_beginload( $this->db, $oid );
            $className = dybase_getclassname( $hnd );
            $obj = @new $className();
            $obj->__oid__ = $oid;

            if ( !$recursive ) 
			{ 
                if ( $obj->recursiveLoading() ) 
                    $recursive = true;
                else 
                    $obj->__raw__ = true;
            }
			
            $this->objByOidMap[$oid] = &$obj;          
            $obj->__storage__ = &$this;
			
            if ( get_class( $obj ) == "DybaseIndex" ) 
			{
                dybase_nextfield( $hnd );
                $obj->index = dybase_getref( $hnd );
                dybase_nextfield( $hnd );
            } 
			else 
			{       
                while ( true ) 
				{ 
                    $fieldName = dybase_nextfield( $hnd );
					
                    if ( $fieldName == null )
                         break;
                    
                    $oid = dybase_getref( $hnd );
					
                    if ( $oid == null ) 
					{                         
                        $len = dybase_arraylength( $hnd );
						
                        if ( $len != null )
                            $obj->$fieldName = &$this->_fetchArray( $hnd, $len, $recursive );
                        else 
                            $obj->$fieldName = dybase_getvalue( $hnd );
                    } 
					else if ( $oid == 0 ) 
					{
                        $obj->$fieldName = null;
                    } 
					else if ( $recursive ) 
					{ 
                        $obj->$fieldName = &$this->_lookupObject( $oid, false );
                    } 
					else 
					{
                        $stub = &$this->objByOidMap[$oid];
                        
						if ( $stub == null ) 
                            $stub = &new Persistent( $oid );

                        $obj->$fieldName = &$stub;
                    }
                }
				
                if ( $recursive )
                    $obj->onLoad();
            }
        } 
		else 
		{ 
            if ( $recursive && $obj->__raw__ )
                $this->loadObject( $obj );
        }
		
        return $obj;
    }

    function &_fetchArray( $hnd, $len, $recursive ) 
	{
        $arr = array();
	
		for ( $i = 0; $i < $len; $i++ ) 
		{ 
         	dybase_nextelem( $hnd );
           	$oid = dybase_getref( $hnd );
           
		   	if ( $oid == null ) 
			{ 
               	$len = dybase_arraylength( $hnd );
               
			   	if ( $len != null ) 
                   	$arr[] = &$this->_fetchArray( $hnd, $len, $recursive );
               	else
                   	$arr[] = dybase_getvalue( $hnd );
           	} 
			else if ( $recursive ) 
			{ 
               	$arr[] = &$this->_lookupObject( $oid, false );
           	} 
			else 
			{
               	$stub = &$this->objByOidMap[$oid];
               
			   	if ( $stub == null ) 
					$stub = &new Persistent( $oid );
               
				$arr[] = &$stub;
			}
		}       
		
		return $arr;
    }

    function _storeElement( $hnd, &$elem ) 
	{
        if ( is_int( $elem ) || is_float( $elem ) || is_string( $elem ) ) 
		{
             dybase_storeelem( $hnd, $elem );
        } 
		else if ( is_array( $elem ) ) 
		{ 
             dybase_storearrayelem( $hnd, $elem );
			 
             for ( $i = 0; $i < sizeof( $elem ); $i++ )
                 $this->_storeElement( $hnd, $elem[$i] );
        } 
		else if ( $elem == null ) 
		{
             dybase_storerefelem( $hnd, 0 );
        } 
		else 
		{ 
             if ( $elem->__oid__ == null )
                 $this->storeObject( $elem );
             
             dybase_storerefelem( $hnd, $elem->__oid__ );
        }
    }
} // END OF DybaseStorage

?>
