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
 * SHA-1 (FIPS 180-1) hash algorithm implementation
 * No padding, only 56 first characters are used for hash
 * supposed 32 bit unsigned integers.
 *
 * @package security_checksum
 */

/**
 * Constructor
 *
 * @access public
 */
SHA1 = function()
{
	this.Base = Base;
	this.Base();
};


SHA1.prototype = new Base();
SHA1.prototype.constructor = SHA1;
SHA1.superclass = Base.prototype;

/**
 * Returns hash value fo argument in array
 * of five 32 bit dwords.
 *
 * @access public
 * @static
 */
SHA1.getSHA1Hash = function( keyPhrase )
{
	var keyArray = SHA1.sha1CreateArray( keyPhrase );
	var digest   = SHA1.sha1Compute( keyArray );

	return digest;
};

/**
 * Converts argument string to SHA-1 input block
 * returns array containing 16 32-bit dwords.
 * OBS: argument string is truncated to maximum
 *      length of 56 bytes
 * 8-bit characters in String assumed
 *
 * @access public
 * @static
 */
SHA1.sha1CreateArray = function( keyPhrase )
{ 
	var l = keyPhrase.length;
	var byteArray  = new Array( 64 );
	var dwordArray = new Array( 16 );
		 
	if ( l >56 )
		l = 56;
		 
	for ( i = 0; i < 64; i++ )
	{
		if ( i < l )
			byteArray[i] = keyPhrase.charCodeAt( i );
		else
			byteArray[i] = 0;
	}
		 		 		 	 
	byteArray[l]  = 0x80;
	byteArray[63] = ( l * 8 ) & 0xFF;
	byteArray[62] = ( ( l * 8 ) & 0xFF00 ) >>> 8;
		 
	for ( i = 0; i < 16; i++ )
	{
		j  = i * 4;
		x  = byteArray[j]   << 24;
		x += byteArray[j+1] << 16;
		x += byteArray[j+2] <<  8;
		x += byteArray[j+3];
			
		dwordArray[i] = x;
	}
		 						
	return dwordArray;
};

/**
 * Initiliaze K constants.
 * Returns array containing 80 32-bit dwords.
 * OBS: K object might be constructed and used
 *      to return apropriate K values,
 *      alltough an Array _may_ be more efficient
 *
 * @access public
 * @static
 */
SHA1.sha1KBuffer = function()
{
	var k = new Array( 80 );
		 
	for ( i = 0; i < 20; i++ )	 
		k[i] = 0x5A827999; // (  0 =< t =< 19 )
		
	for ( i = 20; i < 40; i++ )	 
		k[i] = 0x6ED9EBA1  // ( 20 =< t =< 39 )

	for ( i = 40; i < 60; i++ )	 
		k[i] = 0x8F1BBCDC  // ( 40 =< t =< 59 )

	for ( i = 60; i < 80; i++ )	 
		k[i] = 0xCA62C1D6  // ( 60 =< t =< 79 )

	return( k );
};

/**
 * Initialiaze H values, wich store hash
 * function state during computation.
 * Array containing five 32-bit dwors is returned.
 *
 * @access public
 * @static
 */
SHA1.sha1HBuffer = function()
{
	var h = new Array( 0x67452301, 0xEFCDAB89, 0x98BADCFE, 0x10325476, 0xC3D2E1F0 );
	return h;
};

/**
 * Circular left shift for dword argument argument n bits
 * referenced as Sn function in alogorithm specification.
 * Returns shifted 32-bit dword.
 *
 * @access public
 * @static
 */
SHA1.sha1S = function( word, n )
{
	r = ( word << n ) | ( word >>> ( 32 - n ) );
	return r;
};

/**
 * Bitwise operations between dwords B,C and D
 * operations depends integer t [0...79]
 * Returns single 32-bit dword.
 *
 * @access public
 * @static
 */
SHA1.sha1Ft = function( B, C, D, t )
{
	if ( t < 20 )
		r = ( B & C ) | ( ~B & D );
	else if ( t < 40 )
		r = B ^ C ^ D;
	else if ( t < 60 )
		r = ( B & C ) | ( B & D ) | ( C & D );
	else
		r = B ^ C ^ D;

	return r;
};

/**
 * Computes hash for given array of 32-bit dwords
 * returns array containing five 32-bit dwords
 *
 * @access public
 * @static
 */
SHA1.sha1Compute = function( dwordArray )
{
	var wA, wB, wC, wD, wE, temp;
	
	var W = new Array( 80 );
	var H = SHA1.sha1HBuffer();
	var K = SHA1.sha1KBuffer();
		 
	for ( i = 0; i < 16; i++ )
		W[i] = dwordArray[i];
		 
	for ( i = 16; i < 80; i++ )
		W[i] = SHA1.sha1S( W[i-3] ^ W[i-8] ^ W[i-14] ^ W[i-16],1 );
		 
	wA = H[0];
	wB = H[1];
	wC = H[2];
	wD = H[3];
	wE = H[4];

	for ( i = 0; i < 80; i++ )
	{
		temp = SHA1.sha1S( wA, 5 ) + SHA1.sha1Ft( wB, wC, wD, i ) + wE + W[i] + K[i];
		wE   = wD;
		wD   = wC;
		wC   = SHA1.sha1S( wB, 30 );
		wB   = wA;
		wA   = temp;
	}

	H[0] += wA;
	H[1] += wB;
	H[2] += wC;
	H[3] += wD;
	H[4] += wE;
		 
	for ( i = 0; i < 5; i++ )
		H[i] &= 0xFFFFFFFF;

	return H;
};

/**
 * Returns 160 bit hexadecimal representation of the hash.
 *
 * @access public
 * @static
 */
SHA1.sha1 = function( inStr )
{
	var dWA    = SHA1.getSHA1Hash( inStr );
	var retStr = DWordInBytes.dWords2String( dWA );

	return retStr;
};
