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
Document = function()
{
	this.doctype = null;
	this.implementation = null;
	this.documentElement = null;

	// inherited from Node
	this.childNodes = new NodeList();
	this.nodeName   = "#document";
	this.nodeType   = Node.DOCUMENT_NODE;
	this.ownerDocument = this;
};


Document.prototype = new Node();
Document.prototype.constructor = Document;
Document.superclass = Node.prototype;

/**
 * @access public
 */
Document.prototype.createAttribute = function( name, value )
{
	return new Attr( this, name, value );
};

/**
 * @access public
 */
Document.prototype.createCDATASection = function( data )
{
	return new CDATASection( this, data );
};

/**
 * @access public
 */
Document.prototype.createComment = function( data )
{
	return new Comment( this, data );
};

/**
 * @access public
 */
Document.prototype.createDocumentFragment = function()
{
	return new DocumentFragment( this );
};

/**
 * @access public
 */
Document.prototype.createElement = function( tagName )
{
	return new Element( this, tagName );
};

/**
 * @access public
 */
Document.prototype.createEntityReference = function( name )
{
	return new EntityReference( this, name );
};

/**
 * @access public
 */
Document.prototype.createProcessingInstruction = function( target, data )
{
	return new ProcessingInstruction( this, target, data );
};

/**
 * @access public
 */
Document.prototype.createTextNode = function( data )
{
	return new Text( this, data );
};

/**
 * @access public
 */
Document.prototype.getDoctype = function()
{
	return this.doctype;
};

/**
 * @access public
 */
Document.prototype.getDocumentElement = function()
{
	return this.documentElement;
};

/**
 * @access public
 */
Document.prototype.getElementsByTagName = function( tagName )
{
	return new DeepNodeList( this, tagName );
};

/**
 * @access public
 */
Document.prototype.getImplementation = function()
{
	return this.implementation;
};
