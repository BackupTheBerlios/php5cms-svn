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
 * @package util_color
 */
 
/**
 * Constructor
 *
 * @access public
 */
ColorUtil = function( hex )
{
	this.Base = Base;
	this.Base();
	
	// if the hex parameter is omitted, black is used
	this.hex = ( ColorUtil.arguments.length > 0 )? hex : "000000";
	
	// validate and assign color
	rgb = ColorUtil.hexToRGB( this.hex );
	this.r = rgb[0];
	this.g = rgb[1];
	this.b = rgb[2];

	return this;
};


ColorUtil.prototype = new Base();
ColorUtil.prototype.constructor = ColorUtil;
ColorUtil.superclass = Base.prototype;

/**
 * Sets the color using RGB values (0-255).
 *
 * @access public
 */
ColorUtil.prototype.setRGB = function( r, g, b )
{
	if ( ( r >= 0 ) && ( r < 256 ) && ( g >= 0 ) && ( g < 256 ) && ( b >= 0 ) && ( b < 256 ) )
	{
		this.r = r;
		this.g = g;
		this.b = b;
		
		this.hex = ColorUtil.RGBToHex( r, g, b );
		
		return this.hex;
	}
};

/**
 * @access public
 */
ColorUtil.prototype.getNearestWebsafe = function( r, g, b )
{
	return new Array(
		ColorUtil._nextNearest( r ),
		ColorUtil._nextNearest( g ),
		ColorUtil._nextNearest( b )
	);
};

/**
 * @access public
 */
ColorUtil.prototype.getNearestGrayscale = function( r, g, b )
{
	var gray = ( r + g + b ) / 3;
	
	if ( Math.round( gray ) == gray )
	{
		return new Array( gray, gray, gray );
	}
	else
	{
		gray = Math.round( gray );
		return new Array( gray, gray, gray );
	}
};

/**
 * Returns the current color in hex format.
 *
 * @access public
 */
ColorUtil.prototype.getHex = function()
{
	return "#" + this.hex;
};

/**
 * Sets the current color in hex format.
 *
 * @access public
 */
ColorUtil.prototype.setHex = function( hex )
{
	rgb = ColorUtil.hexToRGB( hex );
	
	// set color using the setRGB() method which does the errorchecking as well
	this.setRGB( rgb[0], rgb[1], rgb[2] );
};

/**
 * Returns a string representation of the object.
 *
 * @access public
 */
ColorUtil.prototype.toString = function()
{
	return "#" + this.hex + " = (" + this.r + ", " + this.g + ", " + this.b + ")";
};


/**
 * A string constant used for decimal-hex-conversion (see helper functions at the bottom).
 * @access public
 * @static
 */
ColorUtil.HEXVALUES = "0123456789ABCDEF";

/**
 * @access public
 * @static
 */
ColorUtil.RGBToHex = function( r, g, b )
{
	return "" +
		ColorUtil.HEXVALUES.charAt( Math.floor( r / 16 ) ) + ColorUtil.HEXVALUES.charAt( r % 16 ) +
		ColorUtil.HEXVALUES.charAt( Math.floor( g / 16 ) ) + ColorUtil.HEXVALUES.charAt( g % 16 ) +
		ColorUtil.HEXVALUES.charAt( Math.floor( b / 16 ) ) + ColorUtil.HEXVALUES.charAt( b % 16 );
};

/**
 * @access public
 * @static
 */
ColorUtil.hexToRGB = function( hex )
{
	hex += "";
	
	// turn hex into uppercase and cut out the interesting part
	hex = hex.toUpperCase().substr( ( hex.charAt( 0 ) == "#" )? 1 : 0, ( hex.charAt( 0 ) == "#" )? 7 : 6 );
	
	var rgb = new Array( 0, 0, 0 );
	
	for ( j = 0; j < 3; j++ )
		rgb[j] = ColorUtil.HEXVALUES.indexOf( hex.charAt( j * 2 ) ) * 16 + ColorUtil.HEXVALUES.indexOf( hex.charAt( j * 2 + 1 ) );
	
	return rgb;
};

/**
 * @access public
 * @static
 */
ColorUtil.hexToInt = function( h )
{
	return parseInt( h.substring( 1 ), 16 );
};

/**
 * @access public
 * @static
 */
ColorUtil.intToHex = function( i )
{
  	i = i.toString( 16 );
  
  	while ( i.length < 6 )
		i = "0" + i;
  
  	return "#" + i;
};

/**
 * @access public
 * @static
 */
ColorUtil.makeSureIsHexColor = function( s )
{
  	// netscape 6 seems to return colors in rgb(0,0,0) format, even in you set
  	// the color using hex format #FFFFFF.
  	// this function will detect that and convert it to hex for use in the other
  	// color routines (lightenColor, darkenColor, etc...)
  	// - new in 2.07
  	// If color_c.js has been included on this page, then this function can attempt
  	// to resolve color constants such as "red" into their hex equivalents
  	
	// must be ns, of course
	if ( s.substring( 0, 4 ) == "rgb(" )
	{
    	var temp = s.split( "rgb(")[1].split( "," ); // pull out rgb values
    
		// convert them to integers
		for ( var i = 0; i < temp.length; i++ )
			temp[i] = parseInt( temp[i] );
    
		return ( ColorUtil.RGBToHex( temp[0], temp[1], temp[2] ) );
  	}
	
	return s;
};


// private methods

/**
 * @access private
 * @static
 */
ColorUtil._nextNearest = function( value )
{
	var reminder = value % 51;
	
	if ( reminder > 25 )
		value = value - reminder + 51;
	else
		value = value - reminder;
		
	return value;
};
