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
 * The HTMLInputElement-Class represents a form input element.
 *
 * @package xml_dom_lib_html
 */
 
class HTMLInputElement extends HTMLElement
{
	/** 
	 * Constructor
	 *
	 * @param	string		$name		the name of the element
	 * @param	string		$value		the value of the element
	 * @access	public
	 */	
	function HTMLInputElement( $name = "", $value = "" )
	{
		$this->HTMLElement( "input", false );
		
		if ( $name != "" )
			$this->setName( $name );
		
		if ( $value != "" )
			$this->setValue( $value );
	}
	
	/** 
	 * setType set the type attribute.
	 *
	 * @param	string		$type		may be "text", "checkbox", "radio", "button", "image" 
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setType( $type )
	{
		$this->setAttribute( "type", $type );
	}
	
	/** 
	 * getType returns the type attribute, if set.
	 *
	 * @return	string		$type
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getType( ) {
		return $this->getAttribute( "type" );
	}
	
	/** 
	 * setReadonly set the readonly attribute.
	 *
	 * @param	boolean		$readonly	defaults to true
	 * @return	boolean 	true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setReadonly( $readonly = true )
	{
		return $this->setAttribute( "readonly", $readonly );
	}
	
	/** 
	 * setChecked set the checked attribute.
	 *
	 * @param	boolean		$checked	defaults to true
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setChecked( $checked = true )
	{
		return $this->setAttribute( "checked", $checked );
	}
	
	/** 
	 * setDisabled set the disabled attribute.
	 *
	 * @param	boolean		$disabled	defaults to true
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setDisabled( $disabled = true )
	{
		return $this->setAttribute( "disabled", $disabled );
	}

	/** 
	 * setName set the name attribute.
	 *
	 * @param	string		$name
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setName( $name )
	{
		return $this->setAttribute( "name", $name );
	}
	
	/** 
	 * getName returns the name attribute, if set.
	 *
	 * @return	string		$method
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getName()
	{
		return $this->setAttribute( "name" );
	}
	
	/** 
	 * setValue set the value attribute.
	 *
	 * @param	string		$value
	 * @return	boolean 	true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setValue( $value )
	{
		return $this->setAttribute( "value", $value );
	}
	
	/** 
	 * getValue returns the value attribute, if set.
	 *
	 * @return	string		$method
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getValue()
	{
		return $this->setAttribute( "value" );
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
	 * @see		getAttribute()
	 * @access	public
	 */	
	function getSrc( )
	{
		return $this->getAttribute( "src" );
	}
	
	/** 
	 * getForm return the Form-Element to which the Element is assigned
	 *
	 * Returns a reference to the Form-Element. 
	 * 
	 * Be careful: If the current Element is not
	 * in the scope of a form, the function cause an error! 
	 *
	 * @return 	object		HTMLFormElement $form
	 * @access	public
	 */	
	function getForm()
	{
		!$found	= false;
		$form	= new HTMLFormElement;
		$parent_element	= $this->getParent();
		
		while ( ( $parent_element ) && (!$found) )
		{
			if ( $parent_element->getNodeName() == "form" )
			{
				$found = true;
				break;
			}

			$parent_element	= $parent_element->getParent();
		}
		
		if ( $found )
		{
			$form->node	= $parent_element->node;
			return $form;
		}
		
		return false;
	}
} // END OF HTMLInputElement

?>
