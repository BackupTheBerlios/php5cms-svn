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
 * @package xml_dom
 */
 
/**
 * Constructor
 *
 * @access public
 */
DOMElement = function( i, s )
{
	this.Base = Base;
	this.Base();
	
	this.elm = document.getElementById( i );
	
	if ( !this.elm )
		this.elm = document.createElement( "DIV" );
	
	this.elm.id = i;

	if ( !s )
		this.elm.style.position = 'absolute';
	else
		this.elm.className = s;

	this.style = this.elm.style;
	return this;
};


DOMElement.prototype = new Base();
DOMElement.prototype.constructor = DOMElement;
DOMElement.superclass = Base.prototype;

/**
 * @access public
 */
DOMElement.prototype.setHTML = function( s )
{
	this.elm.innerHTML = s;
};

/**
 * @access public
 */
DOMElement.prototype.appendChild = function( o )
{
	this.elm.appendChild( o.elm );
};

/**
 * @access public
 */
DOMElement.prototype.setXY = function( x, y )
{
	this.style.left = x;
	this.style.top  = y;
};

/**
 * @access public
 */
DOMElement.prototype.setWH = function( w, h )
{
	this.style.width  = w;
	this.style.height = h;
};

/**
 * @access public
 */
DOMElement.prototype.getX = function()
{
	return this.elm.offsetLeft;
};

/**
 * @access public
 */
DOMElement.prototype.getY = function()
{
	return this.elm.offsetTop;
};

/**
 * @access public
 */
DOMElement.prototype.getW = function()
{
	return this.elm.offsetWidth;
};

/**
 * @access public
 */
DOMElement.prototype.getH = function()
{
	return this.elm.offsetHeight;
};

/**
 * @access public
 */
DOMElement.prototype.killEvents = function()
{
	this.elm.onmouseup = function( e )
	{
		event.cancelBubble = true;
	}
	this.elm.onmouseout = function( e )
	{
		event.cancelBubble = true;
	}
	this.elm.onmouseover = function( e )
	{
		event.cancelBubble = true;
	}
	this.elm.onmousedown = function( e )
	{
		event.cancelBubble = true;
	}
	this.elm.oncontextmenu = function()
	{
		return false;
	}
};

/**
 * @access public
 */
DOMElement.prototype.addEventListener = function( evType, fn )
{
	if ( evType.indexOf( 'mouse' ) == 0)
		return this.elm.attachEvent( "on" + evType, fn );
	else
		eval( this["on" + evType] = fn );
};

/**
 * @access public
 */
DOMElement.prototype.removeEventListener = function( evType, fn )
{
	if ( evType.indexOf( 'mouse' ) == 0 )
		return this.elm.detachEvent( "on" + evType, fn );
	else
		eval( this["on" + evType] = null );
};

/**
 * @access public
 */
DOMElement.prototype.invokeEvent = function( evType, args )
{
	var ret = true;
	
	if ( this["on" + evType] )
		ret = this["on" + evType]( args );
	
	if ( ret && this.parent )
		this.parent.invokeEvent( evType, args );
};


/**
 * @access public
 * @static
 */
DOMElement.getContainerLayerOf = function( element )
{
	if ( !element )
		return null;

	while ( ( element.tagName != 'DIV' ) && element.parentDOMElement && ( element.parentDOMElement != element ) )
		element = element.parentDOMElement;

	return element;
};

/**
 * Given a selector string, return a style object by searching
 * through stylesheets. Return null if none found (NS6 only)
 *
 * @access public
 * @static
 */
DOMElement.getStyleBySelector = function( selector )
{
	if ( !Browser.ns6 )
		return null;
		 
	var sheetList = document.styleSheets;
	var ruleList;
	var i, j;

 	// look through stylesheets in reverse order that
	// they appear in the document
	for ( var i = sheetList.length - 1; i >= 0; i-- )
	{
		ruleList = sheetList[i].cssRules;
		for ( var j = 0; j < ruleList.length; j++ )
		{
			if ( ruleList[j].type == CSSRule.STYLE_RULE && ruleList[j].selectorText == selector )
				return ruleList[j].style;
        }
    }
	
    return null;
};

/**
 * Given an id and a property (as strings), return the given
 * property of that id.  Navigator 6 will first look for the
 * property in a tag; if not found, it will look through the
 * stylesheet. Note: do not precede the id with a # -- it
 * will be appended when searching the stylesheets.
 *
 * @access public
 * @static
 */
DOMElement.getIdProperty = function( id, property )
{
	if ( Browser.ns6 )
	{
		var styleObject = document.getElementById( id );
		if ( styleObject != null )
		{
			styleObject = styleObject.style;
			
			if ( styleObject[property] )
				return styleObject[ property ];
		}
		styleObject = DOMElement.getStyleBySelector( "#" + id );
		
        return ( styleObject != null )? styleObject[property] : null;
	}
	else if ( Browser.ns4 )
	{
        return document[id][property];
    }
	else if ( Browser.ie )
    {
        return document.all[id].style[property];
    }
	else
	{
		return null;
	}
};

/**
 * Given an id and a property (as strings), set the given
 * property of that id to the value provided. The property
 * is set directly on the tag, not in the stylesheet.
 *
 * @access public
 * @static
 */
DOMElement.setIdProperty = function( id, property, value )
{
	if ( Browser.ns6 )
    {
		var styleObject = document.getElementById( id );
		
		if ( styleObject != null )
        {
            styleObject = styleObject.style;
            styleObject[ property ] = value;
        }
    }
    else if ( Browser.ns4 )
    {
		document[id][property] = value;
    }
    else if ( Browser.ie )
	{
		document.all[id].style[property] = value;
    }
};

/**
 * Return a division's document.
 *
 * @access public
 * @static
 */
DOMElement.getDocument = function( divName )
{
	var doc;

    if ( Browser.ns4 )
		doc = window.document[divName].document;
    else if ( Browser.ns6 )
		doc = document;
    else if ( Browser.ie )
		doc = document;
		
    return doc;
};
