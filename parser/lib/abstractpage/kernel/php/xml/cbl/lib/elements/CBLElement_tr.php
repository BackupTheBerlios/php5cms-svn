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
 
class CBLElement_tr extends CBLElement
{
	/**
	 * Element name
	 *
	 * @access public
	 * @var    string
	 */
    var $elementName = 'tr';

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
		'td'
	);
	
	
   /**
    * Add cell.
    *
    * @access   public
    * @param    array   attributes of the Cell
    * @return   object  CBLElement_cell
    */
    function &addCell( $cell = array() )
    {
        if( !is_object( $cell ) )
            $cell = &$this->_doc->createElement( 'td', $cell );
        
        $this->appendChild( $cell );
        return $cell;
    }
} // END OF CBLElement_tr

?>
