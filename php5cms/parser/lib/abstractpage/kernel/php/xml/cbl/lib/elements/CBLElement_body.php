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
 
class CBLElement_body extends CBLElement
{
	/**
	 * Element name
	 *
	 * @access public
	 * @var    string
	 */
    var $elementName = 'body';

	/**
	 * Attribute defintions
	 *
	 * @access private
	 * @var    array
	 */
	var $_attribDefs = array(
		'playout' => array(
			'required' => false,
			'type'     => 'enum',
			'values'   => array( 'auto', 'user' )
		),
		'onbind' => array(
			'required' => false,
			'type'     => 'string'
		),
		'oninit' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onrefresh' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onsearch' => array(
			'required' => false,
			'type'     => 'string'
		)
	);
	
	/**
	 * Allowed child elements
	 *
	 * @access private
	 * @var    array
	 */
	var $_childElements = array(
		'keyword',
		'group',
		'seq',
		'item'
	);
	
	
   /**
    * Add item.
    *
    * @access   public
    * @param    array   attributes of the Item
    * @return   object  CBLElement_item
    */
    function &addItem( $item = array() )
    {
        if( !is_object( $item ) )
            $item = &$this->_doc->createElement( 'item', $item );
        
        $this->appendChild( $item );
        return $item;
    }
	
   /**
    * Add keyword.
    *
    * @access   public
    * @param    array   attributes of the Keyword
    * @return   object  CBLElement_keyword
    */
    function &addKeyword( $keyword = array() )
    {
        if( !is_object( $keyword ) )
            $keyword = &$this->_doc->createElement( 'keyword', $keyword );
        
        $this->appendChild( $keyword );
        return $keyword;
    }
} // END OF CBLElement_body

?>
