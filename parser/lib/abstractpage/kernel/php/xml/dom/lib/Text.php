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


using( 'xml.dom.lib.CharacterData' );

 
/**
 * The Text Class represents a text section within a document.
 *
 * Since the current status of the domxml functions is limited to the creation of
 * XML_ELEMENT_NODEs, the text node is currently not useable.
 *
 * XML_TEXT_NODES are currently created implicitely by >> domxml_new_child <<
 * 
 * Future development should cover a CharacterData which then extend to
 * - Text nodes
 * - CData Sections
 *
 * @package xml_dom_lib
 */
 
class Text extends CharacterData
{
	/** 
	 * Contains the data for the text section
	 * @var		string	$data
	 * @access 	public
	 */
	var $data = "";
	
	
	/** 
	 * Constructor
	 *
	 * @access public
	 */
	function Text()
	{
		$this->Tag( "", false );
	}
} // END OF Text

?>
