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
 * The HTMLBrElement-Class represents a line feed.
 *
 * @package xml_dom_lib_html
 */
 
class HTMLBrElement extends HTMLElement
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function HTMLBrElement()
	{
		$this->HTMLElement( "br", false );
	}
	
	
	/** 
	 * setClear set the clear attribute of the br tag.
	 *
	 * @param	string		$clear
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */
	function setClear( $clear )
	{
		return $this->setAttribute( "clear", $clear );
	}
	
	/** 
	 * getClear returns the clear attribute, returns false if the attribute is not set.
	 *
	 * @return	string		$clear 
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getClear()
	{
		return $this->getAttribute( "clear" );
	}
} // END OF HTMLBrElement

?>
