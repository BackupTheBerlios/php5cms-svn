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
 * SoapMessage Object (basically a container for SoapValue objects)
 *
 * @package peer_soap_lib
 */

/**
 * Constructor
 *
 * @access public
 */
SoapMessage = function( method, target, param )
{
	this.Base = Base;
	this.Base();
	
	this.setMethod( method );
	this.setTarget( target );

	if ( param != null && param.slice )	// add array
		this._parameters = param;
	else								// add single SoapValue object
		this.add( param );

	this._encoding = "UTF-8";
	this._encStyle = "http://schemas.xmlsoap.org/soap/encoding/";
	
	this._header = "<?xml version=\"1.0\" encoding=\"" + this._encoding + "\"?>\n" +
		"<SOAP-ENV:Envelope\n" + 
		"\txmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"\n" +
		"\txmlns:xsi=\"http://www.w3.org/1999/XMLSchema-instance\"\n" +
		"\txmlns:xsd=\"http://www.w3.org/1999/XMLSchema\">\n" +
		"<SOAP-ENV:Body>\n";

	this._footer = "</SOAP-ENV:Body>\n" + 
		"</SOAP-ENV:Envelope>\n";
	
	// methods for Mozilla
	if ( SoapObject.mozilla )
	{
		this.createXMLFromString = function( string )
		{
			try
			{
    			var xmlParser   = new DOMParser();
    			var xmlDocument = xmlParser.parseFromString( string, 'text/xml' );
    
				return xmlDocument;
  			}
  			catch ( e )
			{
				return Base.raiseError( "Can't create XML document." );
  			}
		};
		this.serializeXML = function( xmlDocument )
		{
			try
			{
    			var xmlSerializer = new XMLSerializer();
    			return xmlSerializer.serializeToString( xmlDocument );
  			}
  			catch ( e )
			{
				return Base.raiseError( "Can't serialize XML document." );
  			}
		};
	}
	// methods for ie
	else if ( SoapObject.ie_win )
	{
		this.createXMLFromString = function( string )
		{
			try
			{
    			var xmlDocument = new ActiveXObject( SoapObject.getControlPrefix() + ".DomDocument" );
    			xmlDocument.async = false;
    			xmlDocument.loadXML( string );
    
				return xmlDocument;
  			}
  			catch ( e )
			{
				return Base.raiseError( "Can't create XML document." );
  			}
		};
		this.serializeXML = function( xmlDocument )
		{
			return xmlDocument.xml;
		};
	}
	else
	{
		return Base.raiseError( "No XML Parser available." );
	}
};


SoapMessage.prototype = new Base();
SoapMessage.prototype.constructor = SoapMessage;
SoapMessage.superclass = Base.prototype;

/**
 * @access public
 */
SoapMessage.prototype.setMethod = function( method )
{
	if ( method != null && typeof( method ) == "string" )
		this._method = method;
		
	return this._method;
};

/**
 * @access public
 */
SoapMessage.prototype.setTarget = function( target )
{
	if ( target != null && typeof( target ) == "string" )
		this._target = target;
		
	return this._target;
};

/**
 * @access public
 */
SoapMessage.prototype.add = function( par )
{
	if ( par instanceof SoapValue )
		this._parameters[this._parameters.length] = par;
};

/**
 * @access public
 */
SoapMessage.prototype.getParamCount = function()
{
	return this._parameters.length;
};

/**
 * @access public
 */
SoapMessage.prototype.serialize = function()
{
	var soap = "";
	soap += this._header;
	soap += "<ns1:" + this._method + " xlmns:ns1=\"" + this._target + "\" SOAP-ENV:encodingStyle=\"" + this._encStyle + "\">\n";
	
	for ( var i in this._parameters )
		soap += this._parameters[i].serialize();
	
	soap += "</ns1:" + this._method + ">\n";
	soap += this._footer;
	
	return soap;
};

/**
 * @access public
 */
SoapMessage.prototype.xml = function()
{
	return this.createXMLFromString( this.serialize() );
};
