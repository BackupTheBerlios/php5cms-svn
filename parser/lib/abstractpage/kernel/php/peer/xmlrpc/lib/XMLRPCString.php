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
 * Handles encoding and decoding of an XML-RPC string.
 *
 * @package peer_xmlrpc_lib
 */
 
class XMLRPCString extends PEAR
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
    function XMLRPCString( $value = "" )
    {
        $this->Value = $value;
    }

	
	/**
	 * This function will encode the sting into a valid XML-RPC value.
	 *
	 * @access public
	 */
    function &serialize()
	{
        $ret = "<value>";
        $ret .= "<string>";
        $ret .= $this->encode( $this->Value );
        $ret .= "</string>";
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
	 * Returns an encoded string. Where <, > and & are converted to &lt;, &gt; and &amp;
	 *
	 * @access public
	 */
    function &encode( $string )
    {
        $string =& ereg_replace( "&", "&amp;", $string );
        $string =& ereg_replace( "<", "&lt;",  $string );
        $string =& ereg_replace( ">", "&gt;",  $string );
        
        return $string;
    }

	/**
	 * Returns a string which is decoded. Opposite of encode().
	 *
	 * @access public
	 */
    function decode( $string )
    {
        $string =& ereg_replace( "&amp;", "&", $string );
        $string =& ereg_replace( "&lt;",  "<", $string );
        $string =& ereg_replace( "&gt;",  ">", $string );

        $this->Value =& $string;                
    }
} // END OF XMLRPCString

?>
