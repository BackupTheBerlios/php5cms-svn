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
 * Seralizes an IE HTML DOM tree to a well formed XHTML string.
 *
 * @package xml
 */

/**
 * Constructor
 *
 * @access public
 */
XHTMLSerializer = function()
{
	this.Base = Base;
	this.Base();
};


XHTMLSerializer.prototype = new Base();
XHTMLSerializer.prototype.constructor = XHTMLSerializer;
XHTMLSerializer.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
XHTMLSerializer.getXhtml = function( oNode ) 
{
	var sb = new StringBuilder;
	
	// IE5 and IE55 has trouble with the document node so beware
	XHTMLSerializer._appendNodeXHTML( oNode, sb );
	return sb.toString();
};


// private methods

/**
 * @access private
 * @static
 */
XHTMLSerializer._fixAttribute = function( s ) 
{
	return String( s ).replace( /\&/g, "&amp;" ).replace( /</g, "&lt;" ).replace( /\"/g, "&quot;" );
};

/**
 * @access private
 * @static
 */
XHTMLSerializer._fixText = function( s ) 
{
	return String( s ).replace( /\&/g, "&amp;" ).replace( /</g, "&lt;" );
};

/**
 * @access private
 * @static
 */
XHTMLSerializer._getAttributeValue = function( oAttrNode, oElementNode, sb ) 
{
	if ( !oAttrNode.specified )
		return;
		
	var name  = oAttrNode.nodeName;
	var value = oAttrNode.nodeValue;
	
	if ( name != "style" ) 
	{
		if ( !isNaN( value ) ) // IE5.x bugs for number values
			value = oElementNode.getAttribute( name );
			
		sb.append( " " + ( oAttrNode.expando? name : name.toLowerCase()) +
				   "=\"" + XHTMLSerializer._fixAttribute( value ) + "\"" );
	}
	else
	{
		sb.append( " style=\"" + XHTMLSerializer._fixAttribute( oElementNode.style.cssText ) + "\"" );
	}
};

/**
 * @access private
 * @static
 */
XHTMLSerializer._appendNodeXHTML = function( node, sb ) 
{
	switch ( node.nodeType ) 
	{
		case 1:	// ELEMENT
			// IE5.0 and IE5.5 are weird
			if ( node.nodeName == "!" ) 
			{
				sb.append( node.text );
				break;
			}
		
			var name = node.nodeName;

			if ( node.scopeName == "HTML" )
				name = name.toLowerCase();
		
			sb.append( "<" + name );
			
			// attributes
			var attrs = node.attributes;
			var l = attrs.length;

			for ( var i = 0; i < l; i++ )
				XHTMLSerializer._getAttributeValue( attrs[i], node, sb );
				
			if ( node.canHaveChildren || node.hasChildNodes() ) 
			{
				sb.append( ">" );
				
				// childNodes
				var cs = node.childNodes;
				l = cs.length;
				
				for ( var i = 0; i < l; i++ )
					_appendNodeXHTML( cs[i], sb );
				
				sb.append( "</" + name + ">" );
			}
			else if ( name == "script" )
			{
				sb.append( ">" + node.text + "</" + name + ">" );
			}
			else if ( name == "title" || name == "style" || name == "comment" )
			{
				sb.append( ">" + node.innerHTML + "</" + name + ">" );
			}
			else 
			{
				sb.append( " />" );
			}
			
			break;
			
		case 3:	// TEXT
			sb.append( _fixText( node.nodeValue ) );
			break;
				
		case 4:
			sb.append( "<![CDA" + "TA[\n" + node.nodeValue + "\n]" + "]>" );
			break;
				
		case 8:
			// sb.append( "<!--" + node.nodeValue + "-->" );
			sb.append( node.text );
			
			if ( /(^<\?xml)|(^<\!DOCTYPE)/.test( node.text ) )
				sb.append( "\n" );

			break;
			
		case 9:	// DOCUMENT
			// childNodes
			var cs = node.childNodes;
			l = cs.length;
			
			for ( var i = 0; i < l; i++ )
				XHTMLSerializer._appendNodeXHTML( cs[i], sb );

			break;
			
		default:
			sb.append( "<!--\nNot Supported:\n\n" + "nodeType: " + node.nodeType + "\nnodeName: " + node.nodeName + "\n-->" );
	}
};
