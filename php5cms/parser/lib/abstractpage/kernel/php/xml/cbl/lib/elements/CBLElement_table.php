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
 
class CBLElement_table extends CBLElement
{
	/**
	 * Element name
	 *
	 * @access public
	 * @var    string
	 */
    var $elementName = 'table';

	/**
	 * Attribute defintions
	 *
	 * @access private
	 * @var    array
	 */
	var $_attribDefs = array(
		'xml:lang' => array(
			'required' => false,
			'type'     => 'string'
		),
		'xml:space' => array(
			'required' => false,
			'type'     => 'enum',
			'values'   => array( 'default', 'preserve' )
		),
		'align' => array(
			'required' => false,
			'type'     => 'enum',
			'values'   => array( 'left', 'right', 'center' )
		),
		'height' => array(
			'required' => false,
			'type'     => 'int'
		),
		'width' => array(
			'required' => false,
			'type'     => 'int'
		),
		'border' => array(
			'required' => false,
			'type'     => 'int'
		),
		'padding' => array(
			'required' => false,
			'type'     => 'int'
		)
	);
	
	/**
	 * Allowed child elements
	 *
	 * @access private
	 * @var    array
	 */
	var $_childElements = array(
		'tr'
	);
	
	
   /**
    * Add row.
    *
    * @access   public
    * @param    array   attributes of the Row
    * @return   object  CBLElement_row
    */
    function &addRow( $row = array() )
    {
        if( !is_object( $row ) )
            $row = &$this->_doc->createElement( 'tr', $row );
        
        $this->appendChild( $row );
        return $row;
    }
} // END OF CBLElement_table

?>
