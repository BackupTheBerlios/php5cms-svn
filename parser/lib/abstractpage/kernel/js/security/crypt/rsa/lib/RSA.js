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
|Authors: Dave Shapiro <dave@ohdave.com>                               |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package security_crypt_rsa_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
RSA = function()
{
	this.Base = Base;
	this.Base();
};


RSA.prototype = new Base();
RSA.prototype.constructor = RSA;
RSA.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
RSA.twoDigit = function( n )
{
	return ( n < 10? "0" : "" ) + String( n );
};

/**
 * Altered by Rob Saunders (rob@robsaunders.net). New routine pads the
 * string after it has been converted to an array. This fixes an
 * incompatibility with Flash MX's Actionscript.
 *
 * @access public
 * @static
 */
RSA.encrypt = function( key, s )
{
	var a  = new Array();
	var sl = s.length;
	var i  = 0;
	
	while ( i < sl ) 
	{
		a[i] = s.charCodeAt( i );
		i++;
	}

	while ( ( a.length % key.chunkSize ) != 0 )
		a[i++] = 0;

	var al = a.length;
	var result = "";
	var i, j, k, block;
	
	for ( i = 0; i < al; i += key.chunkSize ) 
	{
		block = new BigInt( "" );
		j = 0;
		
		for ( k = i; k < i + key.chunkSize; ++j ) 
		{
			block.digits[j]  = a[k++];
			block.digits[j] += a[k++] << 8;
		}
		
		var crypt = BigInt.powMod( block, key.e, key.m );
		result += BigInt.toHex( crypt ) + " ";
	}
	
	return result.substr( 0, result.length - 1 ); // Remove last space.
};

/**
 * @access public
 * @static
 */
RSA.decrypt = function( key, s )
{
	var blocks = s.split( " " );
	var result = "";
	var i, j, block;
	
	for ( i = 0; i < blocks.length; ++i ) 
	{
		block = BigInt.powMod( BigInt.fromHex( blocks[i] ), key.d, key.m );
		
		for ( j = 0; j <= BigInt.numDigits( block ); ++j ) 
			result += String.fromCharCode( block.digits[j] & 255, block.digits[j] >> 8 );
	}
	
	return result;
};
