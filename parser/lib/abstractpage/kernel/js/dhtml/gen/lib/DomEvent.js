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
 * @package dhtml_gen_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
DomEvent = function( type, source, target )
{
	this.Base = Base;
	this.Base();
	
	this.type   = type;
	this.source = source;
	this.target = target;
	this.bubble = false;
	this.cancelBubble = true;
};


DomEvent.prototype = new Base();
DomEvent.prototype.constructor = DomEvent;
DomEvent.superclass = Base.prototype;

mouse = new DomEvent();
mouse.isondrag = false;

mouse.handler = function( e )
{
	if ( !e )
		var e = event;
	
	var type = e.type;
	mouse.type = type;
	mouse.cancelBubble = false;
	mouse.button = DomDoc.browser.ie? ( ( e.button == 2 )? 3 : ( ( e.button == 4 )? 2 : e.button ) ) : e.which;
	
	if ( !mouse.el )
	{
		mouse.dragout = null;
		var el = mouse.source = GenLib.containerOf( DomDoc.browser.ie? e.srcElement : e.target ) || GenLib.document;
		
		if ( !el.eventListeners && el != GenLib.document )
			return true;
		
		var elorg = GenLib.containerOf( ( DomDoc.browser.ie? ( ( mouse.type == 'mouseout' )? e.toElement : ( ( mouse.type == 'mouseover' )? e.fromElement : null ) ) : e.relatedTarget ) );
		
		if ( elorg && elorg.isChildOf( el ) && mouse.bubble || el == elorg )
			return true;
		
		if ( elorg && elorg.isChildOf( el.parent ) || elorg == el.parent )
			mouse.cancelBubble = true;
	}
	else
	{
		var el = mouse.el;
		
		if ( mouse.type == 'mousemove' && mouse.button != 0 )
		{ 
			var x = e.screenX - mouse.xOffset;
			var y = e.screenY - mouse.yOffset;
			
			if ( mouse.el.limit )
			{
				if ( x < mouse.el.limit[3] )
					x = mouse.el.limit[3];
				else if ( x + mouse.el.w > mouse.el.limit[1] )
					x = mouse.el.limit[1] - mouse.el.w;
					
				if ( y < mouse.el.limit[0] )
					y = mouse.el.limit[0];
				else if ( y + mouse.el.h > mouse.el.limit[2] )
					y = mouse.el.limit[2] - mouse.el.h;
			}
			
			setTimeout( mouse.el.toString() + '.moveTo(' + x + ',' + y + ')', 20 );
			
			if ( mouse.isondrag )
			{
				mouse.type = 'drag';
			}
			else
			{
				mouse.isondrag = true;
				mouse.type = 'dragstart';
			}
		}
		else if ( mouse.type == 'mouseup' || mouse.type == 'click' && mouse.isondrag || mouse.type == 'mousemove' && mouse.button == 0 )
		{ 
			var x  = DomDoc.browser.ie? e.x + GenLib.document.elm.scrollLeft : e.pageX - window.pageXOffset;
			var y  = DomDoc.browser.ie? e.y + GenLib.document.elm.scrollTop  : e.pageY - window.pageYOffset;
			
			var px = mouse.el.getPageX();
			var py = mouse.el.getPageY();
			
			if ( x < px || x > px + mouse.el.w || y < py || y > py + mouse.el.h )
				mouse.dragout = true;
			else
				mouse.dragout = false;
				
			mouse.el = mouse.xOffset = mouse.yOffset = null;
			mouse.type = 'dragend';
			mouse.isondrag = false; 
		}
		else
		{
			return false;
		}
		
		mouse.cancelBubble = true;
	}
	
	mouse.pageX = DomDoc.browser.ie? e.x + GenLib.document.elm.scrollLeft : e.pageX - window.pageXOffset;
	mouse.pageY = DomDoc.browser.ie? e.y + GenLib.document.elm.scrollTop  : e.pageY - window.pageYOffset;
	
	mouse.x = DomDoc.browser.ie? e.x - ( el.getPageX() - ( GenLib.document.elm.offsetLeft - GenLib.document.elm.clientLeft ) ) : e.layerX;
	mouse.y = DomDoc.browser.ie? e.y - ( el.getPageY() - ( GenLib.document.elm.offsetTop  - GenLib.document.elm.clientTop  ) ) : e.layerY;
	
	if ( mouse.control )
		mouse.control( mouse.type, mouse );
	
	if ( mouse.type == 'mousedown' && el.drag )
	{
		mouse.el = el;
		
		mouse.xOffset = e.screenX - parseInt( el.x );
		mouse.yOffset = e.screenY - parseInt( el.y );
	}

	if ( el.eventListeners )
		mouse.target = el.eventListeners.target;
	else
		mouse.target = null;
	
	el.invokeEvent( mouse.type, mouse );
	e.cancelBubble = true;
	
	return false;
};
