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
 * Unix crypt algorithm implementation. Note: There is no decrypt 
 * function, since crypt() uses a one-way algorithm.
 *
 * @see php://crypt
 * @package security_crypt
 */

class UnixCrypt
{  
    /**
     * Encrypt a string.
     *
     * @static
     * @access  public
     * @param   string original
     * @param   string salt default null
     * @return  string crypted
     */
    function crypt( $original, $salt = null )
	{
      	return crypt( $original, $salt );
    }
    
    /**
     * Check if an entered string matches the crypt.
     *
     * @static
     * @access  public
     * @param   string encrypted
     * @param   string entered
     * @return  bool
     */
    function matches( $encrypted, $entered ) 
	{
      	return ( $encrypted == crypt( $entered, $encrypted ) );
    }
} // END OF UnixCrypt

?>
