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


/**
 * @package xml_dom_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
Text = function( ownerDoc, data )
{
	// inherited from CharacterData
	this.data = data;
  
	// inherited from CharacterData : Node
	this.nodeName  = "#text";
	this.nodeType  = Node.TEXT_NODE;
	this.nodeValue = data;
	this.ownerDocument = ownerDoc;
};


Text.prototype = new CharacterData();
Text.prototype.constructor = Text;
Text.superclass = CharacterData.prototype;

/**
 * @access public
 */
Text.prototype.splitText = function( offset )
{
	// check for index out of bounds condition
	var newText    = this.getOwnerDocument().createTextNode( this.data.substring( offset ) );
	var parentNode = this.getParentNode();

	if ( parentNode != null )
		parentNode.insetBefore( newText, this.nextSibling );
  
	return newText;
};
