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
 * The HTMLScriptElement-Class represents Script-Section.
 *
 * @package xml_dom_lib_html
 */
 
class HTMLScriptElement extends HTMLElement
{
	/**
	 * Constructor
	 *
	 * @param	string		$language		The language of the script element
	 * @param	string		$script			The content of the script element
	 * @access	public
	 */
	function HTMLScriptElement( $language = "", $script = "" )
	{
		$this->HTMLElement( "script", true );
		
		if ( $language != "" )
			$this->setAttribute( "language", $language );
		
		$this->nodevalue = " " . $script;
	}
} // END OF HTMLScriptElement

?>
