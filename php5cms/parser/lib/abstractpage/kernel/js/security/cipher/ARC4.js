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
 * ARCFOUR stream cipher implementation.
 *
 * @package security_cipher
 */
 
/**
 * Constructor
 *
 * @access public
 */
ARC4 = function( key )
{
	this.Base = Base;
	this.Base();
	
	this.k  = new Array( 256 );
	this.s  = new Array( 256 );
	this.ei = 0;
	this.ej = 0;
	var kl  = key.length;
	var i   = 0;
	var j   = 0;

	for ( i = 0; i < 256; i++ )
	{
		this.s[i] = i;
		this.k[i] = key.charCodeAt( j );
		
		if ( ++j == kl )
			j = 0;
	}

	for ( i = 0, j = 0; i < 256; i++ )
	{
		j = ( j + this.s[i] + this.k[i] ) % 0x100;
		this.s.swap( i, j );
	}
};


ARC4.prototype = new Base();
ARC4.prototype.constructor = ARC4;
ARC4.superclass = Base.prototype;

/**
 * Returns next PRNG byte from the cipher
 * method of rc4engine object.
 *
 * @access public
 * @static
 */
ARC4.prototype.next = function()
{
	this.ei = ( this.ei + 1 ) % 0x100;
	this.ej = ( this.ej + this.s[this.ei] ) % 0x100;
    
	this.s.swap( this.ei, this.ej );

	var t = ( this.s[this.ei] + this.s[this.ej] ) % 0x100;
	return this.s[t];
};


/**
 * Main operation function of ARCFOUR stream cipher.
 * 
 * @param  string  inStr  The input stream, plain or ciphertext
 * @param  string  key    de/encryption key
 * @access public
 * @static
 */
ARC4.arc4 = function( inStr, key )
{
	var e = new ARC4( key );
	var outStr = new String;
	var sl = inStr.length;
	var i  = 0;

	for ( i = 0; i < sl; i++ )
	{
		a = inStr.charCodeAt( i );
		b = e.next();
		outStr = outStr + String.fromCharCode( a ^ b );   
	}

	return outStr;
};
