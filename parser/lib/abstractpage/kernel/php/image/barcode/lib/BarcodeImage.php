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
 * The BarcodeImage class provides method to create barcode using GD library.
 *
 * @package image_barcode_lib
 */
 
class BarcodeImage extends PEAR
{
	/**
	 * Array of params
	 *
	 * @var    array
	 * @access private
	 */
	var $_params = array();
	
	/**
	 * Output format
	 *
	 * @var    array
	 * @access private
	 */
	var $_format = "png";
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function BarcodeImage( $params = array() )
	{
		$this->_params = $params;
		
		if ( !empty( $params['format'] ) )
            $this->_format = $params['format'];
	}
	
	
    /**
     * Attempts to return a concrete BarcodeImage instance based on
     * $driver.
     *
     * @param mixed $driver  The type of concrete BarcodeImage subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object BarcodeImage  The newly created concrete BarcodeImage instance,
     *                       or false on an error.
     */
    function &factory( $driver, $params = array() )
    {
        $driver = strtolower( $driver );
        $image_class = "BarcodeImage_" . $driver;
		
		using( 'image.barcode.lib.' . $image_class );

		if ( class_registered( $image_class ) )
	        return new $image_class( $params );
		else
			return PEAR::raiseError( "Driver not supported." );
    }

    /**
     * Attempts to return a reference to a concrete BarcodeImage
     * instance based on $driver. It will only create a new instance
     * if no BarcodeImage instance with the same parameters currently
     * exists.
     *
     * This should be used if multiple types of image renderers (and,
     * thus, multiple BarcodeImage instances) are required.
     *
     * This method must be invoked as: $var = &BarcodeImage::singleton()
     *
     * @param mixed $driver  The type of concrete BarcodeImage subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included. 
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object BarcodeImage  The concrete BarcodeImage reference, or false
     *                       on an error.
     */
    function &singleton( $driver, $params = array() )
    {
        static $instances;
        
		if ( !isset( $instances ) )
            $instances = array();

        $signature = serialize( array( $driver, $params ) );

        if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &BarcodeImage::factory( $driver, $params );

        return $instances[$signature];
    }
	
	/**
	 * @abstract
	 */
	function draw( $text = "" )
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	
	// private methods
	
	/**
	 * Output image.
	 *
	 * @access private
	 */
	function _send( &$img )
	{
		// TODO: better error handling
		
        switch ( $this->_format )
		{
            case 'gif':
                header( "Content-type: image/gif" );
                imagegif( $img );
                imagedestroy( $img );
				
				break;

            case 'jpg':
                header( "Content-type: image/jpg" );
                imagejpeg( $img );
                imagedestroy( $img );
				
            	break;

            default:
                header( "Content-type: image/png" );
                imagepng( $img );
                imagedestroy( $img );
            	
				break;
        }
	}
} // END OF BarcodeImage

?>
