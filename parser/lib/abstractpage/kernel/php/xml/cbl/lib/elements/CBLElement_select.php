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
 
class CBLElement_select extends CBLElement
{
	/**
	 * Element name
	 *
	 * @access public
	 * @var    string
	 */
    var $elementName = 'select';

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
		'size' => array(
			'required' => false,
			'type'     => 'int'
		),
		'name' => array(
			'required' => false,
			'type'     => 'string'
		),
		'multiple' => array(
			'required' => false,
			'type'     => 'boolean'
		),
		'onfocus' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onblur' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onclick' => array(
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
		'option'
	);
	
	
   /**
    * Add option.
    *
    * @access   public
    * @param    array   attributes of the Option
    * @return   object  CBLElement_option
    */
    function &addOption( $option = array() )
    {
        if( !is_object( $option ) )
            $option = &$this->_doc->createElement( 'option', $option );
        
        $this->appendChild( $option );
        return $option;
    }
} // END OF CBLElement_select

?>
