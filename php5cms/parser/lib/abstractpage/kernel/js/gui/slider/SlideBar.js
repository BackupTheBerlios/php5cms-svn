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
 * @package gui_slider
 */
 
/**
 * Constructor
 *
 * @access public
 */
SlideBar = function()
{
	this.Base = Base;
	this.Base();
};


SlideBar.prototype = new Base();
SlideBar.prototype.constructor = SlideBar;
SlideBar.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
SlideBar.dragobject = null;

/**
 * @access public
 * @static
 */
SlideBar.onchange = "";

/**
 * @access public
 * @static
 */
SlideBar.down = function()
{
	// traverse the element tree
	var tmp = Util.getReal( window.event.srcElement, "className", "sliderHandle" );

	if ( tmp.className == "sliderHandle" )
	{
		// this is a global reference to the current dragging object
		SlideBar.dragobject = tmp;

		//set the onchange function
		SlideBar.onchange = SlideBar.dragobject.getAttribute( "onchange" );
		
		if ( SlideBar.onchange == null )
			SlideBar.onchange = "";
		
		SlideBar.type = SlideBar.dragobject.getAttribute( "type" );

		if ( SlideBar.type == "y" )
			SlideBar.ty = ( window.event.clientY - SlideBar.dragobject.style.pixelTop  );
		else
			SlideBar.tx = ( window.event.clientX - SlideBar.dragobject.style.pixelLeft );

		window.event.returnValue = false;
		window.event.cancelBubble = true;
	}
	else
	{
		SlideBar.dragobject = null;
	}
};

/**
 * @access public
 * @static
 */
SlideBar.up = function()
{
	if ( SlideBar.dragobject )
		SlideBar.dragobject = null;
};

/**
 * @access public
 * @static
 */
SlideBar.move = function()
{
	if ( SlideBar.dragobject )
	{
		if ( SlideBar.type == "y" )
		{
			if ( event.clientY >= 0 )
			{
				if ( ( event.clientY - SlideBar.ty >= 0 ) && ( event.clientY - SlideBar.ty <= SlideBar.dragobject.parentElement.offsetHeight - SlideBar.dragobject.offsetHeight ) )
					SlideBar.dragobject.style.top = event.clientY - SlideBar.ty;
				
				if ( event.clientY - SlideBar.ty < 0 )
					SlideBar.dragobject.style.top = "0";
				
				if ( event.clientY - SlideBar.ty > SlideBar.dragobject.parentElement.offsetHeight - SlideBar.dragobject.offsetHeight - 0 )
					SlideBar.dragobject.style.top = SlideBar.dragobject.parentElement.offsetHeight - SlideBar.dragobject.offsetHeight;

				SlideBar.dragobject.value = SlideBar.dragobject.style.pixelTop / ( SlideBar.dragobject.parentElement.offsetHeight - SlideBar.dragobject.offsetHeight );
				eval( SlideBar.onchange.replace(/this/g, "SlideBar.dragobject" ) );
			}
		}
		else
		{
			if ( event.clientX  >= 0 )
			{
				if ( ( event.clientX  - SlideBar.tx >= 0 ) && ( event.clientX - SlideBar.tx <= SlideBar.dragobject.parentElement.offsetWidth - SlideBar.dragobject.offsetWidth ) )
					SlideBar.dragobject.style.left = event.clientX - SlideBar.tx;
				
				if ( event.clientX - SlideBar.tx < 0 )
					SlideBar.dragobject.style.left = "0";
				
				if ( event.clientX - SlideBar.tx > SlideBar.dragobject.parentElement.clientWidth - SlideBar.dragobject.offsetWidth - 0 )
					SlideBar.dragobject.style.left = SlideBar.dragobject.parentElement.clientWidth - SlideBar.dragobject.offsetWidth;

				SlideBar.dragobject.value = SlideBar.dragobject.style.pixelLeft / ( SlideBar.dragobject.parentElement.clientWidth - SlideBar.dragobject.offsetWidth );
				eval( SlideBar.onchange.replace(/this/g, "SlideBar.dragobject" ) );
			}
		}
		
		window.event.returnValue  = false;
		window.event.cancelBubble = true;
	}
};

/**
 * @access public
 * @static
 */
SlideBar.setValue = function( el, val )
{
	el.value = val;
	
	if ( el.getAttribute( "TYPE" ) == "x" )
		el.style.left =  val * ( el.parentElement.clientWidth - el.offsetWidth );
	else
		el.style.top =  val * ( el.parentElement.clientHeight - el.offsetHeight );

	eval( el.onchange.replace(/this/g, "el") );
};
