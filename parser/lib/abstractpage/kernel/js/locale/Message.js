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
 * @package locale
 */
 
/**
 * Constructor
 *
 * @access public
 */
Message = function()
{
	this.Base = Base;
	this.Base();
};


Message.prototype = new Base();
Message.prototype.constructor = Message;
Message.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
Message.language = null;

/**
 * @access public
 * @static
 */
Message.prompts = new Object();

/**
 * @access public
 * @static
 */
Message.varPrefix = "$_";

/**
 * @access public
 * @static
 */
Message.varSuffix = "_$";

/**
 * @access public
 * @static
 */
Message.pattern = /(\$_[A-Z0-9_]*_\$)/g;

/**
 * @access public
 * @static
 */
Message.xmlPath = "i18n/";

/**
 * @access public
 * @static
 */
Message.defaultLang = "de";

/**
 * @access public
 * @static
 */
Message.sessionVarName = "user_language";

/**
 * @access public
 * @static
 */
Message.invalidHandle  = "INVALIDHANDLE";


/**
 * @access public
 * @static
 */
Message.process = function( xmlfile, lang )
{
	var file;
	
	// eval file
	if ( xmlfile != null )
	{
		file = xmlfile;	
	}
	else
	{
		var loc  = document.location.href;
		var file = loc.substring( loc.lastIndexOf( "/" ) + 1, loc.lastIndexOf( "." ) );
		file = Message.xmlPath + file + ".xml";
	}
	
	// eval lang
	if ( lang != null )
	{
		Message.language = lang;
	}
	else
	{
		var sd = new SessionData();
		
		Message.language = sd.has( Message.sessionVarName )?
			sd.get( Message.sessionVarName ) :
			Message.defaultLang;
	}
	
	var parser = new XMLParser();
	parser.loadXMLFailsafe( file, function( xmlparser )
	{
		Message._storePrompts( xmlparser );
		Message._substitute();
	} );
};

/**
 * @access public
 * @static
 */
Message.getMessage = function( which )
{
	handle = Message.varPrefix + which + Message.varSuffix;
	
	return Message.prompts[handle]?
		Message.prompts[handle][Message.language] :
		Message.invalidHandle;
};

/**
 * Replaces all occurencies of handles in a given string.
 *
 * @access public
 * @static
 */
Message.substituteString = function( str )
{
	if ( ( str == "" ) || ( str == null ) || ( typeof str != "string" ) )
		return "";

	var handle, text;
	
	while ( Message.pattern.test( str ) )
	{
		handle = str.substring( str.indexOf( Message.varPrefix ), str.indexOf( Message.varSuffix ) + Message.varSuffix.length );
		text   = Message._getMessage( handle );
		str    = str.replace( handle, text );
	}
	
	return str;
};


// private methods

/**
 * @access private
 * @static
 */
Message._storePrompts = function( xmlDoc )
{
	var children, handle, lang, text;
	var nodes = xmlDoc.documentElement.childNodes;
	
	// walk document and store prompts
	for ( var i = 0; i < nodes.length; i++ )
	{
		// continue if comment or so...
		if ( nodes.item( i ).nodeType != 1 )
			continue;
		
		handle = nodes.item( i ).getAttribute( "id" );
		
		// handle has no id
		if ( !handle )
			continue;

		// add prefix and suffix
		handle = Message.varPrefix + handle + Message.varSuffix;
		
		Message.prompts[handle] = new Object();
		children = nodes.item( i ).childNodes;
		
		for ( var j = 0; j < children.length; j++ )
		{
			lang = children.item( j ).getAttribute( "lang" );
			text = children.item( j ).text;
			
			Message.prompts[handle][lang] = text;
		}
	}
};

/**
 * Used internally to substitute child nodes of body automatically.
 *
 * @access public
 * @static
 */
Message._substitute = function( nodes )
{
	var i, j, node, value, handle, text;
	var nd = nodes || document.getElementsByTagName( "BODY" ).item( 0 ).childNodes;
	
	for ( i = 0; i < nd.length; i++ )
	{
		node = nd[i];
		
		// traversing attributes
		if ( ( node.nodeType == 1 ) && ( node.attributes.length != 0 ) )
		{
			for ( j = 0; j < node.attributes.length; j++ )
			{
				// sniff values for pattern
				if ( node.attributes( j ).nodeValue && Message.pattern.test( node.attributes( j ).nodeValue ) )
				{
					value  = new String( node.attributes( j ).nodeValue );
					handle = value.substring( value.indexOf( Message.varPrefix ), value.indexOf( Message.varSuffix ) + Message.varSuffix.length );
					text   = Message._getMessage( handle );
					
					node.attributes( j ).nodeValue = value.replace( handle, text );
				}
			}
		}
		
		// sniffing content
		while ( Message.pattern.test( node.nodeValue ) )
		{			
			value  = new String( node.nodeValue );
			handle = value.substring( value.indexOf( Message.varPrefix ), value.indexOf( Message.varSuffix ) + Message.varSuffix.length );
			text   = Message._getMessage( handle );
					
			if ( text )
				node.nodeValue = value.replace( handle, text );
		}
		
		// processing child nodes
		if ( node.hasChildNodes() )
			Message._substitute( node.childNodes );
	}
};

/**
 * @access private
 * @static
 */
Message._getMessage = function( handle )
{	
	return Message.prompts[handle]?
		Message.prompts[handle][Message.language] :
		Message.invalidHandle;
};
