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
 * @package util_datetime_namedays_lib
 */
 
class Date_Namedays extends PEAR
{
	/**
	 * Array of Namedays
	 *
	 * @var    array
	 * @access private
	 */
	var $_namedays = array();
	
	/**
	 * Array of parameters passed to Constructor
	 *
	 * @var    array
	 * @access private
	 */
	var $_params = array();
	

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Date_Namedays( $params = array() )
	{
		$this->_params = $params;
	}
	
	
    /**
     * Attempts to return a concrete Date_Namedays instance based on $driver.
     *
     * @param mixed $driver  The type of concrete Date_Namedays subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Date_Namedays The newly created concrete Date_Namedays instance, or
     *                      false an error.
     * @access public
     */
    function &factory( $driver, $params = array() )
    {
        // TODO: be a little more precise, maybe check if driver is valid two (or four) letter code
        $driver = strtolower( $driver );
     
        if ( empty( $driver ) || ( strcmp( $driver, 'none' ) == 0 ) )
            return PEAR::raiseError( "No driver specified." );
    
        $namedays_class = "Date_Namedays_" . $driver;

        using( 'util.datetime.namedays.lib.' . $namedays_class );
        
        if ( class_exists( $namedays_class ) )
            return new $namedays_class( $params );
        else
            return PEAR::raiseError( "Driver not implemented." );
    }
	
    /**
     * Attempts to return a reference to a concrete Date_Namedays instance
     * based on $driver. It will only create a new instance if no
     * Date_Namedays instance with the same parameters currently exists.
     *
     * This method must be invoked as: $var = &Date_Namedays::singleton()
     *
     * @param mixed $driver  The type of concrete Date_Namedays subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Date_Namedays  The concrete Date_Namedays reference, or false on an
     *                       error.
     * @access public
     */
    function &singleton( $driver, $params = array() )
    {
        static $instances;
        
        if ( !isset( $instances ) )
            $instances = array();

        if ( is_array( $driver ) )
            $drivertag = implode( ':', $driver );
        else
            $drivertag = $driver;
        
        $signature = md5( strtolower( $drivertag ) . '][' . implode( '][', $params ) );

        if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &Date_Namedays::factory( $driver, $params );

        return $instances[$signature];
    }
	
	/**
	 * Returns name if date is a nameday.
	 *
	 * @return mixed String or Array of Strings if date is nameday, otherwise empty string
	 */
	function getNameday( $date )
	{
		// TODO
	}
	
	/**
	 * Returns if date is a nameday.
	 * (I'm not sure if this is a useful function - anyway...)
	 *
	 * @return bool
	 */
	function isNameday( $date )
	{
		// TODO
	}

	/**
	 * TODO
	 */	
	function getNamedays( $month = null )
	{
		if ( !$month )
			return PEAR::raiseError( "No month argument given." );
	}
	
	/** 
	 * Returns date if name has a nameday, otherwise false.
	 *
	 * @var string $name
	 */
	function hasNameday( $name, $normalize = false, $similar = false )
	{
	
	}
} // END OF Date_Namedays

?>
