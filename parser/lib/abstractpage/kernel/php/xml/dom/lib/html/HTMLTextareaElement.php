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
 * The HTMLTextareaElement-Class. This class represents a textarea field for HTML/XHTML.
 *
 * @package xml_dom_lib_html
 */
 
class HTMLTextareaElement extends HTMLInputElement
{
	/**
	 * Constructor
	 *
	 * @param	string		$name		The name attribute of the textarea element
	 * @param	string		$content	The value/content of the textarea element
	 * @access	public
	 */
	function HTMLTextareaElement( $name = "", $content = "" )
	{
		$this->HTMLInputElement( "textarea", true );
		
		if ( $name != "" )
			$this->setName( $name );
		
		// textarea element must have a closing element
		if ( $content == "" )
			$content = " ";
		
		$this->nodevalue = $content;	
	}
} // END OF HTMLTextareaElement

?>
