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
DOMHelper = function()
{
	this.Base = Base;
	this.Base();
	
	var ie55 = /msie 5\.[56789]/i.test( navigator.userAgent );

	// IE55 has a serious DOM1 bug... Patch it!
	if ( ie55 )
	{
		document._getElementsByTagName = document.getElementsByTagName;
		document.getElementsByTagName  = function ( sTagName )
		{
			if ( sTagName == "*" )
				return document.all;
			else
				return document._getElementsByTagName( sTagName );
		};
	}
};


DOMHelper.prototype = new Base();
DOMHelper.prototype.constructor = DOMHelper;
DOMHelper.superclass = Base.prototype;

/**
 * @access public
 */
DOMHelper.prototype.getElementById = function( id )
{
	return d.getElementById && d.getElementById( id ) || d.all && d.all( id );
};

/**
 * @access public
 */
DOMHelper.prototype.getElementsByTagName = function( tagName, parentElement )
{
	var d = parentElement? parentElement : document;
	return d.getElementsByTagName && d.getElementsByTagName( tagName ) || d.all && d.all.tags( tagName );
};

/**
 * @access public
 */
DOMHelper.prototype.getElementsByClassName = function( ClassName, tagName, parentElement )
{
	var elements = new Array();
	var d = parentElement? parentElement : document;
	var allElements;

	if ( tagName )
		allElements = d.all && d.all.tags( tagName ) || d.getElementsByTagName && d.getElementsByTagName( tagName );
	else
		allElements = d.all || d.getElementsByTagName( "*" );
 
	for ( var i = 0, len = allElements.length; i < len; i++ )
	{
		if ( allElements[i].className == ClassName )
			elements[elements.length] = allElements[i];
	}

	return elements;
};

/**
 * @access public
 */
DOMHelper.prototype.getClassList = function( tagName, parentElement )
{
	var elements = new Array();
	var d = parentElement? parentElement : document;
	var allElements;

	if ( tagName )
		allElements = d.all && d.all.tags( tagName ) || d.getElementsByTagName && d.getElementsByTagName( tagName );
	else
		allElements = d.all || d.getElementsByTagName( "*" );
		
	for ( var i = 0, len = allElements.length; i < len; i++ )
	{
		var cN = allElements[i].className;

		if ( cN )
		{
			if ( elements[cN] )
			{
				elements[cN][elements[cN].length] = allElements[i];
			} 
			else
			{
				elements[cN] = new Array();
				elements[cN][0] = allElements[i];
			}
		} 
	}

	return elements;
};


/**
 * Returns an array of element objects from the current document
 * matching the CSS selector. Selectors can contain element names, 
 * class names and ids and can be nested. For example:
 *    
 * elements = document.getElementsBySelect('div#main p a.external')
 *   
 * Will return an array of all 'a' elements with 'external' in their 
 * class attribute that are contained inside 'p' elements that are 
 * contained inside the 'div' element which has id="main"
 *
 * Works in Phoenix 0.5, Mozilla 1.3, Opera 7, Internet Explorer 6, 
 * Internet Explorer 5 on Windows. Opera 7 fails.
 *
 * @access public
 */
document.getElementsBySelector = function( selector ) 
{
  	// Attempt to fail gracefully in lesser browsers
  	if ( !document.getElementsByTagName )
    	return new Array();
  
  	// Split selector in to tokens
  	var tokens = selector.split( ' ' );
  	var currentContext = new Array( document );
  
  	for ( var i = 0; i < tokens.length; i++ ) 
	{
    	token = tokens[i].replace( /^\s+/, '' ).replace( /\s+$/, '' );
    
		if ( token.indexOf( '#' ) > -1 ) 
		{
      		// Token is an ID selector
      		var bits    = token.split( '#' );
      		var tagName = bits[0];
      		var id      = bits[1];
      		var element = document.getElementById( id );
      
	  		if ( tagName && element.nodeName.toLowerCase() != tagName ) 
			{
        		// tag with that ID not found, return false
        		return new Array();
      		}
      
	  		// Set currentContext to contain just this element
      		currentContext = new Array( element );
      		continue; // Skip to next token
    	}
    
		if ( token.indexOf( '.' ) > -1 ) 
		{
      		// Token contains a class selector
      		var bits      = token.split( '.' );
      		var tagName   = bits[0];
      		var className = bits[1];
      
	  		if ( !tagName )
        		tagName = '*';
      
      		// Get elements matching tag, filter them for class selector
      		var found = new Array;
      		var foundCount = 0;
      
	  		for ( var h = 0; h < currentContext.length; h++ ) 
			{
        		var elements;
        
				if ( tagName == '*' )
            		elements = currentContext[h].all? currentContext[h].all : currentContext[h].getElementsByTagName( '*' ); // IE workaround
        		else
            		elements = currentContext[h].getElementsByTagName( tagName );
        
        		for ( var j = 0; j < elements.length; j++ ) 
					found[foundCount++] = elements[j];
      		}
      
	  		currentContext = new Array;
      		var currentContextIndex = 0;
      
	  		for ( var k = 0; k < found.length; k++ ) 
			{
        		if ( found[k].className && found[k].className.match( new RegExp( '\\b' + className + '\\b' ) ) )
          			currentContext[currentContextIndex++] = found[k];
      		}
      
	  		continue; // Skip to next token
    	}
    
		/* 
		 * Code to deal with attribute selectors.
		 *
		 * That revolting regular expression explained:
		 *
		 * /^(\w+)\[(\w+)([=~\|\^\$\*]?)=?"?([^\]"]*)"?\]$/
		 *   \---/  \---/\-------------/    \-------/
		 *     |      |         |               |
 		 *     |      |         |           The value
		 *     |      |    ~,|,^,$,* or =
		 *     |   Attribute 
		 *    Tag
		 */
    	if ( token.match( /^(\w*)\[(\w+)([=~\|\^\$\*]?)=?"?([^\]"]*)"?\]$/) ) 
		{
      		var tagName      = RegExp.$1;
      		var attrName     = RegExp.$2;
      		var attrOperator = RegExp.$3;
      		var attrValue    = RegExp.$4;
      
	  		if ( !tagName ) 
				tagName = '*';
      
      		// Grab all of the tagName elements within current context
      		var found = new Array;
      		var foundCount = 0;

			for ( var h = 0; h < currentContext.length; h++ ) 
			{
        		var elements;
        
				if ( tagName == '*' )
            		elements = currentContext[h].all? currentContext[h].all : currentContext[h].getElementsByTagName( '*' ); // IE workaround
        		else
            		elements = currentContext[h].getElementsByTagName( tagName );
        
        		for ( var j = 0; j < elements.length; j++ )
          			found[foundCount++] = elements[j];
      		}
      
	  		currentContext = new Array;
      		var currentContextIndex = 0;
      		var checkFunction; // This function will be used to filter the elements
      
	  		switch ( attrOperator ) 
			{
        		case '=': // Equality
          			checkFunction = function( e ) 
					{ 
						return ( e.getAttribute(attrName) == attrValue ); 
					};
          
		  			break;
        
				case '~': // Match one of space seperated words 
          			checkFunction = function( e ) 
					{
					 	return ( e.getAttribute( attrName ).match( new RegExp( '\\b' + attrValue + '\\b' ) ) ); 
					};
          
		  			break;
        
				case '|': // Match start with value followed by optional hyphen
          			checkFunction = function( e ) 
					{
					 	return ( e.getAttribute( attrName ).match( new RegExp( '^' + attrValue + '-?' ) ) ); 
					};
          
		  			break;
        
				case '^': // Match starts with value
          			checkFunction = function( e ) 
					{ 
						return ( e.getAttribute( attrName ).indexOf( attrValue ) == 0 ); 
					};
          			
					break;
        
				case '$': // Match ends with value - fails with "Warning" in Opera 7
          			checkFunction = function( e ) 
					{ 
						return ( e.getAttribute( attrName ).lastIndexOf( attrValue ) == e.getAttribute( attrName ).length - attrValue.length ); 
					};
          			
					break;
        		
				case '*': // Match ends with value
          			checkFunction = function( e ) 
					{ 
						return ( e.getAttribute( attrName ).indexOf( attrValue ) > -1 ); 
					};
          			
					break;
        		
				default :
          			// Just test for existence of attribute
          			checkFunction = function( e ) 
					{ 
						return e.getAttribute( attrName ); 
					};
      		}
      		
			currentContext = new Array;
     	 	var currentContextIndex = 0;
      
	  		for ( var k = 0; k < found.length; k++ ) 
			{
        		if ( checkFunction( found[k] ) )
          			currentContext[currentContextIndex++] = found[k];
      		}

      		continue; // Skip to next token
    	}
    
		// If we get here, token is JUST an element (not a class or ID selector)
    	tagName = token;
    	var found = new Array;
    	var foundCount = 0;
    
		for ( var h = 0; h < currentContext.length; h++ ) 
		{
      		var elements = currentContext[h].getElementsByTagName( tagName );
      
	  		for ( var j = 0; j < elements.length; j++ )
        		found[foundCount++] = elements[j];
    	}
    
		currentContext = found;
  	}
  
  	return currentContext;
};
