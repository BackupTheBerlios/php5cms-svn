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
SoapValue = function( value, name, type )
{
	this.Base = Base;
	this.Base();
	
	this._name  = name || this._getNextValueName();
	this._type  = this._getSoapType( value, type );
	this._value = this._getSoapValue( value, this._type );

	this._encStyle = "http://schemas.xmlsoap.org/soap/encoding/";
};


SoapValue.prototype = new Base();
SoapValue.prototype.constructor = SoapValue;
SoapValue.superclass = Base.prototype;

/**
 * @access public
 */
SoapValue.prototype.serialize = function()
{
	switch ( this._type )
	{
		case 'array':
			return this._serializeArray( this._name, this._value );
			break;
			
		case 'struct':
			return this._serializeStruct( this._name, this._value );
			break;
			
		default:
			return this._serializeScalar( this._name, this._value, this._type );
			break;
	}
};


// private methods

/**
 * @access private
 */
SoapValue.prototype._serializeStruct = function( name, struct )
{
	var type, value, keytype, keyvalue;
	var str = "<" + name + " xmlns:ns2=\"http://xml.apache.org/xmlsoap\" xsi:type=\"ns2:Map\">\n";
	
	for ( prop in struct )
	{
		str += "<item>\n";
		
		type  = this._getSoapType( struct[prop] );
		value = this._getSoapValue( struct[prop], type );
		
		// I don´t know exactly if deep structures are part of the soap specification. Anyway, it´s done.
		switch ( type )
		{
			case 'array':
				str += this._serializeArray(  this._getNextValueName(), value );
				break;
			
			case 'struct':
				str += this._serializeStruct( this._getNextValueName(), value );
				break;
			
			default:
				keytype  = this._getSoapType( prop );
				keyvalue = this._getSoapValue( prop, keytype );
		
				str += "<key xsi:type=\"xsd:" + keytype + "\">" + keyvalue + "</key>\n";
				str += "<value xsi:type=\"xsd:" + type + "\">" + value + "</value>\n";
				str += "</item>\n";
				
				break;
		}
	}
	
	str += "</" + name + ">\n";
	return str;
};

/**
 * @access private
 */
SoapValue.prototype._serializeArray = function( name, values )
{
	var itemname, itemtype, itemvalue;
	var str = "<" + name + " xmlns:ns2=\"" + this._encStyle + "\" xsi:type=\"ns2:Array\" ns2:arrayType=\"xsd:ur-type[" + values.length + "]\">\n";
	
	for ( var i in values )
	{
		itemname  = "item";
		itemtype  = this._getSoapType( values[i] );
		itemvalue = this._getSoapValue( values[i], itemtype );
		
		// I don´t know exactly if deep structures are part of the soap specification. Anyway, it´s done.
		switch ( itemtype )
		{
			case 'array':
				str += this._serializeArray(  this._getNextValueName(), itemvalue );
				break;
			
			case 'struct':
				str += this._serializeStruct( this._getNextValueName(), itemvalue );
				break;
			
			default:
				str += this._serializeScalar( itemname, itemvalue, itemtype );
				break;
		}
	}
	
	str += "</" + name + ">\n";
	return str; 
};

/**
 * @access private
 */
SoapValue.prototype._serializeScalar = function( name, value, type )
{
	return "<" + name + " xsi:type=\"xsd:" + type + "\">" + value + "</" + name + ">\n";
};

/**
 * @access private
 */
SoapValue.prototype._isValidSoapType = function( type )
{
	return (
		( type == "i4"      ) ||
		( type == "int"     ) ||
		( type == "boolean" ) ||
		( type == "double"  ) ||
		( type == "string"  ) ||
		( type == "struct"  ) ||
		( type == "base64"  ) ||
		( type == "array"   ) ||
		( type == "timeInstant" ) )? true  : false;
};

/**
 * @access private
 */
SoapValue.prototype._getSoapValue = function( value, type )
{
	// Don´t send anything here unless you ran it through _getSoapType.
	// We need good soap types here.

	switch ( type )
	{
		case 'boolean':
			if ( value == "true" || value == 1 || value == true )
				value = 1;
			else
				value = 0;
				
			break;
			
		case 'string':
			value = this._convertToEscapes( value );
			break;
		
		case 'timeInstant':
			value = this._dateToISO8601( value );
			break;
			
		default:
			// for all other types we do nothing for now
			break;
	}
	
	return value;
};

/**
 * @access private
 */
SoapValue.prototype._getSoapType = function( value, type )
{
	var res;
	
	if ( type != null )
	{
		type = type.toLowerCase();

		if ( !this._isValidSoapType( type ) )
			res = "string";
		
		return type;
	}
	
	// We don´t know the type, so let´s guess...
	if ( type == null )
	{
		var tof = typeof( value );
		tof = tof.toLowerCase();
		
		switch ( tof )
		{
			case "boolean":
				res = "boolean";
				break;
			
			case "string":
				/*
				// hmm, fuzzy...
				if ( ( value.length % 4 ) == 0 )
					res = "base64";
				else
				*/
				
				res = "string";	
				break;
					
			case "number":
				if ( Math.round( value ) == value )
					res = "int";
				else
					res = "double";
      			
				break;
			
			case "object":
				var con = value.constructor;
				
				if ( con == Date )
					res = "timeInstant";
				else if ( con == Array )
					res = "array";
				else
					res = "struct";
				
				break;
				
			default:
				res = "string";
				break;
		}
	}
	
	return res;
};

/**
 * @access private
 */
SoapValue.prototype._convertToEscapes = function( str )
{
	var i, ch; 
	var stringnew = "";
	
	if ( str.length == 0 )
		return "";

	for ( i = 0; i < str.length; i++ ) 
	{ 
		ch = str.charAt( i ); 
		
		if ( ch == '<' )
			stringnew += '&lt;';
		else if ( ch == '>' )
			stringnew += '&gt;';
		else if ( ch == '&' )
			stringnew += '&amp;';
		else
			stringnew += ch;
	} 

	return stringnew;
};

/**
 * @access private
 */
SoapValue.prototype._getNextValueName = function()
{
	return ( "value" + ( ++SoapValue.paramCount ) );
};

/**
 * @access private
 */
SoapValue.prototype._dateToISO8601 = function( date )
{
	var year  = new String( date.getYear() );
	var month = this._leadingZero( new String( date.getMonth() ) );
	var day   = this._leadingZero( new String( date.getDate()  ) );
	var time  = this._leadingZero( new String( date.getHours() ) ) + ":" + this._leadingZero( new String( date.getMinutes() ) ) + ":" + this._leadingZero( new String( date.getSeconds() ) );

	var converted = year + "-" +  month + "-" + day + "T" + time;
	return converted;
};

/**
 * @access private
 */
SoapValue.prototype._leadingZero = function( n )
{
	// Pads a single number with a leading zero.
	if ( n.length == 1 )
		n = "0" + n;

	return n;	
};


/**
 * @access public
 * @static
 */
SoapValue.paramCount = 0;
