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
 * @package gui_menu_context
 */
 
/**
 * Constructor
 *
 * @access public
 */
ContextMenu = function()
{
	this.Base = Base;
	this.Base();
};


ContextMenu.prototype = new Base();
ContextMenu.prototype.constructor = ContextMenu;
ContextMenu.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
ContextMenu.css_path = "";

/**
 * @access public
 * @static
 */
ContextMenu.setCSSPath = function( path )
{
	if ( path && typeof( path ) == "string" )
		ContextMenu.css_path = path;
};

/**
 * @access public
 * @static
 */
ContextMenu.showPopup = function( x, y )
{
	PopUpcss.style.display = "block";
};

/**
 * @access public
 * @static
 */
ContextMenu.intializeContextMenu = function( set )
{
	if ( set != null )
		document.body.innerHTML += '<iframe scrolling="no" class="cMenu" marginwidth="0" marginheight="0" frameborder="0" style="position:absolute;display:none;z-index:50000000;" id="PopUp"></iframe>';
	
	PopUp    = self.frames["PopUp"];
	PopUpcss = document.getElementById( "PopUp" );
	
	document.body.attachEvent( "onmousedown", function()
	{
		PopUpcss.style.display = "none";
	} );
	
	PopUpcss.onfocus = function()
	{
		PopUpcss.style.display = "inline";
	};
	PopUpcss.onblur = function()
	{
		PopUpcss.style.display = "none";
	};
	
	self.attachEvent( "onblur", function() { PopUpcss.style.display = "none"; } );
};

/**
 * @access public
 * @static
 */
ContextMenu.display = function( popupoptions, css_path )
{
    var eobj, x, y;
	
	eobj = window.event;
	x    = eobj.x;
	y    = eobj.y
	
	/*
	not really sure why I had to pass window here
	it appears that an iframe inside a frames page
	will think that its parent is the frameset as
	opposed to the page it was created in...
	*/
	ContextMenu.populatePopup( popupoptions, window, css_path );
	
	ContextMenu.showPopup( x, y );
	ContextMenu.fixSize();
	ContextMenu.fixPos( x, y );
	
    eobj.cancelBubble = true;
    eobj.returnValue  = false;
};

/**
 * @access public
 * @static
 */
ContextMenu.getScrollTop = function()
{
	return document.body.scrollTop;
};

/**
 * @access public
 * @static
 */
ContextMenu.getScrollLeft = function()
{
	return document.body.scrollLeft;
};

/**
 * @access public
 * @static
 */
ContextMenu.fixPos = function( x, y )
{
	var docheight,docwidth,dh,dw;	
	
	docheight = document.body.clientHeight;
	docwidth  = document.body.clientWidth;
	
	dh = ( PopUpcss.offsetHeight + y ) - docheight;
	dw = ( PopUpcss.offsetWidth  + x ) - docwidth;
	
	if ( dw > 0 )
		PopUpcss.style.left = ( x - dw ) + ContextMenu.getScrollLeft() + "px";		
	else
		PopUpcss.style.left = x + ContextMenu.getScrollLeft();

	if ( dh > 0 )
		PopUpcss.style.top = ( y - dh ) + ContextMenu.getScrollTop() + "px"
	else
		PopUpcss.style.top  = y + ContextMenu.getScrollTop();
};

/**
 * @access public
 * @static
 */
ContextMenu.fixSize = function()
{
	var body,h,w;
	
	PopUpcss.style.width  = "10px";
	PopUpcss.style.height = "100000px";
	body = PopUp.document.body;

	var dummy = PopUpcss.offsetHeight + " dummy";
	
	h = body.scrollHeight + PopUpcss.offsetHeight - body.clientHeight;
	w = body.scrollWidth  + PopUpcss.offsetWidth  - body.clientWidth;
	
	PopUpcss.style.height = h + "px";
	PopUpcss.style.width  = w + "px";
};

/**
 * @access public
 * @static
 */
ContextMenu.populatePopup = function( arr, win, css_path )
{
	var alen,i,tmpobj,doc,height,htmstr;
	
	alen = arr.length;
	doc  = PopUp.document;
	doc.body.innerHTML = "";
	
	if ( doc.getElementsByTagName( "LINK" ).length == 0 )
	{
		doc.open();
		doc.write( '<html><head><link rel="styleSheet" type="text/css" href="' + ( css_path || ContextMenu.css_path || '' ) + '"></head><body></body></html>' );
		doc.close();
	}
	
	for ( i = 0; i < alen; i++ )
	{
		if ( arr[i].constructor == ContextMenuItem )
		{
			tmpobj = doc.createElement( "DIV" );
			tmpobj.noWrap = true;
			tmpobj.className = "cMenu-Item";

			if ( arr[i].isdisabled )
			{
				htmstr  = '<span class="cMenu-DisabledContainer"><span class="cMenu-DisabledContainer">';
				htmstr += arr[i].content + '</span></span>';
				
				tmpobj.innerHTML = htmstr;
				tmpobj.className = "cMenu-Disabled";
				
				tmpobj.onmouseover = function()
				{
					this.className = "cMenu-Disabled-Over";
				};
				tmpobj.onmouseout  = function()
				{
					this.className = "cMenu-Disabled";
				};
			}
			else
			{
				tmpobj.innerHTML = arr[i].content;
				tmpobj.onmousedown = (
					function ( f )
					{
						return function()
						{
							win.PopUpcss.style.display = 'none';
							
							if ( typeof( f ) == "function" )
								f();
						};
					}
				)( arr[i].fn );
					
				tmpobj.onmouseover = function()
				{
					this.className = "cMenu-Over";
				};
				tmpobj.onmouseout  = function()
				{
					this.className = "cMenu-Item";
				};
			}
			
			doc.body.appendChild( tmpobj );
		}
		else
		{
			doc.body.appendChild( doc.createElement( "DIV" ) ).className = "cMenu-Separator";
		}
	}
	
	doc.body.className = "cMenu-Body";
	
	doc.body.onselectstart = function()
	{
		return false;
	}
};
