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
 
class CBLElement_item extends CBLElement
{
	/**
	 * Element name
	 *
	 * @access public
	 * @var    string
	 */
    var $elementName = 'item';

	/**
	 * Attribute defintions
	 *
	 * @access private
	 * @var    array
	 */
	var $_attribDefs = array(
		'rating' => array(
			'required' => false,
			'type'     => 'enum',
			'values'   => array( '0', '10', '20', '30', '40', '50', '60', '70', '80', '90', '100' )
		),
		'href' => array(
			'required' => false,
			'type'     => 'string'
		),
		'target' => array(
			'required' => false,
			'type'     => 'string'
		),
		'parents' => array(
			'required' => false,
			'type'     => 'string'
		),
		'jumps' => array(
			'required' => false,
			'type'     => 'string'
		),
		'first' => array(
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
		'media' => array(
			'required' => false,
			'type'     => 'enum',
			'values'   => array( 'audio', 'video', 'text', 'animation', 'image', 'mixed', 'other' )
		),
		'next' => array(
			'required' => false,
			'type'     => 'string'
		),
		'framework' => array(
			'required' => false,
			'type'     => 'boolean'
		),
		'created' => array(
			'required' => false,
			'type'     => 'string'
		),
		'modified' => array(
			'required' => false,
			'type'     => 'string'
		),
		'duration' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onactivate' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onnext' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onprevious' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onrevisit' => array(
			'required' => false,
			'type'     => 'string'
		),
		'ontimer' => array(
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
		'canvas',
		'label',
		'img',
		'object',
		'area',
		'attrib',
		'narration',
		'script'
	);
} // END OF CBLElement_item

?>
