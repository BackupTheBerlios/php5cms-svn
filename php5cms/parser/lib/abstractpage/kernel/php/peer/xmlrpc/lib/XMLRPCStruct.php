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


using( 'peer.xmlrpc.lib.XMLRPCArray' );


/**
 * Handles encoding and decoding of an XML-RPC struct datatype.
 *
 * @package peer_xmlrpc_lib
 */

class XMLRPCStruct extends PEAR
{
	/**
	 * struct value
	 * @access public
	 */
    var $Struct;
	

	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function XMLRPCStruct( $struct = array() )
    {
		$this->Struct = $struct;
    }

	
	/**
	 * This function will encode the sting into a valid XML-RPC value.
	 *
	 * @access public
	 */
    function &serialize( )
    {
		$ret .= $this->serializeStruct( $this->Struct );        
		return $ret;
    }

	/**
	 * Returns the struct value.
	 *
	 * @access public
	 */
    function value()
    {
        return $this->Struct;
    }

	/**
	 * Decodes the value.
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
    function serializeStruct( $struct )
    {
        $ret .= "<value><struct>";

        reset( $struct );
        
        while ( list( $key, $value ) = each( $struct ) )
        {
            $ret .= "<member><name>" . ${key} . "</name>";

			switch ( gettype($value) )
			{
				case "integer":
					$ret .= "<value><int>$value</int></value>";
					break;
                
				case "object":
					if ( substr( get_class( $value ), 0, 6 ) == "xmlrpc" )
						$ret .= $value->serialize( $value );
				
					break;
                 
                case "array":
					$ret .= XMLRPCArray::serializeArray( $value );
					break;                
                
				default:
					$ret .= "<value><string>$value</string></value>";
					break;
            }
            
            $ret .= "</member>";
        }
        
        $ret .= "</struct></value>";
        return $ret;
    }
} // END OF XMLRPCStruct

?>
