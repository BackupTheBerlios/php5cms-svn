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
 * @package fx_resize
 */
 
/**
 * Constructor
 *
 * @access public
 */
Resize = function()
{
	this.Base = Base;
	this.Base();
};


Resize.prototype = new Base();
Resize.prototype.constructor = Resize;
Resize.superclass = Base.prototype;

/**
 * This gets a value as soon as a resize start
 * @access public
 * @static
 */
Resize.theobject = null;

/**
 * Find out what kind of resize! Return a string inlcluding the directions.
 *
 * @access public
 * @static
 */
Resize.getDirection = function( el )
{
	var xPos, yPos, offset;
	var dir = "";

	xPos = window.event.offsetX;
	yPos = window.event.offsetY;

	// distance from the edge in pixels
	offset = 8;

	if ( yPos < offset )
		dir += "n";
	else if ( yPos > el.offsetHeight - offset )
		dir += "s";
	
	if ( xPos < offset )
		dir += "w";
	else if ( xPos > el.offsetWidth - offset )
		dir += "e";

	return dir;
};

/**
 * @access public
 * @static
 */
Resize.doDown = function()
{
	var el = Util.getReal( event.srcElement, "className", "resizeMe" );

	if ( el.className != "resizeMe" )
	{
		Resize.theobject = null;
		return;
	}		

	dir = Resize.getDirection( el );

	if ( dir == "" )
		return;

	Resize.theobject = new ResizeState();
	
	Resize.theobject.el     = el;
	Resize.theobject.dir    = dir;
	Resize.theobject.grabx  = window.event.clientX;
	Resize.theobject.graby  = window.event.clientY;
	Resize.theobject.width  = el.offsetWidth;
	Resize.theobject.height = el.offsetHeight;
	Resize.theobject.left   = el.offsetLeft;
	Resize.theobject.top    = el.offsetTop;

	window.event.returnValue  = false;
	window.event.cancelBubble = true;
};

/**
 * @access public
 * @static
 */
Resize.doUp = function()
{
	if ( Resize.theobject != null )
		Resize.theobject = null;
};

/**
 * @access public
 * @static
 */
Resize.doMove = function()
{
	var el, xPos, yPos, str;
	var xMin = 8; // smallest width possible
	var yMin = 8; // smallest height possible

	el = Util.getReal( event.srcElement, "className", "resizeMe" );

	if ( el.className == "resizeMe" )
	{
		str = Resize.getDirection( el );
	
		// fix the cursor	
		if ( str == "" )
			str = "default";
		else
			str += "-resize";
		
		el.style.cursor = str;
	}
	
	// dragging starts here
	if ( Resize.theobject != null )
	{
		if ( dir.indexOf( "e" ) != -1 )
			Resize.theobject.el.style.width = Math.max( xMin, Resize.theobject.width + window.event.clientX - Resize.theobject.grabx ) + "px";
		
		if ( dir.indexOf( "s" ) != -1 )
			Resize.theobject.el.style.height = Math.max( yMin, Resize.theobject.height + window.event.clientY - Resize.theobject.graby ) + "px";
		
		if ( dir.indexOf( "w" ) != -1 )
		{
			Resize.theobject.el.style.left  = Math.min( Resize.theobject.left + window.event.clientX - Resize.theobject.grabx, Resize.theobject.left + Resize.theobject.width - xMin ) + "px";
			Resize.theobject.el.style.width = Math.max( xMin, Resize.theobject.width - window.event.clientX + Resize.theobject.grabx ) + "px";
		}

		if ( dir.indexOf( "n" ) != -1 )
		{
			Resize.theobject.el.style.top    = Math.min( Resize.theobject.top + window.event.clientY - Resize.theobject.graby, Resize.theobject.top + Resize.theobject.height - yMin ) + "px";
			Resize.theobject.el.style.height = Math.max( yMin, Resize.theobject.height - window.event.clientY + Resize.theobject.graby ) + "px";
		}
		
		window.event.returnValue  = false;
		window.event.cancelBubble = true;
	} 
};
