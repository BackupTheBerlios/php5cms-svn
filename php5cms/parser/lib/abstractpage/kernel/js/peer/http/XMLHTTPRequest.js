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
 * XMLHTTPRequest Object
 * The HttpRequest object can be used by a client to send an arbitrary
 * HTTP request, receive the response, and have that response parsed
 * by the Microsoft XML Document Object Model. This object is integrated
 * with MSXML to support sending the request body directly from, and
 * parsing the response directly into, the MSXML DOM objects.
 *
 * @package peer_http
 */
 
/**
 * Constructor
 *
 * @access public
 */
XMLHTTPRequest = function()
{
	this.Base = Base;
	this.Base();
	
	this.setRequestMethod( "GET" );
	this.setLogin( "", "" );
	
	this.sent = false;
	this.xmlhttp = XMLHTTP.create();
};


XMLHTTPRequest.prototype = new Base();
XMLHTTPRequest.prototype.constructor = XMLHTTPRequest;
XMLHTTPRequest.superclass = Base.prototype;

/**
 * @access public
 */
XMLHTTPRequest.prototype.getReadyState = function()
{
	return this.xmlhttp.readyState;
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.getResponseBody = function()
{
	if ( this.sent == true )
		return this.xmlhttp.responseBody;
};

/**
 * @access public
 */
/*
XMLHTTPRequest.prototype.getResponseStream = function()
{
	if ( this.sent == true )
		return this.xmlhttp.responseStream;
};
*/

/**
 * @access public
 */
XMLHTTPRequest.prototype.getResponseText = function()
{
	if ( this.sent == true )
		return this.xmlhttp.responseText;
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.getResponseXML = function()
{
	if ( this.sent == true )
		return this.xmlhttp.responseXML;
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.getStatus = function()
{
	if ( this.sent == true )
		return this.xmlhttp.status;
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.getStatusText = function()
{
	if ( this.sent == true )
		return this.xmlhttp.statusText;
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.setLogin = function( user, password )
{
	if ( user != null && password != null )
	{
		this.userid   = user;
		this.password = password;
		
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.setRequestMethod = function( method )
{
	if ( method != null )
		this.method = method;
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.getRequestMethod = function()
{
	return this.method;
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.abort = function()
{
	this.xmlhttp.abort();
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.getAllResponseHeadersAsString = function()
{
	if ( this.sent == true )
		return this.xmlhttp.getAllResponseHeaders();
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.getAllResponseHeadersAsDictionary = function()
{
	if ( this.sent == false )
		return false;
		
	var entry = new Array();
	var dict  = new Dictionary();
	var raw   = this.xmlhttp.getAllResponseHeaders().trim().tokenize();
	
	for ( var i in raw )
	{
		entry[0] = raw[i].substring( 0, raw[i].indexOf( ":" ) ).trim();
		entry[1] = raw[i].substring( raw[i].indexOf( ":" ) + 1, raw[i].length ).trim();
		
		dict.add( entry[0], entry[1] );
	}
	
	return dict;
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.getResponseHeader = function( headerName )
{
	if ( ( headerName != null ) && ( this.sent == true ) )
		return this.xmlhttp.getResponseHeader( headerName );
	else
		return "";
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.open = function( url, method, async, userid, password )
{
	if ( url == null )
		return;
	
	this.xmlhttp.open(
		method || this.getRequestMethod(),
		url,
		async || false,
		userid?   userid   : ( this.userid   != "" )? this.userid   : null,
		password? password : ( this.password != "" )? this.password : null
	);
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.send = function( xmldoc )
{
	this.xmlhttp.send( xmldoc || null );
	this.sent = true;
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.setRequestHeader = function( name, value )
{
	if ( name != null && value != null )
		this.xmlhttp.setRequestHeader( name, value );
};

/**
 * @access public
 */
XMLHTTPRequest.prototype.openLocalFile = function( file )
{
	if ( file == null )
		return "";
	
	this.open( "servlet/RedirectServlet?fileName=" + file, "GET" );
	this.send();
};
