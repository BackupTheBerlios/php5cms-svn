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
 * @package peer_http_url
 */
 
/**
 * Constructor
 *
 * @access public
 */
URL = function( url )
{
	this.Base = Base;
	this.Base();
	
	this.url    = url || '';
	this.urlObj = new Object();
	
	this.defaultProtocol = "http";
	this.defaultPort     = 80;
	this.defaultPortFTP  = 21;
	
	this.parseURL( this.url );
};


URL.prototype = new Base();
URL.prototype.constructor = URL;
URL.superclass = Base.prototype;

/**
 * @access public
 */
URL.prototype.setURL = function( url )
{
	if ( url != null )
		this.url = url;
	else
		return false;
};

/**
 * @access public
 */
URL.prototype.parseURL = function()
{
	var i;
	var defaultProtocolUsed = false;
	
	// protocol
	var url = this.url;
	var protocol = url.split( '://' );
	var rest = protocol[1];
	protocol = protocol[0];

	if ( protocol.length > 5 )
	{
		// result longer than "https"?
		protocol = this.defaultProtocol;
		defaultProtocolUsed = true;
	}
	
	// host
	var host = defaultProtocolUsed? url : rest;
	
	if ( host.indexOf( '/') != -1 )
		host = host.substring( 0, host.indexOf( '/' ) );

	if ( host.indexOf( ':') != -1 )	
		host = host.substring( 0, host.indexOf( ':' ) );
	
	// domain
	var domain = host;
	domain = domain.substring( domain.lastIndexOf( '.' ) + 1, domain.length );	
	
	// path
	var slashCount = 0;
	var path = defaultProtocolUsed? url : rest;
	
	for ( i = 0; i < path.length; i++)
	{
		if ( path.charAt( i ) == "/" )
			slashCount++;
	}
	
	if ( slashCount > 1 )
	{
		if ( path.indexOf( '/') != -1 )
			path = path.substring( path.indexOf( '/' ) + 1, path.lastIndexOf( '/' ) + 1 );
	}
	else
	{
		path = "";
	}
	
	// document
	var dcm = defaultProtocolUsed? url : rest;

	if ( dcm.indexOf( '/') != -1 )
		dcm = dcm.substring( dcm.lastIndexOf( '/' ) + 1 );
	else
		dcm = "";
		
	if ( dcm.indexOf( '?') != -1 )
		dcm = dcm.substring( 0, dcm.indexOf( '?' ) );	
	
	if ( dcm.indexOf( '#') != -1 )
		dcm = dcm.substring( 0, dcm.indexOf( '#' ) );	
	
	// reference
	var reference = defaultProtocolUsed? url : rest;
	reference = reference.split( '#' );
	
	if ( reference[1] )
		reference = reference[1].substring( reference[1].indexOf( '#' ) + 1, reference[1].length );
	else 
		reference = "";
	
	// query
	var queryStr = '';
	var queryArr = new Array();
	var queryObj = new Object();
	
	url = this.url;
	url = url.split( '?' );
	
	if ( url[1] )
	{
		queryStr = argList = url[1];
		argList  = argList.split( '&' );
		
		for ( i = 0; i < argList.length; i++ )
		{
			newArg = argList[i];
			newArg = argList[i].split( '=' );
			
			val = unescape( newArg[1] );
			
			queryArr[i] = val;
			queryObj[i] = { key: newArg[0], value: val };
		}
	}
	
	// port
	var port = ( protocol == "ftp" )? this.defaultPortFTP : this.defaultPort;
	
	url = this.url;
	url = url.split( ':' );

	var chr;	
	var ref = defaultProtocolUsed? url[1] : url[2]
	
	if ( ref )
	{
		port = "";

		for ( i = 0; i < ref.length; i++ )
		{
			chr = ref.charAt(i);
			
			if ( Math.round( chr ) == chr )
				port += chr;

			if ( chr == "/" )
				break;
		}
	}
	
	// put results in urlObj
	this.urlObj.protocol  = protocol;	// http://, https://, ftp://
	this.urlObj.host      = host;		// www.docuverse.de, docuverse.de, sales.docuverse.de
	this.urlObj.domain    = domain;		// e.g. ".de"
	this.urlObj.port      = port;		// defaults: 80 for HTTP, 21 for FTP
	this.urlObj.path      = path;		// services/all/
	this.urlObj.document  = dcm;		// page.htm, index.php, test.zip
	this.urlObj.reference = reference;	// e.g. "#top"
	this.urlObj.query     = queryStr;	// a=1&b=2&c=test
	this.urlObj.queryArr  = queryArr;	// Array of values
	this.urlObj.queryObj  = queryObj;	// Hash (key, value)
	
	return this.urlObj;
};

/**
 * @access public
 */
URL.prototype.getProtocol = function()
{
	return this.urlObj.protocol;
};

/**
 * @access public
 */
URL.prototype.getHost = function()
{
	return this.urlObj.host;
};

/**
 * @access public
 */
URL.prototype.getDomain = function()
{
	return this.urlObj.domain;
};

/**
 * @access public
 */
URL.prototype.getPort = function()
{
	return this.urlObj.port;
};

/**
 * @access public
 */
URL.prototype.getPath = function()
{
	return this.urlObj.path;
};

/**
 * @access public
 */
URL.prototype.getDocument = function()
{
	return this.urlObj.document;
};

/**
 * @access public
 */
URL.prototype.getReference = function()
{
	return this.urlObj.reference;
};

/**
 * @access public
 */
URL.prototype.getQuery = function()
{
	return this.urlObj.query;
};

/**
 * @access public
 */
URL.prototype.getQueryArray = function()
{
	return this.urlObj.queryArr;
};

/**
 * @access public
 */
URL.prototype.getQueryObject = function()
{
	return this.urlObj.queryObj;
};

/**
 * @access public
 */
URL.prototype.hasPath = function()
{
	if ( this.urlObj.path != "" )
		return true;
		
	return false;
};

/**
 * @access public
 */
URL.prototype.hasDocument = function()
{
	if ( this.urlObj.document != "" )
		return true;
		
	return false;
};

/**
 * @access public
 */
URL.prototype.hasReference = function()
{
	if ( this.urlObj.reference != "" )
		return true;
		
	return false;
};

/**
 * @access public
 */
URL.prototype.hasQuery = function()
{
	if ( this.urlObj.query != "" )
		return true;
		
	return false;
};

/**
 * @access public
 */
URL.prototype.getQueryVal = function( key )
{
	for ( var i in this.urlObj.queryObj )
	{
		if ( this.urlObj.queryObj[i].key == key )
			return this.urlObj.queryObj[i].value;
	}
	
	return false;
};

/**
 * @access public
 */
URL.prototype.getString = function( showPort )
{
	var str = ""
	
	str += this.urlObj.protocol + "://";
	str += this.urlObj.host;
	
	if ( showPort == true )
		str += ":" + this.urlObj.port;
	
	str += "/" + this.urlObj.path;
	str += this.urlObj.document;
	
	if ( this.hasReference() )
		str += "#" + this.urlObj.reference;
		
	if ( this.hasQuery() )
		str += "?" + this.urlObj.query;
	
	return str;
};

/**
 * @access public
 */
URL.prototype.dump = function()
{
	var obj = this.urlObj;
	var str = "RESULT FOR URL '" + this.url + "'\n\n\n";
	
	str += "protocol: "   + obj.protocol  + "\n\n";
	str += "host: "       + obj.host      + "\n\n";
	str += "domain: "     + obj.domain    + "\n\n";
	str += "port: "       + obj.port      + "\n\n";
	str += "path: "       + obj.path      + "\n\n";
	str += "document: "   + obj.document  + "\n\n";
	str += "reference: "  + obj.reference + "\n\n";
	str += "query: "      + obj.query     + "\n\n";
	str += "queryArray: " + obj.queryArr  + "\n\n";

	str += "queryObject:\n";
	
	for ( var i in obj.queryObj )
		str += "key: " + obj.queryObj[i].key + " - value: " + obj.queryObj[i].value + "\n";
	
	return str;
};
