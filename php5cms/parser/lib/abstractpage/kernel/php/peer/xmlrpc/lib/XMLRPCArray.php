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
 * Handles encoding and decoding of an XML-RPC array datatype.
 *
 * @package peer_xmlrpc_lib
 */

class XMLRPCArray extends PEAR
{
    /**
	 * array value
	 * @access public
	 */
    var $Array;
	

	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function XMLRPCArray( $array = array() )
    {
        $this->Array = $array;
    }

	
	/**
	 * This function will encode the sting into a valid XML-RPC value.
	 *
	 * @access public
	 */
    function &serialize( )
    {
        $ret .= $this->serializeArray( $this->Array );        
        return $ret;
    }

	/**
	 * Returns the array value.
	 *
	 * @access public
	 */
    function value()
    {
        return $this->Array;
    }

	/**
	 * Decodes the a value.
	 *
	 * @access public
	 */
    function decode( $value )
    {  
    }

	
	// private methods

	/**
	 * @access private
	 */
    function serializeArray( $array )
    {
        $ret .= "<value><array><data>";
        foreach ( $array as $value )
        {
            switch( gettype($value) )
            {
                case "integer":
					$ret .= "<value><int>$value</int></value>";
                	break;
                
                case "array":
					$ret .= XMLRPCArray::serializeArray( $value );
					break;
                
                case "object":
					if ( substr( get_class( $value ), 0, 6 ) == "xmlrpc" )
   						$ret .= $value->serialize( $value );
					
					break;
                    
                default:
					$ret .= "<value><string>$value</string></value>";
					break;
            }
        }

        $ret .= "</data></array></value>";
        return $ret;
    }
} // END OF XMLRPCArray

?>
