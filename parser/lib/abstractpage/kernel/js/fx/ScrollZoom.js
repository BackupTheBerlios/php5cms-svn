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
 * @package fx
 */
 
/**
 * Constructor
 *
 * @access public
 */
ScrollZoom = function()
{
	this.Base = Base;
	this.Base();
};


ScrollZoom.prototype = new Base();
ScrollZoom.prototype.constructor = ScrollZoom;
ScrollZoom.superclass = Base.prototype;

/**
 * Distance from the edge that is dragable in pixels
 * @access public
 * @static
 */
ScrollZoom.offset = 10;

/**
 * Minumum x width
 * @access public
 * @static
 */
ScrollZoom.xMin = 30;

/**
 * @access public
 * @static
 */
ScrollZoom.yMin = 30;

/**
 * @access public
 * @static
 */
ScrollZoom.obj = null;

/**
 * @access public
 * @static
 */
ScrollZoom.down = function()
{
	// traverse the element tree
	var el = Util.getReal( window.event.srcElement, "className", "scrollZoomHandle" );

	if ( el.className == "scrollZoomHandle" )
	{
		// global reference to the current dragging object
		ScrollZoom.obj = new ResizeObject();
				
		ScrollZoom.obj.el     = el;
		ScrollZoom.obj.dir    = ScrollZoom.getDirection( el );
		ScrollZoom.obj.type   = el.getAttribute( "type" );
		ScrollZoom.obj.grabx  = window.event.clientX;
		ScrollZoom.obj.graby  = window.event.clientY;
		ScrollZoom.obj.width  = el.style.pixelWidth;
		ScrollZoom.obj.height = el.style.pixelHeight;
		ScrollZoom.obj.left   = el.style.pixelLeft;
		ScrollZoom.obj.top    = el.style.pixelTop;
		ScrollZoom.obj.parentWidth  = el.parentElement.clientWidth;
		ScrollZoom.obj.parentHeight = el.parentElement.clientHeight;

		window.event.returnValue  = false;
		window.event.cancelBubble = true;
	}
	else
	{
		ScrollZoom.obj = null;
	}
};

/**
 * @access public
 * @static
 */
ScrollZoom.up = function()
{
	if ( ScrollZoom.obj )
		ScrollZoom.obj = null;
};

/**
 * @access public
 * @static
 */
ScrollZoom.move = function()
{
	var el;
	var str;
	var xPos;
	var yPos;

	el = Util.getReal( event.srcElement, "className", "scrollZoomHandle" );

	if ( ( el.className == "scrollZoomHandle" ) && ( ScrollZoom.obj == null ) )
	{
		str = ScrollZoom.getDirection(el);
		
		// fix the cursor	
		if ( str == "" )
			str = "default";
		else
			str += "-resize";
		
		el.style.cursor = str;
	}
	
	if ( ScrollZoom.obj && ( ScrollZoom.obj.dir == "" ) )
	{
		if ( ScrollZoom.obj.type == "y" )
		{
			if ( event.clientY >= 0 )
			{
				if ( ( event.clientY - ScrollZoom.obj.graby + ScrollZoom.obj.top >= 0 ) && ( event.clientY - ScrollZoom.obj.graby + ScrollZoom.obj.top <= ScrollZoom.obj.el.parentElement.clientHeight - ScrollZoom.obj.el.style.pixelHeight ) )
					ScrollZoom.obj.el.style.top = event.clientY - ScrollZoom.obj.graby + ScrollZoom.obj.top;
				
				if ( event.clientY - ScrollZoom.obj.graby + ScrollZoom.obj.top < 0 )
					ScrollZoom.obj.el.style.top = "0";

				if ( event.clientY - ScrollZoom.obj.graby + ScrollZoom.obj.top > ScrollZoom.obj.el.parentElement.clientHeight - ScrollZoom.obj.el.style.pixelHeight - 0 )
					ScrollZoom.obj.el.style.top = ScrollZoom.obj.el.parentElement.clientHeight - ScrollZoom.obj.el.style.pixelHeight;

				ScrollZoom.handleFakeEvent( ScrollZoom.obj.el, "y" );
			}
		}
		else
		{
			if ( event.clientX  >= 0 )
			{
				if ( ( event.clientX  - ScrollZoom.obj.grabx + ScrollZoom.obj.left >= 0 ) && ( event.clientX - ScrollZoom.obj.grabx + ScrollZoom.obj.left <= ScrollZoom.obj.el.parentElement.clientWidth - ScrollZoom.obj.el.style.pixelWidth ) )
					ScrollZoom.obj.el.style.left = event.clientX - ScrollZoom.obj.grabx + ScrollZoom.obj.left;
				
				if ( event.clientX - ScrollZoom.obj.grabx + ScrollZoom.obj.left < 0 )
					ScrollZoom.obj.el.style.left = "0";
				
				if ( event.clientX - ScrollZoom.obj.grabx + ScrollZoom.obj.left > ScrollZoom.obj.el.parentElement.clientWidth - ScrollZoom.obj.el.style.pixelWidth - 0 )
					ScrollZoom.obj.el.style.left = ScrollZoom.obj.el.parentElement.clientWidth - ScrollZoom.obj.el.style.pixelWidth;

				ScrollZoom.handleFakeEvent( ScrollZoom.obj.el, "x" );
			}
		}
		
		window.event.returnValue  = false;
		window.event.cancelBubble = true;
	}
	
	// resizing starts here
	if ( ScrollZoom.obj && ( ScrollZoom.obj.dir != "" ) )
	{
		if ( ScrollZoom.obj.dir.indexOf( "s" ) != -1 )
		{
			var tmpHeight = window.event.clientY - ScrollZoom.obj.graby + ScrollZoom.obj.height;
			
			ScrollZoom.obj.el.style.height = Math.min( ScrollZoom.obj.parentHeight - ScrollZoom.obj.top, Math.max( ScrollZoom.yMin, tmpHeight ) );
			ScrollZoom.handleFakeEvent( ScrollZoom.obj.el, "y" );
		}
		
		if ( ScrollZoom.obj.dir.indexOf( "n" ) != -1 )
		{
			ScrollZoom.obj.el.style.top = Math.min( ScrollZoom.obj.top + ScrollZoom.obj.height - ScrollZoom.yMin, Math.max( 0, window.event.clientY - ScrollZoom.obj.graby + ScrollZoom.obj.top ) );
			ScrollZoom.obj.el.style.height = Math.max( ScrollZoom.yMin, Math.min( ScrollZoom.obj.top + ScrollZoom.obj.height, ScrollZoom.obj.graby - window.event.clientY + ScrollZoom.obj.height ) );
			ScrollZoom.handleFakeEvent( ScrollZoom.obj.el, "y" );
		}
		
		if ( ScrollZoom.obj.dir.indexOf( "e" ) != -1 )
		{
			var tmpWidth = window.event.clientX - ScrollZoom.obj.grabx + ScrollZoom.obj.width;
			
			ScrollZoom.obj.el.style.width = Math.min( ScrollZoom.obj.parentWidth - ScrollZoom.obj.left, Math.max( ScrollZoom.xMin, tmpWidth ) );
			ScrollZoom.handleFakeEvent( ScrollZoom.obj.el, "x" );
		}
		
		if ( ScrollZoom.obj.dir.indexOf( "w" ) != -1 )
		{
			ScrollZoom.obj.el.style.left  = Math.min( ScrollZoom.obj.left + ScrollZoom.obj.width - ScrollZoom.xMin, Math.max( 0, window.event.clientX - ScrollZoom.obj.grabx + ScrollZoom.obj.left ) );
			ScrollZoom.obj.el.style.width = Math.max( ScrollZoom.xMin, Math.min( ScrollZoom.obj.left  + ScrollZoom.obj.width, ScrollZoom.obj.grabx - window.event.clientX + ScrollZoom.obj.width ) );
			ScrollZoom.handleFakeEvent( ScrollZoom.obj.el, "x" );
		}				
		
		window.event.returnValue  = false;
		window.event.cancelBubble = true;
	} 
};

/**
 * @access public
 * @static
 */
ScrollZoom.getDirection = function( el )
{
	var xPos;
	var yPos;
	var dir;
	var tmpEl;
	var type;

	if ( ScrollZoom.obj && ScrollZoom.obj.dir )
	{
		dir = ScrollZoom.obj.dir;
	}
	else
	{
		type  = el.getAttribute( "TYPE" );
		dir   = "";
		xPos  = window.event.offsetX;
		yPos  = window.event.offsetY;
		tmpEl = window.event.srcElement;
		
		while ( tmpEl != el )
		{
			xPos += tmpEl.offsetLeft;
			yPos += tmpEl.offsetTop;
			tmpEl = tmpEl.offsetParent;
		}

		if ( type == "y" )
		{
			if ( yPos < ScrollZoom.offset )
				dir += "n";
			else if ( yPos > el.clientHeight - ScrollZoom.offset )
				dir += "s";
		}
		else
		{
			if ( xPos < ScrollZoom.offset )
				dir += "w";
			else if ( xPos >= el.clientWidth - ScrollZoom.offset )
				dir += "e";
		}
	
		return dir;
	}
};

/**
 * @access public
 * @static
 */
ScrollZoom.setValue = function( el, zoomValue, scrollValue )
{
	if ( zoomValue )
		el.zoomValue = zoomValue;
	
	if ( scrollValue )
		el.scrollValue = scrollValue;

	if ( el.getAttribute( "TYPE" ) == "x" )
	{
		if ( el.zoomValue && ( el.zoomValue != 0 ) ) // to prevent divide by zero
			el.style.width = el.parentElement.clientWidth / el.zoomValue;
		
		if ( el.scrollValue )
			el.style.left =  el.scrollValue * ( el.parentElement.clientWidth - el.style.pixelWidth );
	}
	else
	{
		if ( el.zoomValue && ( el.zoomValue != 0 ) )
			el.style.height = el.parentElement.clientHeight / el.zoomValue;
		
		if ( el.scrollValue )
			el.style.top =  el.scrollValue * ( el.parentElement.clientHeight - el.style.pixelHeight );
	}
	
	eval( el.onchange.replace(/this/g, "el") );
};

/**
 * @access public
 * @static
 */
ScrollZoom.handleFakeEvent = function( el, dir )
{
	var onchange;
	
	if ( dir == "y" )
	{
		el.scrollValue = el.style.pixelTop / ( el.parentElement.clientHeight - el.style.pixelHeight + 1 );
		el.zoomValue   = el.parentElement.clientHeight / el.style.pixelHeight;
		
		onchange = ScrollZoom.obj.el.getAttribute( "ONCHANGE" );
		
		if ( onchange )
			eval( onchange.replace(/this/g, "el") );
	}
	else
	{
		el.scrollValue = el.style.pixelLeft / ( el.parentElement.clientWidth - el.style.pixelWidth + 1 );
		el.zoomValue   = el.parentElement.clientWidth / el.style.pixelWidth;
		
		onchange = ScrollZoom.obj.el.getAttribute( "ONCHANGE" );
		
		if ( onchange )
			eval( onchange.replace(/this/g, "el") );
	}
};
