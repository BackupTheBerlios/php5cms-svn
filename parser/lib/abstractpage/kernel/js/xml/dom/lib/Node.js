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
Node = function()
{
	this.childNodes = new NodeList();
	
	this.attributes      = null;
	this.firstChild      = null
	this.lastChild       = null;
	this.nextSibling     = null;
	this.nodeName        = null;
	this.nodeType        = null;
	this.nodeValue       = null;
	this.ownerDocument   = null;
	this.parentNode      = null;
	this.previousSibling = null;
};


/**
 * @access public
 */
Node.prototype.getAttributes = function()
{
	return this.attributes;
};

/**
 * @access public
 */
Node.prototype.getChildNodes = function()
{
	return this.childNodes;
};

/**
 * @access public
 */
Node.prototype.getFirstChild = function()
{
	return this.firstChild;
};

/**
 * @access public
 */
Node.prototype.getLastChild = function()
{
	return this.lastChild;
};

/**
 * @access public
 */
Node.prototype.getNextSibling = function()
{
	return this.nextSibling;
};

/**
 * @access public
 */
Node.prototype.getNodeName = function()
{
	return this.nodeName;
};

/**
 * @access public
 */
Node.prototype.getNodeType = function()
{
	return this.nodeType;
};

/**
 * @access public
 */
Node.prototype.getNodeValue = function()
{
	return this.nodeValue;
};

/**
 * @access public
 */
Node.prototype.getOwnerDocument = function()
{
	return this.ownerDocument;
};

/**
 * @access public
 */
Node.prototype.getParentNode = function()
{
	return this.parentNode;
};

/**
 * @access public
 */
Node.prototype.getPreviousSibling = function()
{
	return this.previousSibling;
};

/**
 * @access public
 */
Node.prototype.setNodeValue = function()
{
	// Default behavior is to do nothing;
	// overridden in some subclasses
};

/**
 * @access public
 */
Node.prototype.appendChild = function( childNode )
{
	if ( this.nodeType == Node.ELEMENT_NODE   ||
		 this.nodeType == Node.ATTRIBUTE_NODE ||
		 this.nodeType == Node.DOCUMENT_NODE  ||
		 this.nodeType == Node.DOCUMENT_FRAGMENT_NODE )
	{
		this.childNodes.add( childNode );
	}
	else
	{
		return Base.raiseError( "Cannot append child node." );
	}

	if ( this.ownerDocument != childNode.ownerDocument )
		return Base.raiseError( "Cannot append child to this document." );
  
	if ( this.childNodes.length == 1 )
		this.firstChild = childNode;

	this.lastChild = childNode;
	childNode.parentNode = this;
  
	var prevSibling = this.childNodes.item( -2 );
	childNode.previousSibling = prevSibling;
  
	if ( prevSibling != null )
		prevSibling.nextSibling = childNode;
};

/**
 * @access public
 */
Node.prototype.cloneNode = function( deep )
{
	return Base.raiseError( "Not implemented." );
};

/**
 * @access public
 */
Node.prototype.hasChildNodes = function()
{
	return ( this.childNodes.length > 0 );
};

/**
 * @access public
 */
Node.prototype.insertBefore = function( newChild, refChild )
{
	var currentChildren = this.childNodes;
	this.childNodes = new NodeList();

	for ( var i = 0; i < currentChildren.length; )
	{
		var child = currentChildren.item( i );
		
		if ( child == refChild && refChild != null )
		{
			this.appendChild( newChild );
			refChild = null;
		}
		else
		{
			this.appendChild( child );
			i++;
		}
	}
};

/**
 * @access public
 */
Node.prototype.removeChild = function( oldChild )
{
	var currentChildren = this.childNodes;
	this.childNodes = new NodeList();

	for ( var i = 0; i < currentChildren.length; i++ )
	{
		var child = currentChildren.item( i );
		
		if ( child != oldChild )
			this.appendChild( child );
	}
};

/**
 * @access public
 */
Node.prototype.replaceChild = function( newChild, oldChild )
{
	var oldChildren = this.childNodes;
	this.childNodes = new NodeList();

	for ( var i = 0; i < oldChildren.length; i++ )
	{
		if ( oldChildren.item(i) == oldChild )
			this.appendChild( newChild );
		else
			this.appendChild( oldChild );
	}
};


/**
 * @constant
 */
Node.ELEMENT_NODE =  1;

/**
 * @constant
 */
Node.ATTRIBUTE_NODE =  2;

/**
 * @constant
 */
Node.TEXT_NODE =  3;

/**
 * @constant
 */
Node.CDATA_SECTION_NODE =  4;

/**
 * @constant
 */
Node.ENTITY_REFERENCE_NODE =  5;

/**
 * @constant
 */
Node.ENTITY_NODE =  6; // not used

/**
 * @constant
 */
Node.PROCESSING_INSTRUCTION_NODE =  7;

/**
 * @constant
 */
Node.COMMENT_NODE =  8;

/**
 * @constant
 */
Node.DOCUMENT_NODE =  9;

/**
 * @constant
 */
Node.DOCUMENT_TYPE_NODE = 10;

/**
 * @constant
 */
Node.DOCUMENT_FRAGMENT_NODE = 11;

/**
 * @constant
 */
Node.NOTATION_NODE = 12; // not used
