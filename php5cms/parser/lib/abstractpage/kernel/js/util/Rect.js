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
Rect = function( x, y, w, h )
{
	this.Base = Base;
	this.Base();

	this.x = x || 0;
	this.y = y || 0;
	this.w = w || 0;
	this.h = h || 0;
};


Rect.prototype = new Base();
Rect.prototype.constructor = Rect;
Rect.superclass = Base.prototype;

/**
 * @access public
 */
Rect.prototype.setX = function( x )
{
	if ( x != null )
		this.x = x;
};

/**
 * @access public
 */
Rect.prototype.setY = function( y )
{
	if ( y != null )
		this.y = y;
};

/**
 * @access public
 */
Rect.prototype.setW = function( w )
{
	if ( w != null )
		this.w = w;
};

/**
 * @access public
 */
Rect.prototype.setH = function( h)
{
	if ( h != null )
		this.h = h;
};

/**
 * @access public
 */
Rect.prototype.position = function( x, y )
{
	if ( x != null )
		this.x = x;

	if ( y != null )
		this.y = y;
};

/**
 * @access public
 */
Rect.prototype.getRect = function()
{
	return [ this.x, this.y, this.w, this.h ];
};

/**
 * @access public
 */
Rect.prototype.resize = function( w, h )
{
	if ( w != null )
		this.w = w;

	if ( h != null )
		this.h = h;
};
