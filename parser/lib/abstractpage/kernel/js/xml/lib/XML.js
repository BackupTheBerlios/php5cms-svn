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
XML = function()
{
	this.Base = Base;
	this.Base();
};


XML.prototype = new Base();
XML.prototype.constructor = XML;
XML.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
XML.whitespace = "\n\r\t ";

/**
 * @access public
 * @static
 */
XML.quotes = "\"'";

/**
 * @access public
 * @static
 */
XML.convertEscapes = function( str )
{
    var gt;

    // &lt;
    gt = -1;
    while ( str.indexOf( "&lt;", gt + 1 ) > -1 )
	{
        var gt = str.indexOf( "&lt;", gt + 1 );
        var newStr = str.substr( 0, gt );
		
        newStr += "<";
        newStr  = newStr + str.substr( gt + 4, str.length );
        str     = newStr;
    }

    // &gt;
    gt = -1;
    while ( str.indexOf( "&gt;", gt + 1 ) > -1 )
	{
        var gt = str.indexOf( "&gt;", gt + 1 );
        var newStr = str.substr( 0, gt );
		
        newStr += ">";
        newStr  = newStr + str.substr( gt + 4, str.length );
        str     = newStr;
    }

    // &amp;
    gt = -1;
    while ( str.indexOf( "&amp;", gt + 1 ) > -1 )
	{
        var gt = str.indexOf( "&amp;", gt + 1 );
        var newStr = str.substr( 0, gt );
		
        newStr += "&";
        newStr  = newStr + str.substr( gt + 5, str.length );
        str     = newStr;
    }

    return str;
};

/**
 * @access public
 * @static
 */
XML.convertToEscapes = function( str )
{
    // start with &
    var gt = -1;
    while ( str.indexOf( "&", gt + 1 ) > -1 )
	{
        gt = str.indexOf( "&", gt + 1 );
        var newStr = str.substr( 0, gt );
		
        newStr += "&amp;";
        newStr  = newStr + str.substr( gt + 1, str.length );
        str     = newStr;
    }

    // now <
    gt = -1;
    while ( str.indexOf( "<", gt + 1 ) > -1 )
	{
        var gt = str.indexOf( "<", gt + 1 );
        var newStr = str.substr( 0, gt );
		
        newStr += "&lt;";
        newStr  = newStr + str.substr( gt + 1, str.length );
        str     = newStr;
    }

    // now >
    gt = -1;
    while ( str.indexOf( ">", gt + 1 ) > -1 )
	{
        var gt = str.indexOf( ">", gt + 1 );
        var newStr = str.substr( 0, gt );
		
        newStr += "&gt;";
        newStr  = newStr + str.substr( gt + 1, str.length );
        str     = newStr;
    }

    return str;
};

/**
 * @access public
 * @static
 */
XML.firstWhiteChar = function( str, pos )
{
    if ( XML.isEmpty( str ) )
        return -1;

    while ( pos < str.length )
	{
        if ( XML.whitespace.indexOf( str.charAt( pos ) ) != -1 )
            return pos;
        else
            pos++;
    }
	
    return str.length;
};

/**
 * @access public
 * @static
 */
XML.isEmpty = function( str )
{
    return ( str == null ) || ( str.length == 0 );
};

/**
 * @access public
 * @static
 */
XML.trim = function( trimString, leftTrim, rightTrim )
{
    if ( XML.isEmpty( trimString ) )
        return "";

    // the general focus here is on minimal method calls - hence only one
    // substring is done to complete the trim.

    if ( leftTrim == null )
        leftTrim = true;

    if ( rightTrim == null )
        rightTrim = true;

    var left  = 0;
    var right = 0;
    var i = 0;
    var k = 0;

    // modified to properly handle strings that are all whitespace
    if ( leftTrim == true )
	{
        while ( ( i < trimString.length ) && ( XML.whitespace.indexOf( trimString.charAt( i++ ) ) != -1 ) )
            left++;
    }
	
    if ( rightTrim == true )
	{
        k = trimString.length-1;
        
		while ( ( k >= left ) && ( XML.whitespace.indexOf( trimString.charAt( k-- ) ) != -1 ) )
            right++;
    }
	
    return trimString.substring( left, trimString.length - right );
};


// private methods

/**
 * @access private
 * @static
 */
XML._displayElement = function( domElement, strRet )
{
    if ( domElement == null )
        return;
    
    if ( !( domElement.nodeType == 'ELEMENT' ) )
		return;

    var tagName = domElement.tagName;
    var tagInfo = "";
    tagInfo     = "<" + tagName;

    // attributes
    var attributeList = domElement.getAttributeNames();

    for ( var intLoop = 0; intLoop < attributeList.length; intLoop++ )
	{
        var attribute = attributeList[intLoop];
        tagInfo = tagInfo + " "  + attribute + "=";
        tagInfo = tagInfo + "\"" + domElement.getAttribute( attribute ) + "\"";
    }

    tagInfo = tagInfo + ">";
    strRet  = strRet  + tagInfo;

    // children
    if ( domElement.children != null )
	{
        var domElements = domElement.children;
        for ( var intLoop = 0; intLoop < domElements.length; intLoop++ )
		{
            var childNode = domElements[intLoop];
            
			if ( childNode.nodeType == 'COMMENT' )
			{
                strRet = strRet + "<!--" + childNode.content + "-->";
            }
            else if ( childNode.nodeType == 'TEXT' )
			{
                var cont = XML.trim( childNode.content, true, true );
                strRet = strRet + childNode.content;
            }
            else if ( childNode.nodeType == 'CDATA' )
			{
                var cont = XML.trim( childNode.content, true, true );
                strRet = strRet + "<![CDATA[" + cont + "]]>";
            }
            else
			{
                strRet = XML._displayElement( childNode, strRet );
            }
		}
	}

	// ending tag
	strRet = strRet + "</" + tagName + ">";
    return strRet;
};
