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
ColorBlend = function( colorFrom, colorTo )
{
	this.Base = Base;
	this.Base();
	
	// assign object variables
	this.from = colorFrom;
	this.to   = colorTo;

	return this;
};


ColorBlend.prototype = new Base();
ColorBlend.prototype.constructor = ColorBlend;
ColorBlend.superclass = Base.prototype;

/**
 * Calculates the color mixed by colorForm and colorTo.
 *
 * @param  double  alpha  The blend factor (a real value between 0 and 1)
 * @access public
 */
ColorBlend.prototype.getColor = function( alpha )
{
	a = alpha;
	b = 1 - alpha;
	c = new ColorUtil();
	
	c.setRGB(
		Math.round( this.from.r * a + this.to.r * b ),
		Math.round( this.from.g * a + this.to.g * b ),
		Math.round( this.from.b * a + this.to.b * b )
	);	

	return c;
};

/**
 * Returns a string representation of the object.
 *
 * @access public
 */
ColorBlend.prototype.toString = function()
{
	return "ColorBlend(" + this.from + ", " + this.to + ")";
};
