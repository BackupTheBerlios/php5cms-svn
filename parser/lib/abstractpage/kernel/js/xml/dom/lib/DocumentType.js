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
DocumentType = function( ownderDoc, name )
{
	this.name = name;
	this.entities  = null;
	this.notations = null;
  
	// inherited from Node
	this.nodeName = name;
	this.nodeType = Node.DOCUMENT_TYPE_NODE;
	this.ownerDocument = ownderDoc;
};


DocumentType.prototype = new Node();
DocumentType.prototype.constructor = DocumentType;
DocumentType.superclass = Node.prototype;

/**
 * @access public
 */
DocumentType.prototype.getEntities = function()
{
	return Base.raiseError( "Not implemented." );
};

/**
 * @access public
 */
DocumentType.prototype.getName = function()
{
	return this.name;
};

/**
 * @access public
 */
DocumentType.prototype.getNotations = function()
{
	return Base.raiseError( "Not implemented." );
};
