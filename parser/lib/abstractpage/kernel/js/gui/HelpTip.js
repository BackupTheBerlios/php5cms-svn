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
 * @package gui
 */
 
/**
 * Constructor
 *
 * @access public
 */
HelpTip = function()
{
	this.Base = Base;
	this.Base();
}


HelpTip.prototype = new Base();
HelpTip.prototype.constructor = HelpTip;
HelpTip.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
HelpTip.showHelpTip = function( e, s )
{
	// find anchor element
	var el = e.target? e.target : e.srcElement;
	
	while (el.tagName != "A")
		el = el.parentNode;
	
	// Is there already a tooltip? If so, remove it.
	if ( el._helpTip )
	{
		document.body.removeChild( el._helpTip );
		el._helpTip = null;
		el.onblur   = null;
		
		return;
	}

	// create element and insert last into the body
	var d = document.createElement( "DIV" );
	d.className = "help-tooltip";
	document.body.appendChild( d );
	d.innerHTML = s;
	
	// Allow clicks on A elements inside tooltip
	d.onmousedown = function( e )
	{
		if ( !e ) 
			e = event;
		
		var t = e.target? e.target : e.srcElement;
		
		while ( t.tagName != "A" && t != d )
			t = t.parentNode;
			
		if ( t == d )
			return;
		
		el._onblur = el.onblur;
		el.onblur  = null;
	};
	d.onmouseup = function()
	{
		el.onblur = el._onblur;
		el.focus();
	};
	
	// position tooltip
	var dw = document.width? document.width : document.documentElement.offsetWidth - 25;
	
	if ( d.offsetWidth >= dw )
		d.style.width = dw - 10 + "px";
	else
		d.style.width = "";
	
	var scroll = HelpTip.getScroll();
	
	if ( e.clientX > dw - d.offsetWidth )
		d.style.left = dw - d.offsetWidth + scroll.x + "px";
	else
		d.style.left = e.clientX - 2 + scroll.x + "px";
	
	d.style.top = e.clientY + 18 + scroll.y + "px";

	// add a listener to the blur event. When blurred remove tooltip and restore anchor
	el.onblur = function()
	{
		document.body.removeChild( d );
		el.onblur   = null;
		el._helpTip = null;
	};
	
	// store a reference to the tooltip div
	el._helpTip = d;
};
/**
 * Returns the scroll left and top for the browser viewport.
 *
 * @access public
 * @static
 */
HelpTip.getScroll = function()
{
	var result = new Object();
	
	// IE model
	if ( document.all && document.body.scrollTop != undefined )
	{
		var ieBox = document.compatMode != "CSS1Compat";
		var cont  = ieBox? document.body : document.documentElement;
		
		result.x = cont.scrollLeft;
		result.y = cont.scrollTop;
	}
	else
	{
		result.x = window.pageXOffset;
		result.y = window.pageYOffset;
	}
	
	return result;
};
