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
 * Registry is basically a key/value-storage system. This class actually
 * acts as a proxy to the storage providers.
 *
 * @package util_registry
 */

class Registry extends PEAR
{
	/**
	 * @access public
	 */
    var $storage = null;
	
	
    /**
     * Return whether a given key exists.
     *
     * @access  public
     * @param   string key
     * @return  bool true when the key exists
     */
    function contains( $key ) 
	{
      	return $this->storage->contains( $key );
    }

    /**
     * Return all registered keys.
     *
     * @access  public
     * @return  string[] key
     */
    function keys()
	{
      	return $this->storage->keys();
    }
    
    /**
     * Retrieve a value by a given key.
     *
     * @access  public
     * @param   string key
     * @return  &mixed value
     */
    function &get( $key ) 
	{
      	return $this->storage->get( $key );
    }

    /**
     * Insert or update a key/value-pair.
     *
     * @access  public
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666
     * @return  bool success
     */
    function put( $key, &$value, $permissions = 0666 ) 
	{
      	return $this->storage->put( $key, $value, $permissions );
    }

    /**
     * Remove a value by a given key.
     *
     * @access  public
     * @param   string key
     * @return  bool success
     */
    function remove( $key ) 
	{
      	return $this->storage->remove( $key );
    }

    /**
     * Get an instance.
     * 
     * @access  static
     * @param   mixed a string or a RegistryStorage object
     * @return  &Registry registry object
     * @throws  Error
     */
    function &getInstance()
	{
      	static $__instance = array();
      
      	$p = &func_get_arg( 0 );
      
      	// Subsequent calls
      	if ( is_string( $p ) ) 
		{
        	if ( !isset( $__instance[$p] ) ) 
				return PEAR::raiseError( 'Registry "' . $p . '" hasn\'t been setup yet.' );

			return $__instance[$p];
      	}
      
      	// Initial setup
      	if ( is_a( $p, 'RegistryStorage' ) ) 
		{
        	$__instance[$p->id] = new Registry();
        	$__instance[$p->id]->storage = &$p;
        	$__instance[$p->id]->storage->initialize();
        
        	return $__instance[$p->id];
      	}
      
      	trigger_error( 'Type: ' . gettype( $p ), E_USER_WARNING );
		return PEAR::raiseError( 'Argument passed is of wrong type.' );
    }
} // END OF Registry

?>
