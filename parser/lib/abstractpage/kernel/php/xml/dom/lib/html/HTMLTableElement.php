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
 
 
using( 'xml.dom.lib.html.HTMLElement' );


/**
 * The HTMLTableElement-Class represents a table element.
 *
 * @package xml_dom_lib_html
 */
 
class HTMLTableElement extends HTMLElement
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function HTMLTableElement()
	{
		$this->HTMLElement( "table", true );
	}
	
	
	/** 
	 * setWidth set the width attribute of the table tag.
	 *
	 * @param	string		$width
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setWidth( $width )
	{
		return $this->setAttribute( "width", $width );
	}
	
	/** 
	 * getWidth returns the width attribute, returns false if the attribute is not set.
	 *
	 * @return	string		$width 
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getWidth( )
	{
		return $this->getAttribute( "width" );
	}
	
	/** 
	 * setBorder set the border attribute of the table tag.
	 *
	 * @param	string		$border
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setBorder( $border) {
		return $this->setAttribute( "border", $border );
	}
	
	/** 
	 * getBorder returns the border attribute, returns false if the attribute is not set.
	 *
	 * @return	string		$border 
	 * @see 	Element::getAttribute()
	 * @access	public
	 */	
	function getBorder( )
	{
		return $this->getAttribute( "border" );
	}
} // END OF HTMLTableElement

?>
