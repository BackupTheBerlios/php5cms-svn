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
 * CRC32 Object (Cyclic Redundancy Check implementation)
 * ISO 3309 compatible CRC-32 check sum counter
 *
 * Object containing 256 cell table of 'magic numbers',
 * initialiazed when constructed.
 *
 * @package security_checksum
 */
 
/**
 * Constructor
 *
 * @access public
 */
CRC32 = function()
{
	this.Base = Base;
	this.Base();
	
	this.table = new Array( 256 );
   
	var c = 0;
	var i = 0;
	var k = 0;
    
	for ( i = 0; i < 256; i++ )
	{
		c = i;
		
		for ( k = 0; k < 8; k++ )
		{
			if ( c & 1 )
				c = 0xEDB88320 ^ ( c >> 1 );
			else
				c = c >> 1;
		}
		
		this.table[i] = c;
    }
};


CRC32.prototype = new Base();
CRC32.prototype.constructor = CRC32;
CRC32.superclass = Base.prototype;

/**
 * Returns next CRC state, the preceeding
 * CRC value is given as argument.
 *
 * @access public
 * @static
 */
CRC32.nextCRC = function( preCrc, inStr )
{
	var sL   = inStr.length;
	var c    = preCrc ^ 0xFFFFFFFF;
	var n    = 0;
	var crcT = new CRC32();

	for ( n = 0; n < sL; n++ )
		c = crcT.table[( c ^ inStr.charCodeAt( n ) ) & 0xff] ^ ( c >> 8 );

	return c ^ 0xffffffff;
};

/**
 * Returns CRC fr given string.
 *
 * @access public
 * @static
 */
CRC32.countCRC = function( str )
{
	return CRC32.nextCRC( 0, str );
};

/**
 * Returns CRC for given string in
 * hexadecimal representation.
 *
 * @access public
 * @static
 */
CRC32.get = function( str )
{
	var c  = CRC32.countCRC( str );
	var cB = new DWordInBytes( c );
    
	return cB.toString;
};
