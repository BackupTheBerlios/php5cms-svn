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
 * @package xml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
XMLNode = function( nodeType, doc, str )
{
	this.Base = Base;
	this.Base();
	
	// the content of text (also CDATA and COMMENT) nodes
    if ( nodeType == 'TEXT' || nodeType == 'CDATA' || nodeType == 'COMMENT' )
        this.content = str;
    else
        this.content = null;

    this.attributes	= null;		// an array of attributes (used as a hash table)
    this.children	= null;		// an array (list) of the children of this node
    this.doc		= doc;		// a reference to the document
    this.nodeType	= nodeType;	// the type of the node
    this.parent		= "";
    this.tagName	= "";		// the name of the tag (if a tag node)
};


XMLNode.prototype = new Base();
XMLNode.prototype.constructor = XMLNode;
XMLNode.superclass = Base.prototype;

/**
 * @access public
 */
XMLNode.prototype.addAttribute = function(attributeName,attributeValue)
{
	// if the name is found, the old value is overwritten by the new value
	this.attributes['_' + attributeName] = attributeValue;
	return true;
};

/**
 * @access public
 */
XMLNode.prototype.addElement = function( node )
{
    node.parent = this;
    this.children[this.children.length] = node;
	
    return true;
};

/**
 * @access public
 */
XMLNode.prototype.getAttribute = function( name )
{
    if ( this.attributes == null )
        return null;
    
    return this.attributes['_' + name];
};

/**
 * @access public
 */
XMLNode.prototype.getAttributeNames = function()
{ 
    if ( this.attributes == null )
	{
        var ret = new Array();
        return ret;
    }

    var attlist = new Array();

    for ( var a in this.attributes )
        attlist[attlist.length] = a.substring( 1 );
    
    return attlist;
};

/**
 * @access public
 */
XMLNode.prototype.getElementById = function( id )
{
    var node = this;
    var ret;

    if ( node.getAttribute( "id" ) == id )
	{
        return node;
    }
    else
	{
        var elements = node.getElements();
        var intLoop  = 0;

        while ( intLoop < elements.length )
		{
            var element = elements[intLoop];
            ret = element.getElementById( id );
			
            if ( ret != null )
				break;

            intLoop++;
        }
    }
	
    return ret;
};

/**
 * @access public
 */
XMLNode.prototype.getElements = function( byName )
{
    if ( this.children == null )
	{
        var ret = new Array();
        return ret;
    }

    var elements = new Array();
    
	for ( var i = 0; i < this.children.length; i++ )
	{
        if ( ( this.children[i].nodeType == 'ELEMENT') && ( ( byName == null ) || ( this.children[i].tagName == byName ) ) )
            elements[elements.length] = this.children[i];
    }
	
    return elements;
};

/**
 * @access public
 */
XMLNode.prototype.getText = function()
{
    if ( this.nodeType == 'ELEMENT' )
	{
        if ( this.children == null )
			return null;
        
        var str = "";
		
        for ( var i = 0; i < this.children.length; i++ )
		{
            var t = this.children[i].getText();
            str +=  ( t == null? "" : t );
        }
		
        return str;
    }
    else if ( this.nodeType == 'TEXT' )
	{
        return XML.convertEscapes( this.content );
    }
    else
	{
        return this.content;
    }
};

/**
 * @access public
 */
XMLNode.prototype.getParent = function()
{ 
    return this.parent;
};

/**
 * @access public
 */
XMLNode.prototype.getUnderlyingXMLText = function()
{ 
    var strRet = "";
    strRet = XML._displayElement( this, strRet );
    return strRet;
};

/**
 * @access public
 */
XMLNode.prototype.removeAttribute = function( attributeName )
{
    if ( attributeName == null )
        return this.doc.error( "You must pass an attribute name into the removeAttribute function." );

    // Now remove the attribute from the list.
    // I want to keep the logic for adding attribtues in one place. I'm
    // going to get a temp array of attributes and values here and then
    // use the addAttribute function to re-add the attributes
    var attributes = this.getAttributeNames();
    var intCount   = attributes.length;
    var tmpAttributeValues = new Array();
	
    for ( intLoop = 0; intLoop < intCount; intLoop++ )
        tmpAttributeValues[intLoop] = this.getAttribute(attributes[intLoop]);

    // now blow away the old attribute list
    this.attributes = new Array();

    //now add the attributes back to the array - leaving out the one we're removing
    for ( intLoop = 0; intLoop < intCount; intLoop++ )
	{
        if ( attributes[intLoop] != attributeName)
            this.addAttribute( attributes[intLoop], tmpAttributeValues[intLoop] );
    }

	return true;
};

/**
 * @access public
 */
XMLNode.prototype.toString = function()
{
    return "" + this.nodeType + ":" + ( this.nodeType == 'TEXT' || this.nodeType == 'CDATA' || this.nodeType == 'COMMENT'? this.content : this.tagName );
};
