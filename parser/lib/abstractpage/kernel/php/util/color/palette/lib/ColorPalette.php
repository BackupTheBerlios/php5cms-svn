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
 * @package util_color_palette_lib
 */
 
class ColorPalette extends PEAR
{
	/**
	 * Name of palette
	 *
	 * @access private
	 */
	var $_name = "";
	
	/**
	 * Color data
	 *
	 * @access private
	 */
	var $_palette = array();
	
	/**
	 * @access private
	 */
	var $_params = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ColorPalette( $name = "", $params = array() )
	{
		$this->_name   = $name;
		$this->_params = $params;
	}
	
	
    /**
     * Attempts to return a concrete ColorPalette instance based on
     * $driver.
     *
     * @param mixed $driver  The type of concrete ColorPalette subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object ColorPalette  The newly created concrete ColorPalette instance,
     *                       or false on an error.
     */
    function &factory( $driver, $params = array() )
    {
        $driver = strtolower( $driver );
        $palette_class = "ColorPalette_" . $driver;

		using( 'util.color.palette.lib.' . $palette_class );
		
		if ( class_registered( $palette_class ) )
	        return new $palette_class( $params );
		else
			return PEAR::raiseError( "Driver not supported." );
    }

    /**
     * Attempts to return a reference to a concrete ColorPalette
     * instance based on $driver. It will only create a new instance
     * if no ColorPalette instance with the same parameters currently
     * exists.
     *
     * This should be used if multiple types of image renderers (and,
     * thus, multiple ColorPalette instances) are required.
     *
     * This method must be invoked as: $var = &ColorPalette::singleton()
     *
     * @param mixed $driver  The type of concrete ColorPalette subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object ColorPalette  The concrete ColorPalette reference, or false
     *                       on an error.
     */
    function &singleton( $driver, $params = array() )
    {
        static $instances;
        
		if ( !isset( $instances ) )
            $instances = array();

        $signature = serialize( array( $driver, $params ) );

        if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &ColorPalette::factory( $driver, $params );

        return $instances[$signature];
    }
	
	/**
	 * @access public
	 */
	function getPalette()
	{
		return $this->_palette;
	}
	
	/**
	 * Get name of palette.
	 *
	 * @access public
	 */
	function getName()
	{
		return $this->_name;
	}
} // END OF ColorPalette

?>
