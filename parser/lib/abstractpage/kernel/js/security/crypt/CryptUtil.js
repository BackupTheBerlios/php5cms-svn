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
 * @package security_crypt
 */
 
/**
 * Constructor
 *
 * @access public
 */
CryptUtil = function()
{
	this.Base = Base;
	this.Base();
};


CryptUtil.prototype = new Base();
CryptUtil.prototype.constructor = CryptUtil;
CryptUtil.superclass = Base.prototype;

/**
 * Returns string representation of i in hex.
 * 0xFF --> "FF"
 * 0x01 --> "01"
 *
 * @access public
 * @static
 */
CryptUtil.twoCharHex = function( i )
{
	if ( i == 0 )
		return "00";
	else if ( i < 16 )
		return "0" + i.toString( 16 );

	return i.toString( 16 );
};

/**
 * Returns ASCII hex values of characters,
 * i.e. "AA" --> "4141"
 *
 * @access public
 * @static
 */
CryptUtil.str2hex = function( inStr )
{
	var l = inStr.length;
	var i = 0;
	var retStr = new String;

	for ( i = 0; i < l; i++ )
		retStr = retStr + CryptUtil.twoCharHex( inStr.charCodeAt( i ) );

	return retStr;
};

/**
 * @access public
 * @static
 */
CryptUtil.hex2str = function( inStr )
{
	var l = inStr.length;
	var retStr = new String;
	var i = 0;

	for ( i = 0; i < l; i += 2 )
		retStr = retStr + String.fromCharCode( "0x" + inStr.substr( i, 2 ) );

	return retStr;
};

/**
 * Converts array of bytes to string of hex values
 * returns String containing the argument bytes
 * in concatenated hex (Array(15,15) --> "0F0F").
 *
 * @access public
 * @static
 */
CryptUtil.bytes2String = function( aB )
{
	var tA = new Array( 4 );
	var rS = new String;

	for ( i = 3; i >= 0; i-- )
	{
		if ( aB[i] == 0 )
			tA[i] = "00";
		else if ( aB[i] < 16 )
			tA[i] = "0" + aB[i].toString( 16 );
		else
			tA[i] = aB[i].toString( 16 );

		rS = rS + tA[i];
	}
	
	return rS;
};
