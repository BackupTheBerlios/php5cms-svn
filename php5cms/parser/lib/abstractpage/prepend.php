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


if ( !defined( "AP_ROOT_PATH" ) )
	define( "AP_ROOT_PATH", dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
	
if ( !defined( "AP_CONFIG_PATH" ) )
	define( "AP_CONFIG_PATH", AP_ROOT_PATH . "config" . DIRECTORY_SEPARATOR );

if ( !defined( "AP_PACKAGES_PATH" ) )
	define( "AP_PACKAGES_PATH", AP_ROOT_PATH . "kernel" . DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR );

	
require_once 'PEAR.php';
require_once AP_PACKAGES_PATH . 'ClassLoader.php';

include_once AP_CONFIG_PATH . 'basic.php';

extract($_REQUEST);


/**
 * emulate stdClass for PHP 5 
 */
if ( !class_exists( 'stdClass' ) ) 
{
    class stdClass
	{
        function __construct()
		{
		}
    }
}

/**
 * Because is_executable() doesn't exist on windows until php 5.0 we define it as a dummy
 * function here that just runs file_exists.
 *
 * @param string $in_filename The filename to test
 *
 * @access public
 * @return bool If the file exists 
 */
if ( !function_exists( 'is_executable' ) ) 
{
    function is_executable( $in_filename )
    {
        return file_exists( $in_filename );
    }
}


/**
 * Simple wrapper around the basic configuration array.
 *
 * @param  string  $key
 * @param  string  $section
 * @access public
 */
function ap_ini_get( $key, $section = "settings" )
{
	return ( isset( $GLOBALS['AP_BASIC_CONFIGURATION'][$section][$key] ) )? 
		$GLOBALS['AP_BASIC_CONFIGURATION'][$section][$key] : 
		null;
}

/**
 * Get framework version.
 *
 * @return string
 * @access public
 */
function ap_version()
{
	return "0.9";
}
 
?>
