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


using( 'xml.dom.lib.Element' );


/**
 * The WMLElement-Class. The base class for all WMLElements.
 *
 * The WMLElement Class serves as a basis for all WML related elements, 
 * it defines functions for setting and retrieving general attributes.
 * 
 * This class is not meant to be used directly.
 *
 * @package xml_dom_lib_wml
 */
 
class WMLElement extends Element
{
	/**
	 * Constructor
	 * 
	 * This class is not meant to be used directly. So this constructor is only
	 * for convenient usage, when dealing with WMLElement objects.
	 *
	 * @param		string		$name		The name of the WML element
	 * @param		string		$content	The content of the WML element
	 * @access		public
	 */
	function WMLElement( $name = "", $content = "" )
	{
		$this->Element();
		
		$this->tagName   = $name;
		$this->nodevalue = $content;
	}
		
	/** 
	 * setId set the id attribute of the current WMLElement.
	 *
	 * @param		string		$id			Wert des Attributes
	 * @return		boolean		true
	 * @see			Element::setAttribute()
	 * @access		public
	 */	
	function setId( $id )
	{
		return $this->setAttribute( "id", $id );
	}

	/** 
	 * getId returns the id attribute, returns false if the attribute is not set.
	 *
	 * @return		string		$id 
	 * @see			Element::getAttribute()
	 * @access 		public
	 */	
	function getId()
	{
		return $this->getAttribute( "id" );
	}
	
	/** 
	 * setClass set the class attribute of the current WMLElement.
	 *
	 * @param		string		$cssclass	Name of the css-class
	 * @return 		boolean		true
	 * @see			Element::setAttribute()
	 * @access		public
	 */	
	function setClass( $cssclass )
	{
		return $this->setAttribute( "class", $cssclass );
	}
	
	/** 
	 * getClass returns the class attribute, returns false if the attribute is not set.
	 *
	 * @return		string		$class 
	 * @see			Element::getAttribute()
	 * @access		public
	 */	
	function getClass()
	{
		return $this->getAttribute( "class" );
	}
} // END OF WMLElement

?>
