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
 
class CBLElement_a extends CBLElement
{
	/**
	 * Element name
	 *
	 * @access public
	 * @var    string
	 */
    var $elementName = 'a';

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
		'href' => array(
			'required' => false,
			'type'     => 'string'
		),
		'target' => array(
			'required' => false,
			'type'     => 'string'
		),
		'name' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onclick' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onmouseout' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onmouseover' => array(
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
		'img',
		'br'
	);
} // END OF CBLElement_a

?>
