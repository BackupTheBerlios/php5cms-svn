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
 * @package peer_soap_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
SoapClient = function()
{
	this.SoapObject = SoapObject;
	this.SoapObject();
	
	this.responseText   = null;
	this.responseXML    = null;
	this.responseStatus = "";
	
	// some servers are picky about the user agent -- here´s the fake
	// this._agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
	
	// encapsulated xmlhttp object
	this._xhttp =
	{
		sent:    false,
		method:  "GET",
		xmlhttp: SoapObject.create(),
		
		getReadyState: function()
		{
			return this.xmlhttp.readyState;
		},
		getResponseText: function()
		{
			if ( this.sent == true )
				return this.xmlhttp.responseText;
		},
		getResponseXML: function()
		{
			if ( this.sent == true )
				return this.xmlhttp.responseXML;
		},
		getStatus: function()
		{
			if ( this.sent == true )
				return this.xmlhttp.status;
		},
		getStatusText: function()
		{
			if ( this.sent == true )
				return this.xmlhttp.statusText;
		},
		setRequestMethod: function( method )
		{
			if ( method == "POST" || method == "GET" )
				this.method = method;
		},
		abort: function()
		{
			this.xmlhttp.abort();
		},
		getResponseHeader: function( headerName )
		{
			if ( ( headerName != null ) && ( this.sent == true ) )
				return this.xmlhttp.getResponseHeader( headerName );
			else
				return "";
		},
		open: function( url, method, async, userid, password )
		{
			if ( url == null )
				return;
	
			this.xmlhttp.open(
				method || this.method,
				url,
				async || false,
				userid?   userid   : ( this.userid   != "" )? this.userid   : null,
				password? password : ( this.password != "" )? this.password : null
			);
		},
		send: function( xmldoc )
		{
			this.xmlhttp.send( xmldoc || null );
			this.sent = true;
		},
		setRequestHeader: function( name, value )
		{
			if ( name != null && value != null )
				this.xmlhttp.setRequestHeader( name, value );
		}
	}
};


SoapClient.prototype = new SoapObject();
SoapClient.prototype.constructor = SoapClient;
SoapClient.superclass = SoapObject.prototype;

/**
 * @access public
 */
SoapClient.prototype.send = function( url, soapmsg, callback )
{
	if ( url == null || url == "" )
	{
		this._error = "No Server specified.";
		return false;
	}

	if ( soapmsg == null || !( soapmsg instanceof SoapMessage ) )
	{
		this._error = "No valid SOAP Message.";
		return false;
	}
	
	var raw = soapmsg.serialize();
	
	this._xhttp.setRequestMethod( "POST" );
	// this._xhhtp.setRequestHeader( "Content-Type",   "text/xml"  );
	// this._xhhtp.setRequestHeader( "Content-Length", raw.length  );
	// this._xhhtp.setRequestHeader( "SOAPAction",     "/examples" );
	this._xhttp.open( url );
	this._xhttp.send( raw );
	
	this.responseStatus = this._xhttp.getStatus();
	
	if ( this.responseStatus == 200 )
	{
		this.responseText = this._xhttp.getResponseText();
		this.responseXML  = this._xhttp.getResponseXML();

		if ( ( callback != null ) && ( typeof callback == "function" ) )
			callback( this._xhttp.xmlhttp );
			
		return true;
	}
	else
	{
		this._error = "Could not connect to SOAP Server.";
		
		this.responseText = null;
		this.responseXML  = null;
		
		return false;
	}
	
	/*
	// failsafe version
	
	this._xhttp.onreadystatechange = function()
	{
		if ( this._xhttp.readyState == 4 )
		{
			this.responseStatus = this._xhttp.getStatus();
			
			if ( this.responseStatus == 200 )
			{
				this.responseText = this._xhttp.getResponseText();
				this.responseXML  = this._xhttp.getResponseXML();

				if ( ( callback != null ) && ( typeof callback == "function" ) )
					callback( this._xhttp.xmlhttp );
			
				return true;
			}
			else
			{
				this._error = "Could not connect to SOAP Server.";
		
				this.responseText = null;
				this.responseXML  = null;
		
				return false;
			}
		}
	}
	
	this._xhttp.send( soapmsg );
	*/
};
