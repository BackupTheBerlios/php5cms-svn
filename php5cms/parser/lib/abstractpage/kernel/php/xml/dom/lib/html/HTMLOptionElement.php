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

 
using( 'xml.dom.lib.html.HTMLInputElement' );


/**
 * The HTMLOptionElement-Class represents a option element.
 *
 * @package xml_dom_lib_html
 */
 
class HTMLOptionElement extends HTMLInputElement
{
	/**
	 * Constructor
	 *
	 * @param	string		$text		The text of the option element (required)
	 * @param	string		$value		The value of the option element
	 * @access	public
	 */
	function HTMLOptionElement( $text, $value = "" )
	{
		$this->HTMLInputElement( "option", true );
		
		$this->nodevalue = $text;
		
		if ( $value != "" )
			$this->setValue( $value );
	}
	
	
	/** 
	 * setSelected set the selected attribute.
	 *
	 * @param	boolean		$selected	defaults to true
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setSelected( $selected = true )
	{
		return $this->setAttribute( "selected", $selected);
	}
} // END OF HTMLOptionElement

?>
