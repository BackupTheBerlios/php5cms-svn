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
 * @package util
 */
 
/**
 * Constructor
 *
 * @access public
 */
NumConversion = function()
{
	this.Base = Base;
	this.Base();
};


NumConversion.prototype = new Base();
NumConversion.prototype.constructor = NumConversion;
NumConversion.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
NumConversion.binary = 0;

/**
 * @access public
 * @static
 */
NumConversion.ternary = 0;

/**
 * @access public
 * @static
 */
NumConversion.quintal = 0;

/**
 * @access public
 * @static
 */
NumConversion.octal = 0;

/**
 * @access public
 * @static
 */
NumConversion.decimal = 0;

/**
 * @access public
 * @static
 */
NumConversion.hexadecimal = 0;

/**
 * @access public
 * @static
 */
NumConversion.hex = new Array( "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F" );


/**
 * @access public
 * @static
 */
NumConversion.fromDecimal = function( N, radix )
{
	s = "";
	A = N;

	while ( A >= radix )
	{
		B  = A % radix;
		A  = Math.floor( A / radix );
		s += NumConversion.hex[B];
	}

	s += NumConversion.hex[A];
	return NumConversion.transpose( s );
};

/**
 * @access public
 * @static
 */
NumConversion.transpose = function( s )
{
	N = s.length;
	t = "";

	for ( i = 0; i < N; i++ )
		t = t + s.substring( N - i - 1, N - i );

	s = t;
	return s;
};

/**
 * @access public
 * @static
 */
NumConversion.evalBinary = function( val )
{
	if ( val.length != 0 )
		M = parseInt( val, 2 );
	else
		M = 0;

	NumConversion.binary      = val;
	NumConversion.ternary     = NumConversion.fromDecimal( M, 3  );
	NumConversion.quintal     = NumConversion.fromDecimal( M, 5  );
	NumConversion.octal       = NumConversion.fromDecimal( M, 8  );
	NumConversion.hexadecimal = NumConversion.fromDecimal( M, 16 );
	NumConversion.decimal     = M;
};

/**
 * @access public
 * @static
 */
NumConversion.evalTernary = function( val )
{
	if ( val.length != 0 )
		M = parseInt( val, 3 );
	else
		M = 0;

	NumConversion.ternary     = val;
	NumConversion.binary      = NumConversion.fromDecimal( M, 2  );
	NumConversion.quintal     = NumConversion.fromDecimal( M, 5  );
	NumConversion.octal       = NumConversion.fromDecimal( M, 8  );
	NumConversion.hexadecimal = NumConversion.fromDecimal( M, 16 );
	NumConversion.decimal     = M;
};

/**
 * @access public
 * @static
 */
NumConversion.evalQuintal = function( val )
{
	if ( val.length != 0 )
		M = parseInt( val, 5 );
	else
		M = 0;

	NumConversion.quintal     = val;
	NumConversion.binary      = NumConversion.fromDecimal( M, 2  );
	NumConversion.ternary     = NumConversion.fromDecimal( M, 3  );
	NumConversion.octal       = NumConversion.fromDecimal( M, 8  );
	NumConversion.hexadecimal = NumConversion.fromDecimal( M, 16 );
	NumConversion.decimal     = M;
};

/**
 * @access public
 * @static
 */
NumConversion.evalOctal = function( val )
{
	if ( val.length != 0 )
		M = parseInt( val, 8 );
	else
		M = 0;

	NumConversion.octal		  = val;
	NumConversion.binary      = NumConversion.fromDecimal( M, 2  );
	NumConversion.ternary     = NumConversion.fromDecimal( M, 3  );
	NumConversion.quintal     = NumConversion.fromDecimal( M, 5  );
	NumConversion.hexadecimal = NumConversion.fromDecimal( M, 16 );
	NumConversion.decimal     = M;
};

/**
 * @access public
 * @static
 */
NumConversion.evalHexadecimal = function( val )
{
	if ( val.length != 0 )
		M = parseInt( val, 16 );
	else
		M = 0;

	NumConversion.hexadecimal = val;
	NumConversion.binary  	  = NumConversion.fromDecimal( M, 2 );
	NumConversion.ternary     = NumConversion.fromDecimal( M, 3 );
	NumConversion.quintal     = NumConversion.fromDecimal( M, 5 );
	NumConversion.octal       = NumConversion.fromDecimal( M, 8 );
	NumConversion.decimal     = M;
};

/**
 * @access public
 * @static
 */
NumConversion.evalDecimal = function( val )
{
	if ( val.length != 0 )
		M = parseInt( val, 10 );
     else
		M = 0;

	NumConversion.decimal     = val;
	NumConversion.binary      = NumConversion.fromDecimal( M, 2  );
	NumConversion.ternary     = NumConversion.fromDecimal( M, 3  );
	NumConversion.quintal     = NumConversion.fromDecimal( M, 5  );
	NumConversion.octal       = NumConversion.fromDecimal( M, 8  );
	NumConversion.hexadecimal = NumConversion.fromDecimal( M, 16 );
};
