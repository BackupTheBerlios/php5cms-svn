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
 
class CBLElement_area extends CBLElement
{
	/**
	 * Element name
	 *
	 * @access public
	 * @var    string
	 */
    var $elementName = 'area';

	/**
	 * Attribute defintions
	 *
	 * @access private
	 * @var    array
	 */
	var $_attribDefs = array(
		'regions' => array(
			'required' => false,
			'type'     => 'string'
		),
		'visible' => array(
			'required' => false,
			'type'     => 'boolean'
		),
		'hue' => array(
			'required' => false,
			'type'     => 'int'
		),
		'saturation' => array(
			'required' => false,
			'type'     => 'int'
		),
		'brightness' => array(
			'required' => false,
			'type'     => 'int'
		),
		'transparency' => array(
			'required' => false,
			'type'     => 'int'
		),
		'focus' => array(
			'required' => false,
			'type'     => 'int'
		),
		'map' => array(
			'required' => false,
			'type'     => 'string'
		),
		'coords' => array(
			'required' => true,
			'type'     => 'string'
		),
		'shape' => array(
			'required' => true,
			'type'     => 'enum',
			'values'   => array( 'rect', 'circle', 'polygon' )
		),
		'onclick' => array(
			'required' => false,
			'type'     => 'string'
		),
		'ondblclick' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onmousedown' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onmouseup' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onmouseover' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onmousemove' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onmouseout' => array(
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
} // END OF CBLElement_area

?>
