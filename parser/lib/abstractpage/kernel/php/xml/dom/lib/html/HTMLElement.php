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
 * The HTMLElement-Class. The base class for all HTMLElements.
 *
 * The HTMLElement Class serves as a basis for all HTML related elements, it 
 * defines functions for setting and retrieving general attributes.
 * 
 * @package xml_dom_lib_html
 */
 
class HTMLElement extends Element
{
	/**
	 * HTMLElement generic constructor
	 * 
	 * This class is not meant to be used directly. So this constructor is only
	 * for convenient usage, when dealing with HTMLElement objects.
	 *
	 * @param	string		$name		The name of the html element
	 * @param	string		$content	The content of the html element
	 * @access	public
	 */
	function HTMLElement( $name = "", $content = "" )
	{
		$this->Element();
		
		$this->tagName   = $name;
		$this->nodevalue = $content;
	}
	
	
	/** 
	 * getClassName returns the name of the current class!
	 *
	 * the ClassName of the current Element. The PHP Function get_class is used
	 * to determine the classname.
	 *
	 * @return	string		$classname
	 * @access	public
	 */	
	function getClassName()
	{
		return get_class( $this );
	}
	
	/** 
	 * setStyle set the style attribute of the current HTMLElement.
	 *
	 * @param	string		$style		Valid CSS instruction
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setStyle( $style )
	{
		return $this->setAttribute( "style", $style );
	}
	
	/** 
	 * setId set the id attribute of the current HTMLElement.
	 *
	 * @param	string		$id			Wert des Attributes
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setId( $id )
	{
		return $this->setAttribute( "id", $id );
	}
	
	/** 
	 * setClass set the class attribute of the current HTMLElement.
	 *
	 * @param	string		$cssclass	Name of the css-class
	 * @return 	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setClass( $cssclass )
	{
		return $this->setAttribute( "class", $cssclass );
	}
	
	/** 
	 * setTitle set the title attribute of the current HTMLElement.
	 *
	 * @param	string		$title Title for the Element
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setTitle( $title )
	{
		return $this->setAttribute( "title", $title );
	}
	
	/** 
	 * setLang set the lang attribute of the current HTMLElement.
	 *
	 * @param	string		$lang 
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setLang( $lang )
	{
		return $this->setAttribute( "lang", $lang );
	}
	
	/** 
	 * getStyle returns the style attribute, returns false if the attribute is not set.
	 *
	 * @return	string		$style 
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getStyle()
	{
		return $this->getAttribute( "style" );
	}
	
	/** 
	 * getId returns the id attribute, returns false if the attribute is not set.
	 *
	 * @return	string		$id 
	 * @see		Element::getAttribute()
	 * @access 	public
	 */	
	function getId()
	{
		return $this->getAttribute( "id" );
	}
	
	/** 
	 * getClass returns the class attribute, returns false if the attribute is not set.
	 *
	 * @return	string		$class 
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getClass()
	{
		return $this->getAttribute( "class" );
	}
	
	/** 
	 * getLang returns the lang attribute, returns false if the attribute is not set.
	 *
	 * @return	string		$lang 
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getLang()
	{
		return $this->getAttribute( "lang" );
	}
	
	/** 
	 * getTitle returns the title attribute, if the attribute is not set, it will return false.
	 *
	 * @return	string $title
	 * @see 	Element::getAttribute()
	 * @access 	public
	 */	
	function getTitle()
	{
		return $this->getAttribute( "title" );
	}
} // END OF HTMLElement

?>
