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
Boundaries = function()
{
	this.Base = Base;
	this.Base();
	
	if ( arguments.length == 1 )
	{
		this.left   = Boundaries.leftPos( arguments[0] );
		this.top    = Boundaries.topPos( arguments[0] );
		this.width  = arguments[0].offsetWidth;
		this.height = arguments[0].offsetHeight;
	}
	
	if ( arguments.length >= 2 )
	{
		this.left   = arguments[0];
		this.top    = arguments[1];
		this.width  = 0;
		this.height = 0;
	}
	
	if ( arguments.length >= 4 )
	{
		this.width  = arguments[2];
		this.height = arguments[3];
	}
};


Boundaries.prototype = new Base();
Boundaries.prototype.constructor = Boundaries;
Boundaries.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
Boundaries.topPos = function( el )
{
	return Boundaries.doPosLoop( el, "Top" );
};

/**
 * @access public
 * @static
 */
Boundaries.leftPos = function( el )
{
	return Boundaries.doPosLoop( el, "Left" );
};

/**
 * @access public
 * @static
 */
Boundaries.doPosLoop = function( el, val )
{
	var temp = el;
	var x = temp["offset" + val];
	
	while ( temp.tagName != "BODY" )
	{
		temp = temp.offsetParent;
		x += temp["offset" + val];
	}
	
	return x;
};
