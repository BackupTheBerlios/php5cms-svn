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
 
class CBLElement_input extends CBLElement
{
	/**
	 * Element name
	 *
	 * @access public
	 * @var    string
	 */
    var $elementName = 'input';

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
		'height' => array(
			'required' => false,
			'type'     => 'int'
		),
		'width' => array(
			'required' => false,
			'type'     => 'int'
		),
		'size' => array(
			'required' => false,
			'type'     => 'int'
		),
		'src' => array(
			'required' => false,
			'type'     => 'string'
		),
		'name' => array(
			'required' => false,
			'type'     => 'string'
		),
		'value' => array(
			'required' => true,
			'type'     => 'string'
		),
		'maxlength' => array(
			'required' => false,
			'type'     => 'int'
		),
		'format' => array(
			'required' => false,
			'type'     => 'string'
		),
		'type' => array(
			'required' => false,
			'type'     => 'enum',
			'values'   => array( 'text', 'password', 'submit', 'reset', 'hidden', 'image' )
		),
		'onfocus' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onblur' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onchange' => array(
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
	);
} // END OF CBLElement_input

?>
