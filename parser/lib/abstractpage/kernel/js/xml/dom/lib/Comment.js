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
Comment = function( ownerDoc, data )
{
	// inherited from CharacterData
	this.data = data;
  
	// inherited from CharacterData : Node
	this.nodeName  = "#comment";
	this.nodeType  = Node.COMMENT_NODE;
	this.nodeValue = data;
	this.ownerDocument = ownerDoc;
};


Comment.prototype = new CharacterData();
Comment.prototype.constructor = Comment;
Comment.superclass = CharacterData.prototype;
