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
 * The Secret class provides an API for encrypting and decrypting
 * small pieces of data with the use of a shared key.
 *
 * @package security
 */

class Secret extends PEAR
{
    /**
     * Generate a secret key (for encryption), either using a random
     * md5 string and storing it in a cookie if the user has cookies
     * enabled, or munging some known values if they don't.
     *
     * @access public
     *
     * @param optional string $keyname  The name of the key to set.
     *
     * @return string  The secret key that has been generated.
     */
    function setKey( $keyname = 'generic' )
    {
        if ( isset( $_COOKIE ) && array_key_exists( 'AbstractPage', $_COOKIE ) ) 
		{
            if ( array_key_exists( $keyname . '_key', $_COOKIE ) ) 
			{
                $key = $_COOKIE[$keyname . '_key'];
            } 
			else 
			{
                Secret::_srand();
                $key = md5( uniqid( mt_rand() ) );
                $_COOKIE[$keyname . '_key'] = $key;
                setcookie( $keyname . '_key', $key, null, '/abstractpage', $_SERVER['SERVER_NAME'] );
            }
        } 
		else 
		{
            $key = md5( session_id() . $_SERVER['SERVER_NAME'] );
        }

        return $key;
    }

    /**
     * Return a secret key, either from a cookie, or if the cookie
     * isn't there, assume we are using a munged version of a known
     * base value.
     *
     * @access public
     *
     * @param optional string $keyname  The name of the key to get.
     *
     * @return string  The secret key.
     */
    function getKey( $keyname = 'generic' )
    {
        static $keycache;

        if ( is_null( $keycache ) )
            $keycache = array();

        if ( !array_key_exists( $keyname, $keycache ) ) 
		{
            if ( array_key_exists( $keyname . '_key', $_COOKIE ) ) 
			{
                $keycache[$keyname] = $_COOKIE[$keyname . '_key'];
            } 
			else 
			{
                $keycache[$keyname] = md5( session_id() . $_SERVER['SERVER_NAME'] );
            }
        }

        return $keycache[$keyname];
    }

	
	// private methods
	
    /**
     * Ensure that the random number generator is initialized only once.
     *
     * @access private
     */
    function _srand()
    {
        static $initialized;

        if ( empty( $initialized ) ) 
		{
            mt_srand( (double)microtime() * 1000000 );
            $initialized = true;
        }
    }
} // END OF Secret

?>
