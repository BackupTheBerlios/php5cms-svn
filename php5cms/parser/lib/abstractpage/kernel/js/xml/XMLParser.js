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
 * @package xml
 */
 
/**
 * Constructor
 *
 * @access public
 */
XMLParser = function( parser )
{
	this.Base = Base;
	this.Base();
	
	this.error_code   = 0;
	this.error_line   = 0;
	this.error_reason = "";
	this.has_errors   = false;

	// for callbacks
	this.onerror = new Function;
	
	try
	{
		this.parser = new ActiveXObject( parser || XMLParser.getControlPrefix() + ".DomDocument" );
	}
	catch( e )
	{
		this.parser = null;
	}
};


XMLParser.prototype = new Base();
XMLParser.prototype.constructor = XMLParser;
XMLParser.superclass = Base.prototype;

/**
 * @access public
 */
XMLParser.prototype.loadXML = function( src )
{
	if ( !this.parser )
		return;
		
	var parsed = this.parser.load( src );

	if ( parsed == false )
	{
		var pe   = this.parser.parseError;
		var code = ( pe.errorCode ^ 0x80000000 ) & 0xFFFFFFF;
		
		this.setErrorCode( code.toString( 16 ).toUpperCase() );
		this.setErrorLine( pe.line );
		this.setErrorReason( pe.reason );
		this.has_errors = true;
		
		this.onerror(
			this.getErrorCode(),
			this.getErrorLine(),
			this.getErrorReason()
		);
		
		return;
	}
	
	this.has_errors = false;
};

/**
 * @access public
 */
XMLParser.prototype.loadXMLFailsafe = function( src, callback )
{
	xmlHttp = new ActiveXObject( XMLParser.getControlPrefix() + ".XmlHttp" );
	xmlHttp.open( "GET", src, true );
	
	_xmlparser = this.parser;
	
	xmlHttp.onreadystatechange = function()
	{
		if ( xmlHttp.readyState == 4 )
		{
			var xmlDocument = _xmlparser;
    		xmlDocument.async = false;
    		xmlDocument.loadXML( xmlHttp.responseText );
			
			if ( ( callback != null ) && ( typeof callback == "function" ) )
				callback( xmlDocument, xmlHttp );
			
			return true;
		}
	};
	
	// call in new thread to allow ui to update
	window.setTimeout( function()
	{
		xmlHttp.send( null );
	}, 10 );
};

/**
 * @access public
 */
XMLParser.prototype.setValidateOnParse = function( b )
{
	this.parser.validateOnParse = b || false;
};

/**
 * @access public
 */
XMLParser.prototype.setASync = function( b )
{
	this.parser.async = b || false;
};

/**
 * @access public
 */
XMLParser.prototype.setProperty = function( prop, val )
{
	if ( ( prop == null ) || ( val == null ) )
		return;
		
	this.parser.setProperty( prop, val );
};

/**
 * @access public
 */
XMLParser.prototype.getProperty = function( prop  )
{
	return this.parser.getProperty( prop );
};

/**
 * @access public
 */
XMLParser.prototype.setErrorCode = function( code )
{
	this.error_code = code;
};

/**
 * @access public
 */
XMLParser.prototype.getErrorCode = function()
{
	return this.error_code;
};

/**
 * @access public
 */
XMLParser.prototype.setErrorLine = function( line )
{
	this.error_line = line
};

/**
 * @access public
 */
XMLParser.prototype.getErrorLine = function()
{
	return this.error_line;
};

/**
 * @access public
 */
XMLParser.prototype.setErrorReason = function( reason )
{
	this.error_reason = reason;
};

/**
 * @access public
 */
XMLParser.prototype.getErrorReason = function()
{
	return this.error_reason;
};

/**
 * @access public
 */
XMLParser.prototype.hasErrors = function()
{
	return this.has_errors;
};

/**
 * @access public
 */
XMLParser.prototype.getDocumentElement = function()
{
	if ( this.hasErrors() && !this.parser )
		return false;
		
	return this.parser.documentElement;
};

/**
 * @access public
 */
XMLParser.prototype.getPlain = function()
{
	if ( this.hasErrors() && !this.parser )
		return false;
		
	return this.parser.xml;
};

/**
 * @access public
 */
XMLParser.prototype.getChildNodes = function( name )
{
	var i;
	var nList = this.parser.documentElement.childNodes;
	
	for ( i = 0; i < nList.length; i++ )
	{
		if ( nList.item( i ).nodeName == name )
			return nList.item( i );
	}
};

/**
 * Override.
 *
 * @access public
 */
XMLParser.prototype.parseXML = function( node )
{
	if ( node == null)
		node = this.parser;
		
	switch ( node.nodeType )
	{
		case 1: // NODE_ELEMENT
			/*
			// Example code
			// process attributes here...
			if ( node.attributes.length != 0 ) 
			{
				var i = 0;
				
				for ( i = 0; i < node.attributes.length; i++ )
					out += node.attributes( i ).nodeName + "='" + node.attributes( i ).nodeValue + "'";
			}
		
			// process children here...
			if ( node.hasChildNodes() ) 
			{
				for ( i = 0; i < node.childNodes.length; i++ )
					this.parseXML( node.childNodes( i ) );
			}
			else 
			{
			}
			*/
			
			break;
			
		case 3: // TEXT_NODE
			break;
		
		case 4: // CDATA_SECTION_NODE
			break;
		
		case 7: // PROCESSING_INSTRUCTION_NODE
			if ( node.nodeName == "xml" )
				break;
			
			break;
		
		case 8: // COMMENT_NODE
			break;
		
		case 9: // DOCUMENT_NODE
			for ( var i = 0; i < node.childNodes.length; i++ )
				this.parseXML( node.childNodes( i ) );
				
			break;
		
		default: // error
			break;	
	}
	
	return out;
};


/**
 * Used to find the Automation server name.
 *
 * @access public
 * @static
 */
XMLParser.getControlPrefix = function()
{
	if ( XMLParser.getControlPrefix.prefix )
		return XMLParser.getControlPrefix.prefix;
	
	var prefixes = [ "MSXML2", "Microsoft", "MSXML", "MSXML3", "MSXML4" ];
	var o, o2;
	
	for ( var i = 0; i < prefixes.length; i++ )
	{
		try
		{
			// try to create the objects
			o  = new ActiveXObject( prefixes[i] + ".XmlHttp"     );
			o2 = new ActiveXObject( prefixes[i] + ".DomDocument" );
			
			return XMLParser.getControlPrefix.prefix = prefixes[i];
		}
		catch ( ex )
		{
			return Base.raiseError( "Could not find an installed XML parser." );
		}
	}
};

/**
 * @access public
 * @static
 */
XMLParser.sniffParser = function()
{
	var res = new Dictionary();
	var xml = "<?xml version=\"1.0\" encoding=\"UTF-16\"?><cjb></cjb>";
	var xsl = "<?xml version=\"1.0\" encoding=\"UTF-16\"?><x:stylesheet version=\"1.0\" xmlns:x=\"http://www.w3.org/1999/XSL/Transform\" xmlns:m=\"urn:schemas-microsoft-com:xslt\"><x:template match=\"/\"><x:value-of select=\"system-property('m:version')\" /></x:template></x:stylesheet>";
	var x   = null;
	    
	try
	{ 
	    x = new ActiveXObject( "Msxml2.DOMDocument" ); 
	    x.async = false;
		
	    if ( x.loadXML( xml ) )
			res.add( "Msxml2.DOMDocument", true );
	}
	catch ( e )
	{
		res.add( "Msxml2.DOMDocument", false );
	}
	 
	try
	{ 
	    x = new ActiveXObject( "Msxml2.DOMDocument.2.6" ); 
	    x.async = false;
	    
		if ( x.loadXML( xml ) ) 
	    	res.add( "Msxml2.DOMDocument.2.6", true );
	}
	catch ( e )
	{
		res.add( "Msxml2.DOMDocument.2.6", false );
	} 

	try
	{ 
	    x = new ActiveXObject( "Msxml2.DOMDocument.3.0" ); 
	    x.async = false;
		
	    if ( x.loadXML( xml ) ) 
	    	res.add( "Msxml2.DOMDocument.3.0", true );
	}
	catch ( e )
	{
		res.add( "Msxml2.DOMDocument.3.0", false );
	}

	try
	{ 
	    x = new ActiveXObject( "Msxml2.DOMDocument.4.0" ); 
	    x.async = false;
		
	    if ( x.loadXML( xml ) ) 
	   		res.add( "Msxml2.DOMDocument.4.0", true );
	}
	catch ( e )
	{
		res.add( "Msxml2.DOMDocument.4.0", false );
	}

	try
	{ 
	    x = new ActiveXObject( "Microsoft.XMLDOM" );  
	    x.async = false;
	    
		if ( x.loadXML( xml ) )
	    	res.add( "Microsoft.XMLDOM", true );
	}
	catch ( e )
	{
		res.add( "Microsoft.XMLDOM", false );
	} 

	try
	{
		var s = new ActiveXObject( "Microsoft.XMLDOM" ); 
		s.async = false;
		
		if ( s.loadXML( xsl ) )
		{
			try
			{
				var op = x.transformNode( s );
				
				if ( op.indexOf( "stylesheet" ) == -1 )
					res.add( "ReplaceReason", "Replace V" + op.substr( op.lastIndexOf( ">" ) + 1 ) );
			}
			catch ( e )
			{
				res.add( "ReplaceReason", "Side-By-Side" );
			}
		}
	}
	catch ( e )
	{
	}
	
	return res;
};
