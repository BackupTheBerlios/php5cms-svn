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
 
class CBLElement_canvas extends CBLElement
{
	/**
	 * Element name
	 *
	 * @access public
	 * @var    string
	 */
    var $elementName = 'canvas';

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
		'border' => array(
			'required' => false,
			'type'     => 'int'
		),
		'padding' => array(
			'required' => false,
			'type'     => 'int'
		),
		'regions' => array(
			'required' => false,
			'type'     => 'string'
		),
		'left' => array(
			'required' => false,
			'type'     => 'int'
		),
		'top' => array(
			'required' => false,
			'type'     => 'int'
		),
		'zindex' => array(
			'required' => false,
			'type'     => 'int'
		),
		'bgcolor' => array(
			'required' => false,
			'type'     => 'string'
		),
		'bordercolor' => array(
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
		'filter' => array(
			'required' => false,
			'type'     => 'string'
		),
		'scrolling' => array(
			'required' => false,
			'type'     => 'enum',
			'values'   => array( 'yes', 'no', 'auto' )
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
		),
		'ondrag' => array(
			'required' => false,
			'type'     => 'string'
		),
		'ondrop' => array(
			'required' => false,
			'type'     => 'string'
		),
		'onscroll' => array(
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
		'b',
		'i',
		'u',
		'font',
		'br',
		'a',
		'heading',
		'img',
		'object',
		'ol',
		'ul',
		'p',
		'pre',
		'hr',
		'title',
		'form',
		'table'
	);
} // END OF CBLElement_canvas

?>
