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


using( 'util.Util' );


/**
 * @package security_checksum_lib
 */
 
class Checksum extends PEAR
{
	/**
	 * @access public
	 */
    var $value = '';
    

    /**
     * Constructor
     *
     * @access  public
     * @param   mixed value
     */
    function Checksum( $value ) 
	{
      	$this->value = $value;
    }

  	
    /**
     * Attempts to return a concrete Checksum instance based on
     * $driver.
     *
     * @access public
     *
     * @param mixed $driver           The type of concrete Checksum
     *                                subclass to return. This is based on
     *                                the storage driver ($driver). The code
     *                                is dynamically included.
     * @param optional string $value  String containing value
     * @return object Checksum        The newly created concrete
     *                                Checksum instance, or false on an
     *                                error.
     */
    function &factory( $driver, $value = '' )
    {
        $driver = strtolower( $driver );
		
        if ( empty( $driver ) || ( strcmp( $driver, 'none' ) == 0 ) )
            return new Checksum( $value );
	
        $checksum_class = "Checksum_" . $driver;

		using( 'security.checksum.lib.' . $checksum_class );
		
		if ( class_registered( $checksum_class ) )
	        return new $checksum_class( $value );
		else
			return PEAR::raiseError( 'Driver not supported.' );
    }

    /**
     * Attempts to return a reference to a concrete Checksum instance
     * based on $driver. It will only create a new instance if no
     * Checksum instance with the same parameters currently exists.
     *
     * This method must be invoked as:
     *   $var = &Checksum::singleton();
     *
     * @access public
     *
     * @param mixed $driver           See Checksum::factory().
     * @param optional string $value  See Checksum::factory().
     * @return object Checksum        The concrete Checksum reference,
     *                                or false on an error.
     */
    function &singleton( $driver, $value = '' )
    {
        static $instances;

        if ( !isset( $instances ) )
            $instances = array();

        $signature = serialize( array( $driver, $value ) );
        
		if ( !array_key_exists( $signature, $instances ) )
            $instances[$signature] = &Checksum::factory( $driver, $value );

        return $instances[$signature];
    }
	
    /**
     * Create a new checksum from a string. Override this
     * method in child classes!
     *
     * @access  abstract
     * @param   string str
     */
    function &fromString( $str ) 
	{
		return PEAR::raiseError( 'Abstract method.' );
	}

    /**
     * Create a new checksum from a file. Override this
     * method in child classes!
     *
     * @access  abstract
     * @param   file
     */
    function &fromFile( $file ) 
	{ 
		return PEAR::raiseError( 'Abstract method.' );
	}
    
    /**
     * Retrieve the checksum's value
     *
     * @access  public
     * @return  mixed value
     */
    function getValue() 
	{
      	return $this->value;
    }
  
    /**
     * Verify this checksum against another checksum
     *
     * @access  public
     * @param   Checksum object
     * @return  bool true if these checksums match
     */
    function verify( &$sum ) 
	{
      	return $this->value === $sum->value;
    }
	
	/**
	 * Check if mhash extension is available.
	 *
	 * @static 
	 */
	function useMHash()
	{
		if ( Util::extensionExists( "mhash" ) )
			return true;
		else 
			return false;
	}
	
	
	// private methods
	
	/**
	 * Get file data.
	 *
	 * @access private
	 * @param  string filename
	 * @return string or empty string on error
	 */
	function _getFile( $inFile )
	{
		if ( file_exists( $inFile ) ) 
		{
			$fd = fopen( $inFile, 'r' );
			$fileContents = fread( $fd, filesize( $inFile ) );
			fclose( $fd );

			return $fileContents;
		} 
		else 
		{
			return "";
		}
	}
} // END OF Checksum

?>
