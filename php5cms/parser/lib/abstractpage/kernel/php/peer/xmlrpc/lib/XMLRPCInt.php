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
 * Handles both int and i4 as a four-byte signed integer.
 *
 * @package peer_xmlrpc_lib
 */

class XMLRPCInt extends PEAR
{
	/**
	 * int value
	 * @access public
	 */
    var $Value;
	
    
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function XMLRPCInt( $value = 0 )
    {
        if ( !is_numeric( $value ) )
            $value = 0;
        
        $this->Value = $value;
    }

	
	/**
	 * This function will encode the int into a valid XML-RPC value.
	 *
	 * @access public
	 */
    function &serialize()
    {
        $ret  = "<value>";
        $ret .= "<int>";
        $ret .= $this->Value;
        $ret .= "</int>";
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
} // END OF XMLRPCInt

?>
