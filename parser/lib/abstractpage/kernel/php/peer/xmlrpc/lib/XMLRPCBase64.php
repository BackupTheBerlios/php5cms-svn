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
 * Handles encoding and decoding of an XML-RPC base64 encoded binary.
 *
 * @package peer_xmlrpc_lib
 */

class XMLRPCBase64 extends PEAR
{
    /**
	 * string value
	 * @access public
	 */
    var $Value;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function XMLRPCBase64( $value = 0 )
    {
        if ( !isset( $value ) )
             $value = 0;
			 
        $this->Value = $value;
    }


	/**
	 * This function will encode the base64 into a valid XML-RPC value.
	 *
	 * @access public
	 */
    function &serialize()
    {        
        $value =& base64_encode( $this->Value );
        
        $ret  = "<value>";
        $ret .= "<base64>";
        $ret .= $value;
        $ret .= "</base64>";
        $ret .= "</value>";

        return $ret;             
    }
    
	/**
	 * Returns the string value.
	 *
	 * @access public
	 */
    function value()
    {
        return $this->Value;
    }

	/**
	 * Decodes the base64 encoded string and sets the internal value.
	 *
	 * @access public
	 */
    function decode( $value )
    {
        $this->Value =& base64_decode( $value );
    }
} // END OF XMLRPCBase64

?>
