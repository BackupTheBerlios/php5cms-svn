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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


/**
 * @package db_tamino
 */
 
/**
 * TaminoClient Object (for IE5.0)
 */

/* Microsoft XMLDOM node constants */
var NODE_ELEMENT                = 1;
var NODE_ATTRIBUTE              = 2; 
var NODE_TEXT                   = 3; 
var NODE_CDATA_SECTION          = 4; 
var NODE_ENTITY_REFERENCE       = 5; 
var NODE_ENTITY                 = 6; 
var NODE_PROCESSING_INSTRUCTION = 7; 
var NODE_COMMENT                = 8; 
var NODE_DOCUMENT               = 9; 
var NODE_DOCUMENT_TYPE          = 10; 
var NODE_DOCUMENT_FRAGMENT      = 11; 
var NODE_NOTATION               = 12; 


/**
 * Constructor
 *
 * @access public
 */
TaminoClient = function()
{
	this.Base = Base;
	this.Base();
	
	// define properties
	this.taminoAPIIndex = TaminoClient.taminoAPIIndex;
	this.XMLDB          = ""; 
	this.securePath     = "";
	this.lastQuery      = ""; // f2
	this.pageSize       = 5;
	this.user           = "";
	this.password       = "";
	this.transaction    = null;
	this.xmlHeader      = '<?xml version="1.0"?>'; // default - UTF-8;
	this.userAgent      = TaminoClient.inoUserAgent + TaminoClient.inoVersion + '/' + TaminoClient.inoPatchLevel;
	this.acceptLanguage = "en";
	this.charset        = "utf-8";
	this.acceptCharset  = "utf-8";
	
	var argv = arguments;
	var argc = arguments.length;
	
	if	( argc > 0 )
		this.XMLDB = argv[0];
		
	if	( argc > 1 )
		this.pageSize = arguments[1];
	
	if	( argc > 2 )
		this.user = arguments[2];
	
	if	( argc > 3 )
		this.password = arguments[3];	
};


TaminoClient.prototype = new Base();
TaminoClient.prototype.constructor = TaminoClient;
TaminoClient.superclass = Base.prototype;

/**
 * @access public
 */
TaminoClient.prototype.explainQuery = function( xqlquery, parameter )
{
	var p = "";
	
	if ( parameter ) 
		p = ' ,"' + parameter + '"';
		
	var cursor = "?_XQL=" + this.escape( 'ino:explain(' + xqlquery + p + ')');
	return ( this.queryCursor( cursor ) );
};

/**
 * @access public
 */
TaminoClient.prototype.query = function( xqlquery, offset )
{
	var cursor;
	var pageSize = parseInt( this.pageSize );
	var actualOffset = 1;
	
	if	( offset )
	{
		var i = parseInt( offset );
		
		if	( i > 0 )
			actualOffset = i;
	}
	
	if	( pageSize > 0 )
		cursor = "?_XQL(" + actualOffset + "," + pageSize + ")=" + this.escape( xqlquery );
	else 	
		cursor = "?_XQL=" + this.escape( xqlquery );
		
	if ( arguments.length > 1 )
		return ( this.queryCursor( cursor, arguments[1] ) );
	else	
		return ( this.queryCursor( cursor ) );
};

/**
 * @access public
 */
TaminoClient.prototype.queryCursor = function( cursorQuery )
{
	var extraPath = "";
	
	if ( arguments.length > 1 )
		extraPath = arguments[1];
	
	REQ = new ActiveXObject( TaminoClient.XMLHTTP );
	
	var match = cursorQuery.match( /\?(.*)/ );
	var postCursorQuery = match[1]; // remove the ? to go in post body
	
	this.lastQuery = this.XMLDB + extraPath + cursorQuery;
	REQ.open( "POST", this.XMLDB + extraPath, false, this.user, this.password );
	var session = this.tEncoded( REQ );
	
	if ( session != "" )  
		session = "&" + session;
	
	REQ.setRequestHeader( "User-Agent",      this.userAgent      );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	REQ.setRequestHeader( "Accept-Charset",  this.acceptCharset  );
	// REQ.setRequestHeader( "Cache-Control", "no-cache" );
	REQ.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded; charset=" + this.charset );
	REQ.send( postCursorQuery+session );
	
	var result = new TaminoResult( this, REQ );
	result.lastCursor = cursorQuery;
	
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.querydelete = function( query )
{
	if 	( arguments.length > 1 )
		this.lastQuery = this.XMLDB + arguments[1];
	else   
		this.lastQuery = this.XMLDB + this.securePath;
	
	var clause = this.escape( query );
	REQ=new ActiveXObject( TaminoClient.XMLHTTP );
	REQ.open( "POST", this.lastQuery, false, this.user, this.password );
	var session = this.tEncoded( REQ );
	
	if ( session != "" ) 
		session = session + "&";
		
	REQ.setRequestHeader( "User-Agent",      this.userAgent      );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	REQ.setRequestHeader( "Accept-Charset",  this.acceptCharset  );
	REQ.setRequestHeader( "Content-Type",    "application/x-www-form-urlencoded; charset=" + this.charset );
	REQ.send( session + "_DELETE=" + clause );
	
	var result = new TaminoResult( this, REQ );
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.inodelete = function( node )
{
	if	( node && ( node.nodeType == NODE_ELEMENT || node.nodeType == NODE_DOCUMENT ) )
	{	
		var clause = node.nodeName + '[@ino:id=' + node.getAttribute( "ino:id" ) + ']';

		if 	( arguments.length > 1 )
			return ( this.querydelete( clause, arguments[1] ) );
		else 	
			return ( this.querydelete( clause ) );
	}
	else
	{
		var result = new TaminoResult( this, null );
		result.errorNo   = TaminoClient.deleteNotSpecifiedNo;
		result.errorText = TaminoClient.errorText( TaminoClient.deleteNotSpecifiedNo );
	}
	
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.escape = function( myString )
{
	if ( !this.taminoAPIIndex )
		this.taminoAPIIndex = this.discoverTaminoAPIIndex();

	if ( this.taminoAPIIndex > 1 )
		return ( escape( myString ) );

	var out    = "";
	var length = myString.length;

	for ( var i = 0; i < length; i++ )
	{
		var char = myString.charAt( i );
		var val  = char.charCodeAt( 0 );
		
		if ( val > 255 )
			out = out + "&#" + val + ";";
		else
			out = out + char;
	}

	return ( escape( out ) );
};

/**
 * @access public
 */
TaminoClient.prototype.absoluteURL = function( reurl )
{
	if ( reurl.match( /^\w+:\/\// ) != null ) // if absolute URL
		return ( reurl );
		 
	if ( reurl.match( /^\// ) != null ) // if rebased relative URL
	{
		var m = this.XMLDB.match( /^(\w+:\/\/[^\/]+)/ )
		
		if ( m ) 
			return ( m[1] + reurl );
	}
	
	return ( this.XMLDB + "/" + reurl );
};

/**
 * @access public
 */
TaminoClient.prototype.deleteDocument = function( reurl )
{
	this.lastQuery = this.absoluteURL( reurl );
	REQ = new ActiveXObject( TaminoClient.XMLHTTP );
	REQ.open( "DELETE", this.lastQuery, false, this.user, this.password );
	REQ.setRequestHeader( "User-Agent", this.userAgent );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	var session = this.tEncoded( REQ );
	REQ.send( "" );
	
	var result = new TaminoResult( this, REQ, true );
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.getDocument = function( reurl )
{	
	this.lastQuery = this.absoluteURL( reurl );
	REQ = new ActiveXObject( TaminoClient.XMLHTTP );
	REQ.open( "GET", this.lastQuery, false, this.user, this.password );
	REQ.setRequestHeader( "User-Agent",      this.userAgent      );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	REQ.setRequestHeader( "Accept-Charset",  this.acceptCharset  );
	REQ.setRequestHeader( "Cache-Control",   "no-cache"          );
	var session = this.tEncoded( REQ );
	REQ.send( "" );
	var result = new TaminoResult( this, REQ );
	
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.putDocument = function( reurl, node )
{
	var header;
	this.lastQuery = this.absoluteURL( reurl );
	
	if ( node.nodeType == NODE_ELEMENT )
	{
		header = this.xmlHeader;
	}
	else 	
	{
		if ( node.nodeType == NODE_DOCUMENT )
		{
			header = "";
		}
		else 
		{
			var r = new TaminoResult( this, null );
			r.errorNo   = TaminoClient.invalidNodeTypeNo;
			r.errorText = TaminoClient.errorText( TaminoClient.invalidNodeTypeNo, node.nodeType );
			
			return ( r );
		}
	}
	
	REQ = new ActiveXObject( TaminoClient.XMLHTTP );
	REQ.open( "PUT", this.lastQuery, false, this.user, this.password );
	REQ.setRequestHeader( "User-Agent", this.userAgent );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	REQ.setRequestHeader( "Content-Type", "text/xml; charset=" + this.charset );
	var session = this.tEncoded( REQ );
	REQ.send( header + node.xml );
	
	var result = new TaminoResult( this, REQ, true );
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.head = function( reurl )
{
	this.lastQuery = this.absoluteURL( reurl );
	REQ = new ActiveXObject( TaminoClient.XMLHTTP );
	REQ.open( "HEAD", this.lastQuery, false, this.user, this.password );
	REQ.setRequestHeader( "User-Agent", this.userAgent );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	var session = this.tEncoded( REQ );
	REQ.send( "" );
	
	var result = new TaminoResult( this, REQ, true );
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.process = function( node )
{
	var header;
	var nodeType = node.nodeType;
	
	if	( nodeType == NODE_ELEMENT )
	{
		header = this.xmlHeader;
	}
	else 	
	{
		if ( nodeType == NODE_DOCUMENT )
		{
			header = "";
		}
		else 
		{
			var r = new TaminoResult( this, null );
			r.errorNo   = TaminoClient.invalidNodeTypeNo;
			r.errorText = TaminoClient.errorText( TaminoClient.invalidNodeTypeNo, nodeType );
			
			return ( r );
		}
	}
	
	if ( arguments.length > 1 )
		this.lastQuery = this.XMLDB + arguments[1];
	else   
		this.lastQuery = this.XMLDB + this.securePath;
	
	REQ = new ActiveXObject( TaminoClient.XMLHTTP );
	REQ.open( "POST", this.lastQuery, false, this.user, this.password );
	var session = this.tEncoded( REQ );
	
	if ( session ) 
		session = session + "&";
		
	REQ.setRequestHeader( "User-Agent",      this.userAgent      );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	REQ.setRequestHeader( "Accept-Charset",  this.acceptCharset  );
	REQ.setRequestHeader( "Content-Type",    "application/x-www-form-urlencoded; charset=" + this.charset );
	REQ.send( session + "_PROCESS=" + this.escape( header + node.xml ) );

	var r = new TaminoResult( this, REQ );
	return ( r );
};

/**
 * @access public
 */
TaminoClient.prototype.discoverTaminoAPIIndex = function() // internal
{
	var r = this.diagnose( "apiversion" );
	
	if ( r.errorNo )
		return ( 1 );
		
	var apiVersion = r.getTaminoAPIVersion();
	
	if	( apiVersion )
	{
		if ( apiVersion != "1.0" )
			return ( 2 );
	}
	
	return ( 1 );
};

/**
 * @access public
 */
TaminoClient.prototype.diagnose = function( command )
{	
	var REQ = new ActiveXObject( TaminoClient.XMLHTTP );
	REQ.open( "GET", this.XMLDB + "?_diagnose=" + command, false, this.user, this.password );
	REQ.setRequestHeader( "User-Agent",      this.userAgent      );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	REQ.setRequestHeader( "Accept-Charset",  this.acceptCharset  );
	REQ.send();
	
	var r = new TaminoResult( this, REQ );
	return ( r );
};

/**
 * @access public
 */
TaminoClient.prototype.insert = function( node )
{
	var atts;
	
	if ( node.nodeType == NODE_DOCUMENT )
		atts = node.documentElement.attributes;
	else
		atts = node.attributes;
	
	if ( atts )	
		atts.removeNamedItem( "ino:id" );
		
	if ( arguments.length > 1 )
		return ( this.process( node, arguments[1] ) );
		
	return ( this.process( node ) );
};

/**
 * @access public
 */
TaminoClient.prototype.update = function( node, inoid )
{
	// override inoId if it exists
	inoidNode = node.ownerDocument.createAttribute( "ino:id" );
	node.attributes.setNamedItem( inoidNode );
	inoidNode.nodeValue = inoid;
	
	if ( arguments.length > 2 )
		return ( this.process( node, arguments[2] ) );
		
	return ( this.process( node ) );
};

/**
 * @access public
 */
TaminoClient.prototype.inoDoCommand = function( command, tidParam )
{
	var sendString = command + "=*";
	
	if ( tidParam ) 
		sendString += "&" + tidParam;
		
	this.lastQuery = this.XMLDB + this.securePath + "?" + sendString;
	
	REQ = new ActiveXObject( TaminoClient.XMLHTTP );
	REQ.open( "POST", this.lastQuery, false, this.user, this.password );
	REQ.setRequestHeader( "Content-Type","application/x-www-form-urlencoded; charset=" + this.charset );
	REQ.setRequestHeader( "User-Agent",      this.userAgent      );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	REQ.setRequestHeader( "Accept-Charset",  this.acceptCharset  );
	REQ.send( sendString );	
	
	result = new TaminoResult( this, REQ );
	
	if ( command == "_disconnect" && result.errorNo == TaminoClient.sessionIdErrorNo )
	{
		result.errorNo   = 0;
		result.errorText = "";
	}
	
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.SessionError = function( code )
{
	var result = new TaminoResult( this, null );
	result.errorNo   = code;
	result.errorText = TaminoClient.errorText( code );
	
	return ( result );			
};

/**
 * @access public
 */
TaminoClient.prototype.startSession = function( isolation, lockwait )
{
	// test
	if ( this.transaction != null )
		return ( this.SessionError( TaminoClient.sessionOpenErrorNo ) );
	
	var connectParameters = "";
	
	if ( isolation )
		connectParameters = connectParameters + "_isolation=" + isolation;
		
	if ( lockwait )
	{
		if ( connectParameters ) 
			connectParameters = connectParameters + '&';
			
		connectParameters = connectParameters + "_lockwait=" + lockwait;
	}
	
	var result = this.inoDoCommand( "_connect", connectParameters );
	
	if ( !result.errorNo )
	{
		var tid     = REQ.getResponseHeader( TaminoClient.XINOSESSIONID  );
		var key     = REQ.getResponseHeader( TaminoClient.XINOSESSIONKEY );
		var version = REQ.getResponseHeader( TaminoClient.XINOVERSION    );

		if ( !tid || !key )
		{
			var root = result.DOM.getElementsByTagName( "ino:response" ).item( 0 );
			
			if ( root )
			{
				tid = root.getAttribute( "ino:sessionid"  );
				key = root.getAttribute( "ino:sessionkey" );
			}
		}
		
		if ( tid && key )
		{
			this.errorNo     = 0;
			this.transaction = new TaminoTransaction( tid, key, ( version != null ) );
			
			return ( result );
		}
		
		result.errorNo   = TaminoClient.sessionFailureErrorNo;
		result.errorText = TaminoClient.errorText( TaminoClient.sessionFailureErrorNo );
	}
	
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.endSession = function()
{
	var result;
	
	if ( !this.transaction )
		return ( this.SessionError( TaminoClient.sessionNotOpenErrorNo ) );
	  	
	result = this.inoDoCommand( "_disconnect", this.tEncoded() );
	this.transaction = null;
	
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.commit = function()
{
	var result;
	
	if ( !this.transaction )
		return ( this.SessionError( TaminoClient.sessionNotOpenErrorNo ) );
	  	
	result = this.inoDoCommand( "_commit", this.tEncoded() );
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.rollback = function()
{
	var result;
	
	if	( !this.transaction )
		return ( this.SessionError( TaminoClient.sessionNotOpenErrorNo ) );
	  	
	result = this.inoDoCommand( "_rollback", this.tEncoded() );
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.tEncoded = function( REQ ) 
{
	if ( this.transaction )
		return ( this.transaction.encoded( REQ ) );
	else
		return ( "" );
};

/**
 * @access public
 */
TaminoClient.prototype.setEncoding = function( encoding )
{
	if ( encoding == "" )
		this.xmlHeader = '<?xml version="1.0"?>';
	else
		this.xmlHeader = '<?xml version="1.0" encoding="' + encoding + '"?>';
};

/**
 * @access public
 */
TaminoClient.prototype.setUserPassword = function( user, password )
{
	this.user = user;
	this.password = password;
};

/**
 * @access public
 */
TaminoClient.prototype.define = function( node )
{	
	var REQ = new ActiveXObject( TaminoClient.XMLHTTP );
	this.lastQuery = this.XMLDB;
	REQ.open( "POST", this.lastQuery, false, this.user, this.password );
	REQ.setRequestHeader( "User-Agent",      this.userAgent      );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	REQ.setRequestHeader( "Accept-Charset",  this.acceptCharset  );
	REQ.setRequestHeader( "Content-Type","application/x-www-form-urlencoded; charset=" + this.charset );
	REQ.send( "_define=" + this.escape( this.xmlHeader + node.xml ) );
	
	result = new TaminoResult( this, REQ );
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.undefine = function( query )
{
	var REQ = new ActiveXObject( TaminoClient.XMLHTTP );
	this.lastQuery = this.XMLDB;
	REQ.open( "POST", this.lastQuery, false, this.user, this.password );
	REQ.setRequestHeader( "User-Agent",      this.userAgent      );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	REQ.setRequestHeader( "Accept-Charset",  this.acceptCharset  );
	REQ.setRequestHeader( "Content-Type","application/x-www-form-urlencoded; charset=" + this.charset );
	REQ.send( "_undefine=" + this.escape( query ) );
	
	result = new TaminoResult( this, REQ );
	return ( result );
};

/**
 * @access public
 */
TaminoClient.prototype.loadChildren = function( node )
{
	var elements = node.childNodes;
	var length   = elements.length;
	var index    = 0;
	var buffer   = "";
	var errorNo  = 0;
	var maxBunch = this.maxBunch;
	
	var REQ;
	var result;
	
	if ( arguments.length > 1 ) 
		maxBunch = arguments[1];
	
	if ( !maxBunch ) 
		maxBunch = 100;
		
	this.lastQuery = this.XMLDB;
	
	while ( !errorNo && index < length )
	{
		var bunch = 0;
		buffer = "";
		
		while ( !errorNo && bunch < maxBunch && index < length )
		{
			buffer = buffer + elements.item( index ).xml;
			
			bunch++;
			index++;
		}
		
		if ( !errorNo && bunch )
		{
			REQ = new ActiveXObject( TaminoClient.XMLHTTP );
			REQ.open( "POST", this.lastQuery, false, this.user, this.password );
			var session = this.tEncoded( REQ );
			
			if ( session ) 
				session = session + "&";
				
			REQ.setRequestHeader( "User-Agent",      this.userAgent      );
			REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
			REQ.setRequestHeader( "Accept-Charset",  this.acceptCharset  );
			REQ.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded; charset=" + this.charset );
			REQ.send( session + "_PROCESS=" + this.escape( this.xmlHeader + buffer ) );
			
			result  = new TaminoResult( this, REQ );
			errorNo = result.errorNo;
		}
	}
	
	if	( !result )
		result = new TaminoResult( this ); // should give a zero set
		
	return ( result );	
};

/**
 * @access public
 */
TaminoClient.prototype.remoteUpdate = function( verb, reurl, xPath, node )
{
	var payload  = "";
	var nodeType = node.nodeType;
	
	if ( node && node.nodeType != NODE_ELEMENT )
	{
		var r = new TaminoResult( this, null );
		r.errorNo   = TaminoClient.invalidNodeTypeNo;
		r.errorText = TaminoClient.errorText( TaminoClient.invalidNodeTypeNo, node.nodeType );
			
		return ( r );
	}
	
	if ( node )
		payload = "&_newnode=" + this.escape( this.header + node.xml );
		
	this.lastQuery = this.XMLDB;
	REQ = new ActiveXObject( TaminoClient.XMLHTTP );
	REQ.open( "POST", this.lastQuery, false, this.user, this.password );
	var session = this.tEncoded( REQ );
	
	if ( session ) 
		session = session + "&";
		
	REQ.setRequestHeader( "User-Agent",      this.userAgent      );
	REQ.setRequestHeader( "Accept-Language", this.acceptLanguage );
	REQ.setRequestHeader( "Accept-Charset",  this.acceptCharset  );
	REQ.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded; charset=" + this.charset );
	REQ.send( session + verb + "=" + this.escape( reurl ) + "&_xpath=" + this.escape( xPath ) + payload );

	var r = new TaminoResult( this, REQ );
	return ( r );
};

/**
 * @access public
 */
TaminoClient.prototype.insertBefore = function( reurl, xpath, node )
{
	return ( this.remoteUpdate( "_insertBefore", reurl, xpath, node ) );
};

/**
 * @access public
 */
TaminoClient.prototype.appendChild = function( reurl, xpath, node )
{
	return ( this.remoteUpdate( "_appendChild", reurl, xpath, node ) );
};

/**
 * @access public
 */
TaminoClient.prototype.replaceChild = function( reurl, xpath, node )
{
	return ( this.remoteUpdate( "_replaceChild", reurl, xpath, node ) );
};

/**
 * @access public
 */
TaminoClient.prototype.removeChild = function( reurl, xpath )
{
	return ( this.remoteUpdate( "_removeChild", reurl, xpath ) );
};


/**
 * @access public
 * @static
 */
TaminoClient.inoVersion = "2.2";

/**
 * @access public
 * @static
 */
TaminoClient.inoPatchLevel = "2";

/**
 * self-adjusting
 * @access public
 * @static
 */
TaminoClient.taminoAPIIndex = 0;

/**
 * @access public
 * @static
 */
TaminoClient.inoComponent = "INOXJE";

/**
 * @access public
 * @static
 */
TaminoClient.inoErrorText = new Array(
	"HTTP Error ",
	"Parser Error ",
	"Session already open",
	"No session open",
	"No session established",
	"URL did not exist",
	"Document to be Deleted not Specified",
	"Invalid Node Type",
	"Invalid sessionID received from Server",
	"No Tamino Response Returned",
	"URL changed during Transaction"
);

TaminoClient.inoEnoErrorBase       = 8400;
TaminoClient.transportErrorNo      = TaminoClient.inoEnoErrorBase + 0;
TaminoClient.parserErrorNo         = TaminoClient.inoEnoErrorBase + 1;
TaminoClient.sessionOpenErrorNo    = TaminoClient.inoEnoErrorBase + 2;
TaminoClient.sessionNotOpenErrorNo = TaminoClient.inoEnoErrorBase + 3;
TaminoClient.sessionFailureErrorNo = TaminoClient.inoEnoErrorBase + 4;
TaminoClient.URLdidNotExistNo      = TaminoClient.inoEnoErrorBase + 5;
TaminoClient.deleteNotSpecifiedNo  = TaminoClient.inoEnoErrorBase + 6;
TaminoClient.invalidNodeTypeNo     = TaminoClient.inoEnoErrorBase + 7;
TaminoClient.sessionIdErrorNo      = TaminoClient.inoEnoErrorBase + 8;
TaminoClient.noXMLErrorNo          = TaminoClient.inoEnoErrorBase + 9;
TaminoClient.changeURLTran         = TaminoClient.inoEnoErrorBase + 10;

/**
 * This might need changed for server side operation
 *
 * @access public
 * @static
 */
TaminoClient.XMLHTTP = "Microsoft.XMLHTTP";

/**
 * @access public
 * @static
 */
TaminoClient.inoUserAgent = "Tamino Client (Abstractpage)/Java Script/IE5.0 ";

/**
 * ino:id response header name
 *
 * @access public
 * @static
 */
TaminoClient.XINOID = "X-INO-id";

/* Transactionality Constants */
TaminoClient.XINOSESSIONID  = "X-INO-Sessionid";
TaminoClient.XINOSESSIONKEY = "X-INO-Sessionkey";
TaminoClient.XINOVERSION    = "X-INO-Version";
TaminoClient.YES            = "yes";
TaminoClient.NO             = "no";
TaminoClient.PROTECTED      = "protected";
TaminoClient.UNPROTECTED    = "unprotected";
TaminoClient.SHARED         = "shared"; 

TaminoClient.XINOEXPLAINDOCUMENTSCAN = "documentscan";
TaminoClient.XINOEXPLAINPATH         = "path";
TaminoClient.XINOEXPLAINTREE         = "tree";
TaminoClient.XINOEXPLAININTERMEDIATE = "intermediate";

/**
 * @access public
 * @static
 */
TaminoClient.errorText = function( code )
{
	var parameters = "";
	var l = arguments.length;
	
	for ( var i = 1; i < l; i++ )
		parameters = parameters + " " + arguments[i];
	
	// this should get the error text from the server if the langauge is not right
	return ( TaminoClient.inoComponent + code + " " + TaminoClient.inoErrorText[code - TaminoClient.inoEnoErrorBase] + parameters );
};
