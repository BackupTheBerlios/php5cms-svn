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


using( 'xml.cbl.lib.CBLElement' );
 

/**
 * @package xml_cbl_lib_elements
 */
 
class CBLElement_engine extends CBLElement
{
	/**
	 * Element name
	 *
	 * @access public
	 * @var    string
	 */
    var $elementName = 'engine';

	/**
	 * Attribute defintions
	 *
	 * @access private
	 * @var    array
	 */
	var $_attribDefs = array(
	);
	
	/**
	 * Allowed child elements
	 *
	 * @access private
	 * @var    array
	 */
	var $_childElements = array(
		'attrib'
	);
	
	
   /**
    * Add attrib.
    *
    * @access   public
    * @param    array   attributes of the Attrib
    * @return   object  CBLElement_attrib
    */
    function &addAttrib( $attrib = array() )
    {
        if( !is_object( $attrib ) )
            $attrib = &$this->_doc->createElement( 'attrib', $attrib );
        
        $this->appendChild( $attrib );
        return $attrib;
    }
} // END OF CBLElement_engine

?>
