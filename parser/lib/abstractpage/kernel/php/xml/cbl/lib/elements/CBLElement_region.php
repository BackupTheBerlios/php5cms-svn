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
 
class CBLElement_region extends CBLElement
{
	/**
	 * Element name
	 *
	 * @access public
	 * @var    string
	 */
    var $elementName = 'region';

	/**
	 * Attribute defintions
	 *
	 * @access private
	 * @var    array
	 */
	var $_attribDefs = array(
		'src' => array(
			'required' => false,
			'type'     => 'string'
		),
		'bgcolor' => array(
			'required' => false,
			'type'     => 'string'
		),
		'rect' => array(
			'required' => true,
			'type'     => 'string'
		),
		'interaction' => array(
			'required' => false,
			'type'     => 'boolean'
		),
		'lockzoom' => array(
			'required' => false,
			'type'     => 'boolean'
		),
		'lockrange' => array(
			'required' => false,
			'type'     => 'boolean'
		),
		'clip' => array(
			'required' => false,
			'type'     => 'enum',
			'values'   => array( 'fit', 'meet', 'scroll', 'slice' )
		),
		'range' => array(
			'required' => false,
			'type'     => 'enum',
			'values'   => array( '0', '10', '20', '30', '40', '50', '60', '70', '80', '90', '100', 'auto', 'max', 'overview' )
		),
		'zoom' => array(
			'required' => false,
			'type'     => 'enum',
			'values'   => array( '0', '10', '20', '30', '40', '50', '60', '70', '80', '90', '100', 'auto', 'min', 'max' )
		),
		'display' => array(
			'required' => false,
			'type'     => 'string'
		),
		'text' => array(
			'required' => false,
			'type'     => 'string'
		),
		'link' => array(
			'required' => false,
			'type'     => 'string'
		),
		'vlink' => array(
			'required' => false,
			'type'     => 'string'
		),
		'background' => array(
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
		'title',
		'map',
		'attrib'
	);
} // END OF CBLElement_region

?>
