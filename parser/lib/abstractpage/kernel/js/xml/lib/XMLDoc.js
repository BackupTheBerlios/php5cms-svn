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
XMLDoc = function( source )
{
	this.Base = Base;
	this.Base();
	
    this.topNode   = null;
	this.hasErrors = false;
    this.source    = source;

    // parse the document
    if ( this.parse() )
	{
		// we've run out of markup - check the stack is now empty
		if ( this.topNode != null )
			return Base.raiseError( "Expected close " + this.topNode.tagName );
        else
            return true;
    }
};


XMLDoc.prototype = new Base();
XMLDoc.prototype.constructor = XMLDoc;
XMLDoc.superclass = Base.prototype;

/**
 * @access public
 */
XMLDoc.prototype.error = function( msg )
{
	this.hasErrors = true;
	return Base.raiseError( msg );
};

/**
 * @access public
 */
XMLDoc.prototype.createXMLNode = function( strXML )
{
    return new XMLDoc( strXML ).docNode;
};

/**
 * @access public
 */
XMLDoc.prototype.getUnderlyingXMLText = function()
{
    var strRet = "";
    strRet = strRet + "<?xml version=\"1.0\"?>";
    
	if ( this.docNode == null )
        return;

    strRet = XML._displayElement( this.docNode, strRet );
    return strRet;
};

/**
 * @access public
 */
XMLDoc.prototype.handleNode = function( current )
{
    if ( ( current.nodeType == 'COMMENT' ) && ( this.topNode != null ) )
	{
        return this.topNode.addElement( current );
    }
    else if ( ( current.nodeType == 'TEXT' ) ||  ( current.nodeType == 'CDATA' ) )
	{
        if ( this.topNode == null )
		{
            if ( XML.trim( current.content, true, false ) == "" )
				return true;
            else
				return Base.raiseError( "Expected document node, found: " + current );
        }
        else
		{
            // otherwise, append this as child to the element at the top of the stack
            return this.topNode.addElement( current );
        }
    }
    else if ( ( current.nodeType == 'OPEN' ) || ( current.nodeType == 'SINGLE' ) )
	{
        // if we find an element tag (open or empty)
        var success = false;

        // if the stack is empty, this node becomes the document node
        if ( this.topNode == null )
		{
            this.docNode = current;
            current.parent = null;
            success = true;
        }
        else
		{
            // otherwise, append this as child to the element at the top of the stack
            success = this.topNode.addElement( current );
        }

        if ( success && ( current.nodeType != 'SINGLE' ) )
            this.topNode = current;

        // rename it as an element node
        current.nodeType = "ELEMENT";

        return success;
    }
    // if it's a close tag, check the nesting
    else if ( current.nodeType == 'CLOSE' )
	{
        // if the stack is empty, it's certainly an error
        if ( this.topNode == null )
		{
			return Base.raiseError( "Close tag without open: " +  current.toString() );
        }
        else
		{
            // otherwise, check that this node matches the one on the top of the stack
            if ( current.tagName != this.topNode.tagName )
				return Base.raiseError( "Expected close " + this.topNode.tagName );
			// if it does, pop the element off the top of the stack
            else
				this.topNode = this.topNode.getParent();
        }
    }
	
    return true;
};

/**
 * @access public
 */
XMLDoc.prototype.insertNodeAfter = function( referenceNode, newNode )
{
    var parentXMLText = this.getUnderlyingXMLText();
    var selectedNodeXMLText = referenceNode.getUnderlyingXMLText();
    var originalNodePos = parentXMLText.indexOf( selectedNodeXMLText ) + selectedNodeXMLText.length;
    var newXML = parentXMLText.substr( 0, originalNodePos );
    newXML += newNode.getUnderlyingXMLText();
    newXML += parentXMLText.substr( originalNodePos );
    var newDoc = new XMLDoc( newXML );
	
    return newDoc;
};

/**
 * @access public
 */
XMLDoc.prototype.insertNodeInto = function(referenceNode, insertNode) {

    var parentXMLText = this.getUnderlyingXMLText();
    var selectedNodeXMLText = referenceNode.getUnderlyingXMLText();
    var endFirstTag = selectedNodeXMLText.indexOf( ">" ) + 1;
    var originalNodePos = parentXMLText.indexOf( selectedNodeXMLText ) + endFirstTag;
    var newXML = parentXMLText.substr( 0, originalNodePos );
    newXML += insertNode.getUnderlyingXMLText();
    newXML += parentXMLText.substr( originalNodePos );
    var newDoc = new XMLDoc( newXML );
	
    return newDoc;
};

/**
 * @access public
 */
XMLDoc.prototype.parse = function()
{
    var pos = 0;

    // set up the arrays used to store positions of < and > characters
    err = false;

    while ( !err )
	{
        var closing_tag_prefix = '';
        var chpos = this.source.indexOf( '<', pos );
        var open_length = 1;

        var open;
        var close;

        if ( chpos == -1 )
            break;

        open = chpos;

        // create a text node
        var str = this.source.substring( pos, open );

        if ( str.length != 0 )
            err = !this.handleNode( new XMLNode( 'TEXT', this, str ) );

        // handle Programming Instructions - they can't reliably be handled as tags
        if ( chpos == this.source.indexOf( "<?", pos ) )
		{
            pos = this.parsePI( this.source, pos + 2 );
			
            if ( pos == 0 )
                err = true;
            
            continue;
        }

        // nobble the document type definition
        if ( chpos == this.source.indexOf( "<!DOCTYPE", pos ) )
		{
            pos = this.parseDTD( this.source, chpos+ 9 );
            
			if ( pos == 0 )
                err = true;
            
            continue;
        }

        // if we found an open comment, we need to ignore angle brackets
        // until we find a close comment
        if ( chpos == this.source.indexOf( '<!--', pos ) )
		{
            open_length = 4;
            closing_tag_prefix = '--';
        }

        // similarly, if we find an open CDATA, we need to ignore all angle
        // brackets until a close CDATA sequence is found
        if ( chpos == this.source.indexOf( '<![CDATA[', pos ) )
		{
            open_length = 9;
            closing_tag_prefix = ']]';
        }

        // look for the closing sequence
        chpos = this.source.indexOf( closing_tag_prefix + '>', chpos );
		
        if ( chpos == -1 )
			return Base.raiseError( "Expected closing tag sequence: " + closing_tag_prefix + '>' );

        close = chpos + closing_tag_prefix.length;

        // create a tag node
        str = this.source.substring( open + 1, close );
        var n = this.parseTag( str );
		
        if ( n )
            err = !this.handleNode( n );

        pos = close +1;
    }
	
    return !err;
};

/**
 * @access public
 */
XMLDoc.prototype.parseAttribute = function( src, pos, node )
{
    // chew up the whitespace, if any
    while ( ( pos < src.length ) && ( XML.whitespace.indexOf( src.charAt( pos ) ) != -1 ) )
        pos++;

    // if there's nothing else, we have no (more) attributes - just break out
    if ( pos >= src.length )
        return pos;

    var p1 = pos;
    while ( ( pos < src.length ) && ( src.charAt( pos ) != '=' ) )
        pos++;

    var msg = "attributes must have values";

    // parameters without values aren't allowed.
    if ( pos >= src.length )
		return Base.raiseError( msg );

    // extract the parameter name
    var paramname = XML.trim( src.substring( p1, pos++ ), false, true );

    // chew up whitespace
    while ( ( pos < src.length ) && ( XML.whitespace.indexOf( src.charAt( pos ) ) != -1 ) )
        pos++;

    if ( pos >= src.length )
		return Base.raiseError( msg );

    msg = "attribute values must be in quotes";

    // check for a quote mark to identify the beginning of the attribute value
    var quote = src.charAt( pos++ );

    if ( XML.quotes.indexOf( quote ) == -1 )
		return Base.raiseError( msg );

    p1 = pos;
    while ( ( pos < src.length ) && ( src.charAt( pos ) != quote ) )
        pos++;

    if ( pos >= src.length )
		return Base.raiseError( msg );

    // store the parameter
    if ( !node.addAttribute( paramname, XML.trim( src.substring( p1, pos++ ), false, true ) ) )
        return 0;

    return pos;
};

/**
 * @access public
 */
XMLDoc.prototype.parseDTD = function( str, pos )
{
    var firstClose = str.indexOf( '>', pos );

    if ( firstClose == -1 )
		return Base.raiseError( "Error in DTD: expected '>'." );

    var closing_tag_prefix = '';
    var firstOpenSquare    = str.indexOf( '[', pos );

    if ( ( firstOpenSquare != -1 ) && (firstOpenSquare < firstClose ) )
        closing_tag_prefix = ']';

    while ( true )
	{
        var closepos = str.indexOf( closing_tag_prefix + '>', pos );

        if ( closepos == -1 )
			return Base.raiseError( "Expected closing tag sequence: " + closing_tag_prefix + '>' );

        pos = closepos + closing_tag_prefix.length + 1;

        if ( str.substring( closepos - 1, closepos + 2 ) != ']]>' )
            break;
    }
	
    return pos;
};

/**
 * @access public
 */
XMLDoc.prototype.parsePI = function( str, pos )
{ 
    var closepos = str.indexOf( '?>', pos );
    return closepos + 2;
};

/**
 * @access public
 */
XMLDoc.prototype.parseTag = function( src )
{ 
    if ( src.indexOf( '!--' ) == 0 )
        return new XMLNode( 'COMMENT', this, src.substring( 3, src.length - 2 ) );

    // if it's CDATA, do similar
    if ( src.indexOf( '![CDATA[' ) == 0 )
        return new XMLNode( 'CDATA', this, src.substring( 8, src.length - 2 ) );

    var n = new XMLNode();
    n.doc = this;

    if ( src.charAt( 0 ) == '/' )
	{
        n.nodeType = 'CLOSE';
        src = src.substring( 1 );
    }
    else
	{
        // otherwise it's an open tag (possibly an empty element)
        n.nodeType = 'OPEN';
    }

    // if the last character is a /, check it's not a CLOSE tag
    if ( src.charAt( src.length - 1 ) == '/' )
	{
        if ( n.nodeType == 'CLOSE' )
			return Base.raiseError( "Singleton close tag." );
        else
            n.nodeType = 'SINGLE';

        // strip off the last character
        src = src.substring( 0, src.length - 1 );
    }

    // set up the properties as appropriate
    if ( n.nodeType != 'CLOSE' )
        n.attributes = new Array();

    if ( n.nodeType == 'OPEN' )
        n.children = new Array();

    // trim the whitespace off the remaining content
    src = XML.trim( src, true, true );

    // chuck out an error if there's nothing left
    if ( src.length == 0 )
		return Base.raiseError( "Empty tag." );

    // scan forward until a space...
    var endOfName = XML.firstWhiteChar( src, 0 );

    // if there is no space, this is just a name (e.g. (<tag>, <tag/> or </tag>
    if ( endOfName == -1 )
	{
        n.tagName = src;
        return n;
    }

    // otherwise, we should expect attributes - but store the tag name first
    n.tagName = src.substring( 0, endOfName );

    // start from after the tag name
    var pos = endOfName;

    while ( pos < src.length )
	{
        pos = this.parseAttribute( src, pos, n );
		
        if ( this.pos == 0 )
            return null;
    }
	
    return n;
};

/**
 * @access public
 */
XMLDoc.prototype.removeNodeFromTree = function( node )
{
    var parentXMLText = this.getUnderlyingXMLText();
    var selectedNodeXMLText = node.getUnderlyingXMLText();
    var originalNodePos = parentXMLText.indexOf( selectedNodeXMLText );
    var newXML = parentXMLText.substr( 0, originalNodePos );
    newXML += parentXMLText.substr( originalNodePos + selectedNodeXMLText.length );
    var newDoc = new XMLDoc( newXML );
    
	return newDoc;
};

/**
 * @access public
 */
XMLDoc.prototype.replaceNodeContents = function( referenceNode, newContents )
{  
    var newNode = this.createXMLNode( "<X>" + newContents + "</X>" );
    referenceNode.children = newNode.children;
    
	return this;
};
