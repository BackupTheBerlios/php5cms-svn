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
GenMove = function()
{
	this.Base = Base;
	this.Base();
};


GenMove.prototype = new Base();
GenMove.prototype.constructor = GenMove;
GenMove.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
GenMove.checkZIndex = true;

/**
 * @access public
 * @static
 */
GenMove.dragobject = null;

/**
 * @access public
 * @static
 */
GenMove.down = function()
{
	var el = Util.getReal( window.event.srcElement );
	
	if ( el.className == "moveme" || el.className == "handle" )
	{
		if ( el.className == "handle" )
		{
			tmp = el.getAttribute( "handlefor" );
			
			if ( tmp == null )
			{
				GenMove.dragobject = null;
				return;
			}
			else
			{
				GenMove.dragobject = eval( tmp );
			}
		}
		else
		{
			GenMove.dragobject = el;
		}
		
		if ( GenMove.checkZIndex )
			GenMove.makeOnTop( GenMove.dragobject );
		
		GenMove.ty = window.event.clientY - GenMove.getTopPos( GenMove.dragobject );
		GenMove.tx = window.event.clientX - GenMove.getLeftPos( GenMove.dragobject );
		
		window.event.returnValue  = false;
		window.event.cancelBubble = true;
	}
	else
	{
		GenMove.dragobject = null;
	}
};

/**
 * @access public
 * @static
 */
GenMove.up = function()
{
	if ( GenMove.dragobject )
		GenMove.dragobject = null;
};

/**
 * @access public
 * @static
 */
GenMove.move = function()
{
	if ( GenMove.dragobject )
	{
		if ( window.event.clientX >= 0 && window.event.clientY >= 0 )
		{
			GenMove.dragobject.style.left = window.event.clientX - GenMove.tx;
			GenMove.dragobject.style.top  = window.event.clientY - GenMove.ty;
		}
		
		window.event.returnValue  = false;
		window.event.cancelBubble = true;
	}
};

/**
 * @access public
 * @static
 */
GenMove.getLeftPos = function( el )
{
	if ( el.currentStyle.left == "auto" )
		return 0;
	else
		return parseInt( el.currentStyle.left );
};

/**
 * @access public
 * @static
 */
GenMove.getTopPos = function( el )
{
	if ( el.currentStyle.top == "auto" )
		return 0;
	else
		return parseInt( el.currentStyle.top );
};

/**
 * @access public
 * @static
 */
GenMove.makeOnTop = function( el )
{
	var daiz;
	
	var max = 0;
	var da  = document.all;
	
	for ( var i = 0; i < da.length; i++ )
	{
		daiz = da[i].style.zIndex;
		
		if ( daiz != "" && daiz > max )
			max = daiz;
	}
	
	el.style.zIndex = max + 1;
};
