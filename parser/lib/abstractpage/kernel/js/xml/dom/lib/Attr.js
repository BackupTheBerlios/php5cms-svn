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
Attr = function( ownerDoc, name )
{
	this.name = name;
	this.specified = true;
	this.value = null;
  
	// inherited from Node
	this.childNodes = new NodeList();
	this.nodeName   = name;
	this.nodeType   = Node.ATTRIBUTE_NODE;
	this.nodeValue  = null;
	this.ownerDocument = ownerDoc;
};


Attr.prototype = new Node();
Attr.prototype.constructor = Attr;
Attr.superclass = Node.prototype;

/**
 * @access public
 */
Attr.prototype.getName = function()
{
	return this.name;
};

/**
 * @access public
 */
Attr.prototype.getNodeValue = function()
{
	return this.getValue();
};

/**
 * @access public
 */
Attr.prototype.getSpecified = function()
{
	return this.specified;
};

/**
 * @access public
 */
Attr.prototype.getValue = function()
{
	var value = "";

	for ( var i = 0; i < this.childNodes.length; i++ )
		value += this.childNodes.item( i ).getNodeValue();

	return value;
};

/**
 * @access public
 */
Attr.prototype.setValue = function( value )
{
	this.childNodes = new NodeList();
	this.firstChild = null;
	this.lastChild  = null;
  
	if ( value != null )
		this.appendChild( this.ownerDocument.createTextNode( value ) );
};
