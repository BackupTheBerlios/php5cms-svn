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
UTF8 = function()
{
	this.Base = Base;
	this.Base();
};


UTF8.prototype = new Base();
UTF8.prototype.constructor = UTF8;
UTF8.superclass = Base.prototype;

/**
 * Returns an array of byterepresenting dezimal numbers which represent the plaintext
 * in an UTF-8 encoded version. Expects a string.
 * This function includes an exception management for those nasty browsers like
 * NN401, which returns negative decimal numbers for chars>128. I hate it!!
 * This handling is unfortunately limited to the user's charset. Anyway, it works
 * in most of the cases! Special signs with an unicode>256 return numbers, which
 * can not be converted to the actual unicode and so not to the valid utf-8
 * representation. Anyway, this function does always return values which can not
 * misinterpretd by RC4 or base64 en- or decoding, because every value is >0 and <255!
 *
 * @param  string  t
 * @return array
 * @access public
 */
UTF8.prototype.encode = function( t )
{
	t = t.replace( /\r\n/g, "\n" );
	var d = new Array;
	var test = String.fromCharCode( 237 );
	
	if ( test.charCodeAt( 0 ) < 0 )
	{
		for ( var n = 0; n < t.length; n++ )
		{
			var c = t.charCodeAt( n );
			
			if ( c > 0 )
			{
				d[d.length]= c;
			}
			else
			{
				d[d.length] = ( ( ( 256 + c ) >>  6 ) | 192 );
				d[d.length] = ( ( ( 256 + c ) &  63 ) | 128 );
			}
		}
	}
	else
	{
		for ( var n = 0; n < t.length; n++ )
		{
			var c = t.charCodeAt( n );
			
			// all the signs of asci => 1byte
			if ( c < 128 )
			{
				d[d.length] = c;
			}
			// all the signs between 127 and 2047 => 2byte
			else if ( ( c > 127 ) && ( c < 2048 ) )
			{
				d[d.length]= ( ( c >>  6 ) | 192 );
				d[d.length]= ( ( c &  63 ) | 128 );
			}
			// all the signs between 2048 and 66536 => 3byte
			else
			{
				d[d.length] = ( ( c >> 12 ) | 224 );
				d[d.length] = ( ( (c >> 6 ) &  63 ) | 128 );
				d[d.length] = ( ( c &  63 ) | 128 );
			}
		}
		
		return d;
	}
};

/**
 * Returns plaintext from an array of bytesrepresenting dezimal numbers, which
 * represent an UTF-8 encoded text. Browser which does not understand unicode
 * like NN401 will show "?"-signs instead.
 * Expects an array of byterepresenting decimals. Returns a string.
 *
 * @param  array  d
 * @return string
 * @access public
 */
UTF8.prototype.decode = function( d )
{
	var r = new Array;
	var i = 0;
	
	while ( i < d.length )
	{
		if ( d[i] < 128 )
		{
			r[r.length] = String.fromCharCode( d[i] );
			i++;
		}
		else if ( ( d[i] > 191 ) && ( d[i] < 224 ) )
		{
			r[r.length] = String.fromCharCode( ( ( d[i] & 31 ) << 6 ) | ( d[i+1] & 63 ) );
			i += 2;
		}
		else
		{
			r[r.length] = String.fromCharCode( ( ( d[i] & 15 ) << 12 ) | ( ( d[i+1] & 63 ) << 6 ) | ( d[i+2] & 63 ) );
			i += 3;
		}
	}
	
	return r.join( "" );
};
