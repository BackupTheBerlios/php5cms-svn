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

 
using( 'util.registry.lib.RegistryStorage' );
  
  
/**
 * Memory storage
 *
 * @package util_registry
 */

class RegistryStorage_memory extends RegistryStorage
{
	/**
	 * @access public
	 */
    var $segments = array();
    
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	function RegistryStorage_memory( $options = array() )
	{
		$this->RegistryStorage( $options );
	}
	
	
    /**
     * Returns whether this storage contains the given key.
     *
     * @access  public
     * @param   string key
     * @return  bool true when this key exists
     */
    function contains( $key ) 
	{
     	return isset( $this->segments[$key] );
    }

    /**
     * Get all keys.
     *
     * @access  public
     * @return  string[] key
     */
    function keys()
	{ 
      	return array_keys( $this->segments );
    }
    
    /**
     * Get a key by it's name.
     *
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    function &get( $key ) 
	{
      	if ( !isset( $this->segments[$key] ) )
        	return PEAR::raiseError( $key . ' does not exist.' );
      
      	return $this->segments[$key];
    }

    /**
     * Insert/update a key.
     *
     * @access  public 
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666 (ignored)
     */
    function put( $key, &$value, $permissions = 0666 ) 
	{
      	$this->segments[$key] = &$value;
    }

    /**
     * Remove a key.
     *
     * @access  public
     * @param   string key
     */
    function remove( $key ) 
	{
      	unset( $this->segments[$key] );
    }
  
    /**
     * Remove all keys.
     *
     * @access  public
     */
    function free()
	{ 
      	$this->segments = array();
    }
} // END OF RegistryStorage_memory

?>
