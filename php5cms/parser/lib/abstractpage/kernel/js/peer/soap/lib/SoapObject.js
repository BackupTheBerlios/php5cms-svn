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
SoapObject = function()
{
	this.Base = Base;
	this.Base();
	
	this.version = "0.1";
	
	this._error = "";
	
	/*
	// encapsulated base64 object
	this._base64 =
	{
		init: function()
		{
			var b64s = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
			this.b64 = new Array();
			this.f64 = new Array();
	
			for ( var i = 0; i < b64s.length; i++ )
			{
				this.b64[i] = b64s.charAt( i );
				this.f64[b64s.charAt( i )] = i;
			}
		},
		// Creates a base64 encoded text out of an array of byerepresenting decimals.
		// Expects an array, returns a string.
		encode: function( d )
		{
			var r  = new Array;
			var i  = 0;
			var dl = d.length;
	
			// this is for the padding
			if ( ( dl % 3 ) == 1 )
			{
				d[d.length] = 0;
				d[d.length] = 0;
			}
	
			if ( ( dl % 3 ) == 2 )
				d[d.length] = 0;
	
			// from here conversion
			while ( i < d.length )
			{
				r[r.length] = this.b64[d[i] >> 2];
				r[r.length] = this.b64[( ( d[i]   &  3 ) << 4 ) | ( d[i+1] >> 4 )];
				r[r.length] = this.b64[( ( d[i+1] & 15 ) << 2 ) | ( d[i+2] >> 6 )];
				r[r.length] = this.b64[d[i+2] & 63];
		
				if ( ( i % 57 ) == 54 )
					r[r.length] = "\n";
			
				i += 3;
			}
	
			// this is again for the padding
			if ( ( dl % 3 ) == 1 )
				r[r.length-1] = r[r.length-2] = "=";
	
			if ( ( dl % 3 ) == 2 )
				r[r.length-1] = "=";
	
			// we join the array to return a textstring
			var t = r.join( "" );
			return t;
		},
		// Returns array of byterepresenting numbers created of an base64 encoded text
		// it is still the slowest function in this modul.
		// Expects string, returns an array.
		Base64.prototype.decode = function( t )
		{
			var d = new Array;
			var i = 0;
	
			// here we fix this CRLF sequenz created by MS-OS
			t = t.replace(/\n|\r/g,"");
			t = t.replace(/=/g,"");
	
			while ( i < t.length )
			{
				d[d.length] = ( this.f64[t.charAt( i )] << 2 ) | ( this.f64[t.charAt( i + 1 )] >> 4 );
				d[d.length] = ( ( ( this.f64[t.charAt( i + 1 )] & 15 ) << 4 ) | ( this.f64[t.charAt( i + 2 )] >> 2 ) );
	  			d[d.length] = ( ( ( this.f64[t.charAt( i + 2 )] &  3 ) << 6 ) | ( this.f64[t.charAt( i + 3 )] ) );
		 
				i += 4;
			}
	
			if ( t.length % 4 == 2 )
				d = d.slice( 0, d.length - 2 );
	
			if ( t.length % 4 == 3 )
				d = d.slice( 0, d.length - 1 );
	
			return d;
		}
	};
	
	this._base64.init();
	*/
};


SoapObject.prototype = new Base();
SoapObject.prototype.constructor = SoapObject;
SoapObject.superclass = Base.prototype;

/**
 * @access public
 */
SoapObject.prototype.error =  function()
{
	return this._error;
};


/**
 * @access public
 * @static
 */
SoapObject.mozilla = navigator.productSub;

/**
 * @access public
 * @static
 */
SoapObject.ie_win = /MSIE ((5\.[56789])|([6789]))/.test( navigator.userAgent ) && ( navigator.platform == "Win32" );

/** 
 * Used to find the Automation server name.
 *
 * @access public
 * @static
 */
SoapObject.getControlPrefix = function()
{
	if ( SoapObject.getControlPrefix.prefix )
		return SoapObject.getControlPrefix.prefix;
	
	var prefixes = [ "MSXML2", "Microsoft", "MSXML", "MSXML3", "MSXML4" ];
	var o, o2;
	
	for ( var i = 0; i < prefixes.length; i++ )
	{
		try
		{
			// try to create the objects
			o  = new ActiveXObject( prefixes[i] + ".XmlHttp"     );
			o2 = new ActiveXObject( prefixes[i] + ".DomDocument" );
			
			return SoapObject.getControlPrefix.prefix = prefixes[i];
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
SoapObject.create = function()
{
	try
	{
		if ( window.XMLHttpRequest )
		{
			var req = new XMLHttpRequest();
			
			// some versions of Moz do not support the readyState property
			// and the onreadystate event so we patch it!
			if ( req.readyState == null )
			{
				req.readyState = 1;
				req.addEventListener( "load", function()
				{
					req.readyState = 4;
					
					if ( typeof req.onreadystatechange == "function" )
						req.onreadystatechange();
				}, false );
			}
			
			return req;
		}
		
		if ( window.ActiveXObject )
			return new ActiveXObject( SoapObject.getControlPrefix() + ".XmlHttp" );
	}
	catch ( ex )
	{
		return Base.raiseError( "Your browser does not support XmlHttp objects." );
	}
};
