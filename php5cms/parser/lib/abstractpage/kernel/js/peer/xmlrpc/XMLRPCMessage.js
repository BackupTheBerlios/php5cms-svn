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
 * @package peer_xmlrpc
 */
 
/**
 * Constructor
 *
 * @access public
 */
XMLRPCMessage = function( methodname )
{
	this.Base = Base;
	this.Base();
	
	this.method = methodname || "System.listMethods";
	this.params = [];
};


XMLRPCMessage.prototype = new Base();
XMLRPCMessage.prototype.constructor = XMLRPCMessage;
XMLRPCMessage.superclass = Base.prototype;

/**
 * @access public
 */
XMLRPCMessage.prototype.setMethod = function( methodName )
{
 	if ( !methodName )
		return;

	this.method = methodName;
};

/**
 * @access public
 */
XMLRPCMessage.prototype.addParameter = function( data )
{
 	if ( arguments.length == 0 )
		return;

	this.params[this.params.length] = data;
};

/**
 * @access public
 */
XMLRPCMessage.prototype.xml = function()
{
	var method = this.method;

	// var xml += "<?xml version=\"1.0\"?>\n";
	var xml = "<?xml version='1.0'?>\n";
	xml += "<methodCall>\n";
	xml += "<methodName>" + method + "</methodName>\n";
	xml += "<params>\n";
  
	// do individual parameters
	for ( var i = 0; i < this.params.length; i++ )
	{
		var data = this.params[i];
			
		xml += "<param>\n";
		xml += "<value>" + this.getParamXML( this.dataTypeOf( data ), data ) + "</value>\n";
		xml += "</param>\n";
	}
  
  	xml += "</params>\n";
  	xml += "</methodCall>";
  
	return xml;
};

/**
 * @access public
 */
XMLRPCMessage.prototype.dataTypeOf = function( o )
{
	// identifies the data type
	var type = typeof( o );
	type = type.toLowerCase();
		
	switch( type )
	{
		case "number":
			if ( Math.round( o ) == o )
				type = "i4";
			else
				type = "double";
      			
			break;
			
		case "object":
			var con = o.constructor;
				
			if ( con == Date )
				type = "date";
			else if ( con == Array )
				type = "array";
			else
				type = "struct";
				
			break;
	}
		
	return type;
};

/**
 * @access public
 */
XMLRPCMessage.prototype.doValueXML = function( type, data )
{
	var xml = "<" + type + ">" + data + "</" + type + ">";
	return xml;
};

/**
 * @access public
 */
XMLRPCMessage.prototype.doBooleanXML = function( data )
{
	var value = ( data == true )? 1 : 0;
	var xml = "<boolean>" + value + "</boolean>";

	return xml;
};

/**
 * @access public
 */
XMLRPCMessage.prototype.doDateXML = function( data )
{
	var xml = "<dateTime.iso8601>";
	xml += this.dateToISO8601( data );
	xml += "</dateTime.iso8601>";
		
	return xml;
};

/**
 * @access public
 */
XMLRPCMessage.prototype.doArrayXML = function( data )
{
	var xml = "<array><data>\n";
		
	for ( var i = 0; i < data.length; i++ )
		xml += "<value>" + this.getParamXML( this.dataTypeOf( data[i] ), data[i] ) + "</value>\n";
		
	xml += "</data></array>\n";
	return xml;
};

/**
 * @access public
 */
XMLRPCMessage.prototype.doStructXML = function( data )
{
	var xml = "<struct>\n";
		
	for ( var i in data )
	{
		xml += "<member>\n";
		xml += "<name>" + i + "</name>\n";
		xml += "<value>" + this.getParamXML( this.dataTypeOf( data[i] ), data[i] ) + "</value>\n";
		xml += "</member>\n";
  	}
		
	xml += "</struct>\n";
	return xml;
};

/**
 * @access public
 */
XMLRPCMessage.prototype.getParamXML = function( type, data )
{
	var xml;

	switch ( type )
	{
		case "date":
			xml = this.doDateXML( data );
			break;
				
		case "array":
			xml = this.doArrayXML( data );
			break;
			
		case "struct":
			xml = this.doStructXML( data );
			break;
				
		case "boolean":
			xml = this.doBooleanXML( data );
			break;
				
		default:
			xml = this.doValueXML( type, data );
			break;
	}

	return xml;
};

/**
 * @access public
 */
XMLRPCMessage.prototype.dateToISO8601 = function( date )
{
	var year  = new String( date.getYear() );
	var month = this.leadingZero( new String( date.getMonth() ) );
	var day   = this.leadingZero( new String( date.getDate()  ) );
	var time  = this.leadingZero( new String( date.getHours() ) ) + ":" + this.leadingZero( new String( date.getMinutes() ) ) + ":" + this.leadingZero( new String( date.getSeconds() ) );

	var converted = year + month + day + "T" + time;
	return converted;	
};

/**
 * @access public
 */
XMLRPCMessage.prototype.leadingZero = function( n )
{
	// pads a single number with a leading zero. Heh.
	if ( n.length == 1 )
		n = "0" + n;

	return n;	
};
