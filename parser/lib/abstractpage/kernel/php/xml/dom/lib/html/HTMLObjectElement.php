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
 * The HTMLObjectElement-Class represents an object element.
 *
 * @package xml_dom_lib_html
 */
 
class HTMLObjectElement extends HTMLElement
{
	/**
	 * Constructor
	 *
	 * @param	string		$name		The name for the Object element
	 * @param	string		$clsid		The clsid for the Object element
	 * @access	public
	 */
	function HTMLObjectElement( $name = "", $clsid = "" )
	{
		$this->HTMLElement( "object", true );
		
		if ( $name != "" )
			$this->setName( $name );
		
		$this->nodevalue = " ";
		
		if ( $clsid != "" )
			$this->setAttribute( "clsid", $clsid );
	}
} // END OF HTMLObjectElement

?>
