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
 * The HTMLImageElement-Class represents an image element.
 *
 * @package xml_dom_lib_html
 */
 
class HTMLImageElement extends HTMLElement
{
	/**
	 * Constructor
	 *
	 * @param	string		$src		The source attribute of the image element!
	 * @access	public
	 */
	function HTMLImageElement( $src = "" )
	{
		$this->HTMLElement( "img", false );

		// default: no border
		$this->setAttribute( "border", "0" );
		
		if ( $src != "" )
			$this->setAttribute( "src", $src );
	}

	
	/** 
	 * setAlign set the align attribute of the paragraph.
	 *
	 * @param	string		$align
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setAlign( $align )
	{
		return $this->setAttribute( "align", $align );
	}
	
	/** 
	 * getAlign returns the align attribute, returns false if the attribute is not set.
	 *
	 * @return	string		$align 
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getAlign( )
	{
		return $this->getAttribute( "align" );
	}
	
	/** 
	 * setSrc set the src attribute of the image tag.
	 *
	 * @param	string		$src
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setSrc( $src )
	{
		return $this->setAttribute( "src", $src );
	}
	
	/** 
	 * getSrc return the src attribute of the image tag.
	 *
	 * @return	string		$src 
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getSrc( )
	{
		return $this->getAttribute( "src" );
	}
} // END OF HTMLImageElement

?>
