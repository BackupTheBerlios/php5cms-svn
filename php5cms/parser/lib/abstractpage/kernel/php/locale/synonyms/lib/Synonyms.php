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
 * @package locale_synonyms_lib
 */
 
class Synonyms extends PEAR
{
	/**
	 * @access public
	 */
	var $synonyms = array();


    /**
     * Attempts to return a concrete Synonyms instance based on
     * $driver.
     *
     * @param mixed $driver  The type of concrete Synonyms subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Synonyms  The newly created concrete Synonyms instance,
     *                       or false on an error.
     */
    function &factory( $driver, $params = array() )
    {
        $driver = strtolower( $driver );
        $synonyms_class = "Synonyms_" . $driver;

		using( 'locale.synonyms.lib.' . $synonyms_class );
		
		if ( class_registered( $synonyms_class ) )
	        return new $synonyms_class( $params );
		else
			return PEAR::raiseError( "Driver not supported." );
    }

    /**
     * Attempts to return a reference to a concrete Synonyms
     * instance based on $driver. It will only create a new instance
     * if no Synonyms instance with the same parameters currently
     * exists.
     *
     * This should be used if multiple types of image renderers (and,
     * thus, multiple Synonyms instances) are required.
     *
     * This method must be invoked as: $var = &Synonyms::singleton()
     *
     * @param mixed $driver  The type of concrete Synonyms subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Synonyms  The concrete Synonyms reference, or false
     *                       on an error.
     */
    function &singleton( $driver, $params = array() )
    {
        static $instances;
        
		if ( !isset( $instances ) )
            $instances = array();

        $signature = serialize( array( $driver, $params ) );

        if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &Synonyms::factory( $driver, $params );

        return $instances[$signature];
    }
	
	/**
	 * @access public
	 */
	function getAll()
	{
		return $this->synonyms;
	}
} // END OF Synonyms

?>
