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
 * if only one parameter is received, it assumes it is and HTML color ("#aa98ea").
 * Otherwise ( 3 parameters ) they are suposed to be valid r,g,b values from 0 to 255.
 *
 * @access public
 */
Color = function( r, g, b )
{
	this.Base = Base;
	this.Base();
	
	if ( arguments.length == 1 )
	{ 
		if ( r.charAt( 0 ) != '#' )
			r = '#' + r;
			
		g = parseInt( r.substring( 3, 5 ), 16 );
		b = parseInt( r.substring( 5, 7 ), 16 );
		r = parseInt( r.substring( 1, 3 ), 16 );
	}

	this.r = r;
	this.g = g;
	this.b = b;

	// initialize HLS values
	this.syncHLS();
};


Color.prototype = new Base();
Color.prototype.constructor = Color;
Color.superclass = Base.prototype;

/**
 * Used to export the color into HTML format.
 *
 * @access public
 */
Color.prototype.toString = function()
{
	var n = Math.round( this.b );
        
	n += Math.round( this.g ) << 8;
	n += Math.round( this.r ) << 16;

	return Color.DectoHex( n );
};

/**
 * Synchronizes RGB values to hls after a luminance operation has been performed.
 *
 * @access public
 */
Color.prototype.syncRGB = function()
{
	var p1;
	var p2;
	
	if ( this.l <= 0.5 )
		p2 = this.l * ( 1 + this.s );
	else
		p2 = this.l + this.s - ( this.l * this.s );

	p1 = 2 * this.l - p2;
        
	if ( this.s == 0 )
	{
		this.r = this.l;
		this.g = this.l;
		this.b = this.l;
	}
	else
	{
		this.r = Color.RGB( p1, p2, this.h + 120 );
		this.g = Color.RGB( p1, p2, this.h );
		this.b = Color.RGB( p1, p2, this.h - 120 );
	}
	
	this.r *= 255;
	this.g *= 255;
	this.b *= 255;
	
	this.r = Math.floor( this.r );
	this.g = Math.floor( this.g );
	this.b = Math.floor( this.b );
        
	if ( this.r > 255 )
		this.r = 255;
		
	if ( this.g > 255 )
		this.g = 255;
		
	if ( this.b > 255 )
		this.b = 255;
};

/**
 * Synchronizes HLS values to rgb after a color operation has taken place.
 *
 * @access public
 */
Color.prototype.syncHLS = function()
{
	var R = this.r / 255;
	var G = this.g / 255;
	var B = this.b / 255;
	
	var max;
	var min;
	var diff;
	var r_dist;
	var g_dist;
	var b_dist
        
	max = Color.MAX( R, G, B );
	min = Color.MIN( R, G, B );
	diff = max - min;
	this.l = (max + min) / 2;
        
	if ( diff == 0 )
	{
		this.h = 0;
		this.s = 0;
	}
	else
	{
		if ( this.l < 0.5 )
			this.s = diff / ( max + min );
		else
			this.s = diff / ( 2 - max - min );
		
		r_dist = ( max - R ) / diff;
		g_dist = ( max - G ) / diff;
		b_dist = ( max - B ) / diff;
 		
		if ( R == max )
			this.h = b_dist - g_dist;
		else if ( G == max )
			this.h = 2 + r_dist - b_dist;
		else if ( B == max )
			this.h = 4 + g_dist - r_dist;

		this.h *= 60;
		
		if ( this.h < 0 )
			this.h += 360;
			
		if ( this.h >= 360 )
			this.h -= 360;
	}
};

/**
 * Adds another color to this one.
 *
 * @access public
 */
Color.prototype.add = function( c )
{
	this.r += c.r;
	this.g += c.g;
	this.b += c.b;

	this.syncHLS();
};

/**
 * Substracts another color from this one.
 *
 * @access public
 */
Color.prototype.subs = function( c )
{
	this.r -= c.r;
	this.g -= c.g;
	this.b -= c.b;

	this.syncHLS();
};

/**
 * Makes color n% darker.
 *
 * @access public
 */
Color.prototype.dark = function( n )
{
	this.l -= ( ( this.l ) * n / 100 );
	this.syncRGB();
};

/**
 * Makes color n% lighter.
 *
 * @access public
 */
Color.prototype.light = function( n )
{
	this.l += ( ( 1 - this.l ) * n / 100 );
	this.syncRGB();
};


/**
 * Converts a numerical value into its HEX equivalent, so to export our color to HTML format.
 *
 * @access public
 * @static
 */
Color.DectoHex = function( num )
{
	var i = 0;
	var j = 20;
	var str = "#";
	
	while( j >= 0 )
	{
		i = Color.mod( ( num >> j ), 16 );

		if ( i >= 10 )
		{
			if ( i == 10 )
				str += "A";
			else if( i == 11 )
				str += "B";
			else if( i == 12 )
				str += "C";
			else if( i == 13 )
				str += "D";
			else if( i == 14 )
				str += "E";
			else
				str += "F";
		}
		else
		{
			str += i;
		}
		
		j -= 4;
	}

	return str;
};

/**
 * @access public
 * @static
 */
Color.MIN = function()
{
	var min = 255;
	
	for ( var i = 0; i < arguments.length; i++ )
	{
		if ( arguments[i] < min )
			min = arguments[i];
	}

	return min;
};

/**
 * @access public
 * @static
 */
Color.MAX = function()
{
	var max = 0;
	
	for ( var i = 0; i < arguments.length; i++ )
	{
		if ( arguments[i] > max )
			max = arguments[i];
	}

	return max;
};

/**
 * @access public
 * @static
 */
Color.RGB = function( q1, q2, hue )
{
	if ( hue > 360 )
		hue = hue - 360;
		
	if ( hue < 0 )
		hue = hue + 360;
	
	if ( hue < 60 )
		return ( q1 + ( q2 - q1 ) * hue / 60 );
	else if ( hue < 180 )
		return( q2 );
	else if ( hue < 240 )
		return( q1 + ( q2 - q1 ) * ( 240 - hue ) / 60 );
	else
		return( q1 );
};

/**
 * @access public
 * @static
 */
Color.mod = function( a, b )
{
	return a % b;
};

/**
 * @access public
 * @static
 */
Color.HSV2RGB = function( hsv )
{
	var rgb = new Object();
	var i, f, p, q, t;

	if ( hsv.s == 0 )
	{
		// achromatic (grey)
		rgb.r = rgb.g = rgb.b = hsv.v;
		return rgb;
	}
	
	hsv.h /= 60; // sector 0 to 5
	i = Math.floor( hsv.h );
	f = hsv.h - i; // factorial part of h
	p = hsv.v * ( 1 - hsv.s );
	q = hsv.v * ( 1 - hsv.s * f );
	t = hsv.v * ( 1 - hsv.s * ( 1 - f ) );
	
	switch( i )
	{
		case 0 :
			rgb.r = hsv.v;
			rgb.g = t;
			rgb.b = p;
			
			break;
			
		case 1 :
			rgb.r = q;
			rgb.g = hsv.v;
			rgb.b = p;
			
			break;
			
		case 2 :
			rgb.r = p;
			rgb.g = hsv.v;
			rgb.b = t;
			
			break;
			
		case 3 :
			rgb.r = p;
			rgb.g = q;
			rgb.b = hsv.v;
			
			break;
			
		case 4 :
			rgb.r = t;
			rgb.g = p;
			rgb.b = hsv.v;
			
			break;
			
		default :
			rgb.r = hsv.v;
			rgb.g = p;
			rgb.b = q;
			
			break;
	}
	
	return rgb;
};
