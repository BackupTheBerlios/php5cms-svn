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
 * @package util_text_encoding
 */
 
/**
 * Constructor
 *
 * @access public
 */
Base64 = function()
{
	this.Base = Base;
	this.Base();

	var b64s = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
	this.b64 = new Array();
	this.f64 = new Array();
	
	for ( var i = 0; i < b64s.length; i++ )
	{
		this.b64[i] = b64s.charAt( i );
		this.f64[b64s.charAt( i )] = i;
	}
};


Base64.prototype = new Base();
Base64.prototype.constructor = Base64;
Base64.superclass = Base.prototype;

/**
 * Creates a base64 encoded text out of an array of byerepresenting decimals.
 *
 * @param  array  d
 * @return string
 * @access public
 */
Base64.prototype.encode = function( d )
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
};

/**
 * Returns array of byterepresenting numbers created of an base64 encoded text
 * it is still the slowest function in this modul.
 *
 * @param  string  t
 * @return array
 * @access public
 */
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
};
