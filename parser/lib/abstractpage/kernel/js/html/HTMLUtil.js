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
 * @package html
 */
 
/**
 * Constructor
 *
 * @access public
 */
HTMLUtil = function()
{
	this.Base = Base;
	this.Base();
};


HTMLUtil.prototype = new Base();
HTMLUtil.prototype.constructor = HTMLUtil;
HTMLUtil.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
HTMLUtil.giveDec = function( hex ) 
{
	var value;
	
	if ( hex == "A" ) 
		value = 10;
	else if ( hex == "B" ) 
		value = 11;
	else if ( hex == "C" ) 
		value = 12;
	else if ( hex == "D" ) 
		value = 13;
	else if ( hex == "E" ) 
		value = 14;
	else if ( hex == "F" ) 
		value = 15;
	else 
		value = eval( hex );
	
	return value;
};

/**
 * @access public
 * @static
 */
HTMLUtil.giveHex = function( dec ) 
{
	var value;
	
	if ( dec == 10 ) 
		value = "A";
	else if ( dec == 11 ) 
		value = "B";
	else if ( dec == 12 ) 
		value = "C";
	else if ( dec == 13 ) 
		value = "D";
	else if ( dec == 14 ) 
		value = "E";
	else if ( dec == 15 ) 
		value = "F";
	else 
		value = "" + dec;
		
	return value;
};

/**
 * @access public
 * @static
 */
HTMLUtil.hexToDec = function( input ) 
{
	input = input.toUpperCase();
	
	var a = HTMLUtil.giveDec( input.substring( 0, 1 ) ); 
	var b = HTMLUtil.giveDec( input.substring( 1, 2 ) );
	var c = HTMLUtil.giveDec( input.substring( 2, 3 ) );
	var d = HTMLUtil.giveDec( input.substring( 3, 4 ) );
	var e = HTMLUtil.giveDec( input.substring( 4, 5 ) );
	var f = HTMLUtil.giveDec( input.substring( 5, 6 ) );
	
	var outRed   = ( a * 16 ) + b;
	var outGreen = ( c * 16 ) + d;
	var outBlue  = ( e * 16 ) + f;
	
	var out = new Array( outRed, outGreen, outBlue );
	return out;
};

/**
 * @access public
 * @static
 */
HTMLUtil.decToHex = function( red, green, blue ) 
{
	var a = HTMLUtil.giveHex( Math.floor( red   / 16 ) );
	var b = HTMLUtil.giveHex( red % 16 );
	var c = HTMLUtil.giveHex( Math.floor( green / 16 ) );
	var d = HTMLUtil.giveHex( green % 16 );
	var e = HTMLUtil.giveHex( Math.floor( blue  / 16 ) );
	var f = HTMLUtil.giveHex( blue % 16 );
	
	var out = a + b + c + d + e + f;
	return out;
};

/**
 * @access public
 * @static
 */
HTMLUtil.filterForHtml = function( str ) 
{
	str = str.replace( /</g,  "&lt;"   );
	str = str.replace( />/g,  "&gt;"   );
	str = str.replace( /"/g,  "&quot;" );
	str = str.replace( /'/g,  "&#39;"  );
	str = str.replace( /\n/g, "&#10;"  );
	str = str.replace( /\r/g, "&#13;"  );
	
	return str;
};

/**
 * @access public
 * @static
 */
HTMLUtil.filterForHtml2 = function( str ) 
{
	str = HTMLUtil.filter( "&lt;", "<", str );
	str = HTMLUtil.filter( "&gt;", ">", str );
	str = str.replace( "<>", "" );
	
	return str;
};

/**
 * @access public
 * @static
 */
HTMLUtil.filter = function( inTag, outTag, inString ) 
{
	split = inString.split( inTag );
	var outString = '';
	
	if ( split.length > 0 ) 
	{
		for ( i = 0; i < split.length; i++ ) 
		{
			if ( i == split.lenth )
				outString += split[i];
			else
				outString += split[i] + outTag;
		}

		return outString;
	} 
	else 
	{
		return inString;
	}
};
