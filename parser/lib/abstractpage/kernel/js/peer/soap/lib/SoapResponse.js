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
SoapResponse = function()
{
	this.Base = Base;
	this.Base();
	
	this.plain = "";
	this.soap  = null;
	
	// event handlers
	this.onfault   = new Function;
	this.onreceive = new Function;
};


SoapResponse.prototype = new Base();
SoapResponse.prototype.constructor = SoapResponse;
SoapResponse.superclass = Base.prototype;

/**
 * @access public
 */
SoapResponse.prototype.process = function( xmlhttp )
{
	if ( ( xmlhttp != null ) && xmlhttp.status )
	{
		this.plain = xmlhttp.responseText;
		this.soap  = xmlhttp.responseXML;
		
		if ( this.soap )
		{
			// check for soap errors
			if ( this.soap.getElementsByTagName( "SOAP-ENV:Fault" ).length > 0 )
			{
				var err_code = this.soap.getElementsByTagName( "faultcode"   ).item( 0 ).firstChild.nodeValue;
				var err_text = this.soap.getElementsByTagName( "faultstring" ).item( 0 ).firstChild.nodeValue;

				this.onfault( err_code, err_text );
				return false;
			}
			
			var main = this.soap.getElementsByTagName( "SOAP-ENV:Body" );
	    
			if ( main.length == 0 )
				return Base.raiseError( "Malformed SOAP Response." );
			
			var data = SoapResponse._evaluate( SoapResponse._getNode( main[0].firstChild, [0] ) );
			
			// handle receiving
			this.onreceive( data );
			
			return data;
		}
		else
		{
			return Base.raiseError( "Malformed SOAP Response." );
		}
	}
	else
	{
		return Base.raiseError( "Input is not a valid XmlHttp Object." );
	}
};

/**
 * @access public
 */
SoapResponse.prototype.serialize = function()
{
	return this.plain;
};

/**
 * @access public
 */
SoapResponse.prototype.xml = function()
{
	return this.soap;
};


// private methods

/**
 * @access private
 * @static
 */
SoapResponse._getNode = function( data, tree )
{
	var nc = 0; // nodeCount
	
	if ( data != null )
	{
		for ( i = 0; i < data.childNodes.length; i++ )
		{
			if ( data.childNodes[i].nodeType == 1 )
			{
				if ( nc == tree[0] )
				{
					data = data.childNodes[i];
					
					if ( tree.length > 1 )
					{
						tree.shift();
						data = SoapResponse._getNode( data, tree );
					}
					
					return data;
				}
				
				nc++;
			}
		}
	}
		
	return false;
};

/**
 * @access private
 * @static
 */
SoapResponse._evaluate = function( data )
{
	if ( !data.tagName )
		return null;
		
	var ret, i;
	var att  = data.getAttribute( "xsi:type" );
	
	if ( !att )
		return null;
		
	var type = att.substring( att.indexOf( ":" ) + 1, att.length );

	switch ( type )
	{
		case "string":
			return data.firstChild? new String( data.firstChild.nodeValue ) : "";
			break;
			
		case "int":
		
		case "i4":
		
		case "double":
			return data.firstChild? new Number( data.firstChild.nodeValue ) : 0;
			break;
		
		case "timeInstant":
			var sn = SoapObject.ie_win? "-" : "/";
			
			/*
			Have to read the spec to be able to completely 
			parse all the possibilities in iso8601
			07-17-1998 14:08:55
			19980717T14:08:55
			*/		
			if ( /^(\d{4})(\d{2})(\d{2})T(\d{2}):(\d{2}):(\d{2})/.test( data.firstChild.nodeValue ) )
			{
	      		return new Date(
					RegExp.$2 + sn  +
					RegExp.$3 + sn  + 
					RegExp.$1 + " " +
					RegExp.$4 + ":" + 
					RegExp.$5 + ":" + 
					RegExp.$6
				);
	      	}
	    	else
			{
	    		return new Date();
	    	}

			break;
			
		case "array":
			data = SoapResponse._getNode( data, [0] );
				
			if ( data && ( data.tagName == "data" ) )
			{
				ret = new Array();		
				var i = 0;
				
				while ( child = SoapResponse._getNode( data, [i++] ) )
      				ret.push( SoapResponse._evaluate( child ) );
					
				return ret;
			}
			else
			{
				return Base.raiseError( "Malformed SOAP Response." );
			}
			
			break;
		
		case "struct":
			ret = {};			
			var i = 0;
			
			while ( child = SoapResponse._getNode( data, [i++] ) )
			{
				if ( child.tagName == "member" )
					ret[SoapResponse._getNode( child, [0] ).firstChild.nodeValue] = SoapResponse._evaluate( SoapResponse._getNode( child, [1] ) );
				else
					return Base.raiseError( "Malformed SOAP Response." );
			}
				
			return ret;
			break;
			
		case "boolean":
			return Boolean( isNaN( parseInt( data.firstChild.nodeValue ) )? ( data.firstChild.nodeValue == "true" ) : parseInt( data.firstChild.nodeValue ) );
			break;

		case "base64":
			return SoapResponse._decodeBase64( data.firstChild.nodeValue );
			break;
			
		default:
			return Base.raiseError( "Malformed SOAP Response (" + data.tagName + ")." );
			break;
	}
};

/**
 * @access private
 * @static
 */
SoapResponse._decodeBase64 = function( sEncoded )
{
	// Input must be dividable with 4.
	if ( !sEncoded || (sEncoded.length % 4 ) > 0 )
	{
		return sEncoded;
	}	
	// Use NN's built-in base64 decoder if available.
	// This procedure is horribly slow running under NN4,
	// so the NN built-in equivalent comes in very handy. :)
	else if ( typeof( atob ) != 'undefined' )
	{
		return atob( sEncoded );
	}
	
	var nBits, i;
	var sDecoded = '';
	var base64   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
	sEncoded     = sEncoded.replace( /\W|=/g, '' );
	
	for ( i = 0; i < sEncoded.length; i += 4 )
	{
		nBits =
			( base64.indexOf( sEncoded.charAt( i     ) ) & 0xff ) << 18 |
			( base64.indexOf( sEncoded.charAt( i + 1 ) ) & 0xff ) << 12 |
			( base64.indexOf( sEncoded.charAt( i + 2 ) ) & 0xff ) <<  6 |
			  base64.indexOf( sEncoded.charAt( i + 3 ) ) & 0xff;
		
		sDecoded += String.fromCharCode( ( nBits & 0xff0000 ) >> 16, ( nBits & 0xff00 ) >> 8, nBits & 0xff );
	}
	
	// not sure if the following statement behaves as supposed under
	// all circumstances, but tests up til now says it does.
	return sDecoded.substring( 0, sDecoded.length - ( ( sEncoded.charCodeAt( i - 2 ) == 61 )? 2 : ( sEncoded.charCodeAt( i - 1 ) == 61? 1 : 0 ) ) );
};
