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
 * @package  util_text_encoding_data_lib
 */
 
class Encode extends PEAR
{
	/**
	 * @var array
	 * @access private
	 */
	var $_params = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encode( $params = array() )
	{
		$this->_params = $params;
	}
	
	
    /**
     * Attempts to return a concrete Encode instance based on
     * $driver.
     *
     * @param mixed $driver  The type of concrete Encode subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Encode The newly created concrete Encode instance,
     *                       or false on an error.
     */
    function &factory( $driver, $params = array() )
    {
        $driver = strtolower( $driver );
        $encode_class = "Encode_" . $driver;

		using( 'util.text.encoding.data.lib.' . $encode_class );
		
		if ( class_registered( $encode_class ) )
	        return new $encode_class( $params );
		else
			return PEAR::raiseError( "Driver not supported." );
    }

    /**
     * Attempts to return a reference to a concrete Encode
     * instance based on $driver. It will only create a new instance
     * if no Encode instance with the same parameters currently
     * exists.
     *
     * This should be used if multiple types of image renderers (and,
     * thus, multiple Encode instances) are required.
     *
     * This method must be invoked as: $var = &Encode::singleton()
     *
     * @param mixed $driver  The type of concrete Encode subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Encode The concrete Encode reference, or false
     *                       on an error.
     */
    function &singleton( $driver, $params = array() )
    {
        static $instances;
        
		if ( !isset( $instances ) )
            $instances = array();

        $signature = serialize( array( $driver, $params ) );

        if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &Encode::factory( $driver, $params );

        return $instances[$signature];
    }
	
	/**
	 * @abstract
	 */
	function encode()
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	/**
	 * @abstract
	 */
	function decode()
	{
		return PEAR::raiseError( "Abstract method." );
	}
} // END OF Encode

?>
