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
 * DWordInBytes Object
 * An object constructed from argument 32-bit dword
 * contains four 8-bit bytes separated from argument.
 *
 * @package security
 */
 
/**
 * Constructor
 *
 * @access public
 */
DWordInBytes = function( dW )
{
	this.Base = Base;
	this.Base();
	
	this.rB = new Array( 4 );

	this.rB[0] =   dW & 0x000000FF;
	this.rB[1] = ( dW & 0x0000FF00 ) >>>  8;
	this.rB[2] = ( dW & 0x00FF0000 ) >>> 16;
	this.rB[3] = ( dW & 0xFF000000 ) >>> 24;

	// returns hex representation of the object
	this.toString = CryptUtil.bytes2String( this.rB );
};


DWordInBytes.prototype = new Base();
DWordInBytes.prototype.constructor = DWordInBytes;
DWordInBytes.superclass = Base.prototype;

/**
 * Constructs dWordInBytes from four first characters of argument.
 *
 * @access public
 * @static
 */
DWordInBytes.dWordInBytesFromString = function( str )
{
	var retDw = new DWordInBytes( 0 );

	retDw.rB[0] = str.charCodeAt( 0 );
	retDw.rB[1] = str.charCodeAt( 1 );
	retDw.rB[2] = str.charCodeAt( 2 );
	retDw.rB[3] = str.charCodeAt( 3 );

	return retDw;
};

/**
 * Argument dWA is array of 32-bit dwords
 * returns concatenated hex representation of
 * argument dwords.
 * dWords2String(Array(0xFF0A12AA, 0xFF00FF00))
 *   --> String("FF0A12AAFF00FF00")
 *
 * @access public
 * @static
 */
DWordInBytes.dWords2String = function( dWA )
{
	var i = 0;
	var retStr = new String;
	var aL = dWA.length;
	var d  = new String;
   
	for ( i = 0; i < aL; i++ )
	{
		d = new DWordInBytes( dWA[i] );
		retStr = retStr + d.toString;
	}

	return retStr;
};
