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
 * RegistryStorage
 *
 * @package util_registry
 */

class RegistryStorage extends PEAR
{
	/**
	 * @access public
	 */
    var $id = '';
     
	/**
	 * @access private
	 */
	var $_options = array();
	
	 
    /**
     * Constructor
     * 
     * @access  public
     * @param   string id
     */
    function RegistryStorage( $options = array() ) 
	{
		$this->_options = $options;
		
		if ( isset( $options['id'] ) )
			$this->id = $options['id'];
			
		$this->initialize();
    }

    
    /**
     * Attempts to return a concrete RegistryStorage instance based on $driver.
     *
     * @param mixed $driver  The type of concrete RegistryStorage subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object RegistryStorage  The newly created concrete RegistryStorage instance,
     *                       or false on an error.
     */
    function &factory( $driver, $options = array() )
    {	
        $driver = strtolower( $driver );
		
        $reg_storage_class = "RegistryStorage_" . $driver;

		using( 'util.registry.lib.' . $reg_storage_class );
		
		if ( class_registered( $reg_storage_class ) )
	        return new $reg_storage_class( $options );
		else
			return PEAR::raiseError( 'Driver not supported.' );
    }
	
    /**
     * Initialize this storage.
     *
     * @model   abstract
     * @access  public
     */
    function initialize()
	{
		return true;
	}
    
    /**
     * Returns whether this storage contains the given key.
     *
     * @model   abstract
     * @access  public
     * @param   string key
     * @return  bool true when this key exists
     */
    function contains( $key ) 
	{
		return PEAR::raiseError( "Abstract method." );
	}
    
    /**
     * Get a key by it's name.
     *
     * @model   abstract
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    function &get( $key ) 
	{ 
		return PEAR::raiseError( "Abstract method." );
	}

    /**
     * Insert/update a key.
     *
     * @model   abstract
     * @access  public 
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666
     */
    function put( $key, &$value, $permissions = 0666 )
	{ 
		return PEAR::raiseError( "Abstract method." );
	}

    /**
     * Remove a key.
     *
     * @model   abstract
     * @access  public
     * @param   string key
     */
    function remove( $key ) 
	{ 
		return PEAR::raiseError( "Abstract method." );
	}

    /**
     * Remove all keys.
     *
     * @model   abstract
     * @access  public
     */
    function free()
	{
		return PEAR::raiseError( "Abstract method." );
	}

    /**
     * Get all keys.
     *
     * @model   abstract
     * @access  public
     * @return  string[] key
     */
    function keys()
	{ 
		return PEAR::raiseError( "Abstract method." );
	}
} // END OF RegistryStorage

?>
