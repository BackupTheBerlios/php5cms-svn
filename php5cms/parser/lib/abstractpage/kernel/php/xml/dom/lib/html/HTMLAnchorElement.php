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
 * The HTMLAnchorElement-Class represents a hyperlink.
 *
 * @package xml_dom_lib_html
 */
 
class HTMLAnchorElement extends HTMLElement
{
	/**
	 * Constructor
	 *
	 * @param	string		The uniform resource locator
	 * @param	string		The Text representing the Link
	 * @access	public
	 */
	function HTMLAnchorElement( $href = "", $value = "" )
	{
		$this->HTMLElement( "a", true );
		
		if ( $href != "" )
			$this->setHref( $href );
		
		if ( $value != "" )
			$this->nodevalue = $value;
	}
	
	
	/** 
	 * setHref set the href attribute of the anchor
	 * @param		string		$href
	 * @return		boolean		true
	 * @see		Element::setAttribute()
	 */	
	function setHref( $href )
	{
		return $this->setAttribute( "href", $href );
	}
	
	/** 
	 * getSrc return the src attribute of the image tag
	 * @return		string		$src 
	 * @see		Element::getAttribute()
	 */	
	function getHref()
	{
		return $this->getAttribute( "href" );
	}
} // END OF HTMLAnchorElement

?>
