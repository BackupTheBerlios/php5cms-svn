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


using( 'xml.cbl.lib.CBLDocument' );


/**
 * Package to create CBL 2.0 documents.
 *
 * To create a new document you have to call
 * createDocument() statically:
 *
 * <code>
 * $doc = &CBL::createDocument( 'myXUL.xml' );
 * </code>
 *
 * The document object provides methods to create and
 * add any element you like:
 *
 * <code>
 * $cbl = &$doc->createElement( 'cbl', array( 'key'=> '12a4-15tz-uz8i-tr56' ) );
 * $doc->addRoot( $cbl );
 * </code>
 *
 * @static
 * @package xml_cbl_lib
 */

class CBL
{
	/**
	 * Return API version.
	 *
	 * @access   public
	 * @static
	 * @return   string  $version API version
	 */
	function apiVersion()
    {
        return "0.1";
    }

	/**
	 * Create a CBL document.
	 *
	 * @access   public
	 * @param    string  filename
	 * @param    string  namespace for XUL elements
	 */
    function &createDocument( $filename = null, $ns = null )
    {
        $doc = &new CBLDocument( $filename, $ns );
        return $doc;
    }
} // END OF CBL

?>
