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
 * @package image_svg
 */
 
/**
 * Constructor
 *
 * @access public
 */
SVGNodeBuilder = function( type, attributes, text )
{
	this.Base = Base;
	this.Base();
	
	this.type       = type;
	this.attributes = attributes;
	this.text       = text;
	this.node       = null;
	this.tnode      = null;
};


SVGNodeBuilder.prototype = new Base();
SVGNodeBuilder.prototype.constructor = SVGNodeBuilder;
SVGNodeBuilder.superclass = Base.prototype;

/**
 * @access public
 */
SVGNodeBuilder.prototype.appendTo = function( parent )
{
	var SVGDoc = parent.ownerDocument;
	var node   = SVGDoc.createElementNS( "http://www.w3.org/2000/svg", this.type );
	this.node  = node;

	for ( var a in this.attributes )
		node.setAttributeNS( null, a, this.attributes[a] );

	if ( this.text )
	{
		var tnode = SVGDoc.createTextNode( this.text );
		node.appendChild( tnode );
		this.tnode = tnode;
	}

	if ( parent )
		parent.appendChild( this.node );
};

/**
 * @access public
 */
SVGNodeBuilder.prototype.remove = function()
{
	if ( this.node )
		this.node.parentNode.removeChild( this.node );

	this.type       = "";
	this.attributes = null;
    this.text       = null;
	this.node       = null;
	this.tnode      = null;
};
