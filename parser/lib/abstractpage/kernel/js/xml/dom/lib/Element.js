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
Element = function( ownerDoc, name )
{
	this.tagName = name;
  
	// inherited from Node
	this.attributes = new NamedNodeMap();
	this.childNodes = new NodeList();
	this.nodeType   = Node.ELEMENT_NODE;
	this.nodeName   = name;
	
	this.ownerDocument = ownerDoc;
};


Element.prototype = new Node();
Element.prototype.constructor = Element;
Element.superclass = Node.prototype;

/**
 * @access public
 */
Element.prototype.getAttribute = function( name )
{
	var attr = this.attributes.getNamedItem( name );
	return ( attr == null )? "" : attr.getValue();
};

/**
 * @access public
 */
Element.prototype.getAttributeNode = function( name )
{
	return this.attributes.getNamedItem( name );
};

/**
 * @access public
 */
Element.prototype.getElementsByTagName = function( tagName )
{
	return new DeepNodeList( this, tagName );
};

/**
 * @access public
 */
Element.prototype.getTagName = function()
{
	return this.tagName;
};

/**
 * @access public
 */
Element.prototype.normalize = function()
{
	var child, next;

	for ( child = this.getFirstChild(); child != null; child = next )
	{
    	next = child.getNextSibling();

		if ( child.getNodeType() == Node.TEXT_NODE )
		{
			if ( next != null && next.getNodeType() == Node.TEXT_NODE )
			{
				child.appendData( next.getNodeValue() );
				this.removeChild( next );
				next = child;
			}
			else
			{
				if ( child.getNodeValue().length == 0 )
					this.removeChild( child );
			}
		}
		else if ( child.getNodeType() == Node.ELEMENT_NODE )
		{
			child.normalize();
		}
	}
};

/**
 * @access public
 */
Element.prototype.removeAttribute = function( name )
{
	this.attributes.removeNamedItem( name );
};

/**
 * @access public
 */
Element.prototype.removeAttributeNode = function( attr )
{
	return this.attributes.removeNamedItem( attr.nodeName );
};

/**
 * @access public
 */
Element.prototype.setAttribute = function( name, value )
{
	var attr = this.ownerDocument.createAttribute( name );
	arrt.setValue( value );
	this.attributes.setNamedItem( attr );
};

/**
 * @access public
 */
Element.prototype.setAttributeNode = function( attr )
{
	return this.attributes.setNamedItem( attr );
};
