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
OutlookBar = function()
{
	this.Base = Base;
	this.Base();
};


OutlookBar.prototype = new Base();
OutlookBar.prototype.constructor = OutlookBar;
OutlookBar.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
OutlookBar.init = function(oeBarContainer)
{
	document.body.onselectstart = new Function( "return false" );
	OutlookBar.checkScroll( oeBarContainer );
	OutlookBar.selectRightItem();
};

/**
 * @access public
 * @static
 */
OutlookBar.selectRightItem = function()
{
	if ( !parent.trLoaded || ( parent.treeframe.document.readyState != "complete" ) )
	{
		window.setTimeout( "OutlookBar.selectRightItem()", 100 );
	}
	else
	{
		var el;
		var gr   = parent.treeframe.document.body.group;
		var divs = document.all.tags( "DIV" );
		
		for ( var i = 0; i < divs.length; i++ )
		{
			if ( divs[i].getAttribute( "onclick" ) != null	&& divs[i].getAttribute( "onclick" ).toString().indexOf( gr ) != -1 )
			{
				document.body.selectedItem = divs[i];
				OutlookBar.select( divs[i] );
				
				break;
			}
		}
	}
};

/**
 * @access public
 * @static
 */
OutlookBar.handleClick = function()
{
	el = Util.getReal( window.event.srcElement, "tagName", "DIV" );
		
	if ( el.className == "oeBarButton" )
	{
		if ( document.body.selectedItem == null )
		{
			document.body.selectedItem = el;
			OutlookBar.select( el );
		}
		else if ( document.body.selectedItem != el )
		{
			OutlookBar.unselect( document.body.selectedItem );
			document.body.selectedItem = el;
			OutlookBar.select( el );
		}
	}
};

/**
 * @access public
 * @static
 */
OutlookBar.handleOver = function()
{
	var fromEl = Util.getReal( window.event.fromElement, "tagName", "DIV" );
	var toEl   = Util.getReal( window.event.toElement,   "tagName", "DIV" );
	
	if ( fromEl == toEl )
		return;
	
	el = toEl;
	
	if ( ( el.className == "scrollButton" ) || ( el.className == "oeBarButton" ) )
		OutlookBar.raise( el );
};

/**
 * @access public
 * @static
 */
OutlookBar.handleOut = function()
{
	var fromEl = Util.getReal( window.event.fromElement, "tagName", "DIV" );
	var toEl   = Util.getReal( window.event.toElement,   "tagName", "DIV" );
	
	if ( fromEl == toEl )
		return;
	
	el = fromEl;

	if ( ( el.className == "scrollButton" ) || ( el.className == "oeBarButton" ) )
	{
		OutlookBar.flat( el );
		window.clearTimeout( scrollTimer );
	}
};

/**
 * @access public
 * @static
 */
OutlookBar.handleDown = function()
{
	el = Util.getReal( window.event.srcElement, "tagName", "DIV" );
		
	if ( el.className == "scrollButton" )
	{
		var type = el.getAttribute( "type" );
		var container = el.getAttribute( "for" );
		
		OutlookBar.lower( el );
		OutlookBar.scrollContainer( document.all(container), type );
	}
	
	if ( el.className == "oeBarButton" )
		OutlookBar.lower( el );
};

/**
 * @access public
 * @static
 */
OutlookBar.handleUp = function()
{
	el = Util.getReal( window.event.srcElement, "tagName", "DIV" );
		
	if ( el.className == "scrollButton" )
	{
		OutlookBar.raise( el );
		window.clearTimeout( scrollTimer );
	}
	
	if (el.className == "oeBarButton")
		OutlookBar.raise( el );
};

/**
 * @access public
 * @static
 */
OutlookBar.raise = function( el )
{
	with ( el.style )
	{
		borderWidth	 = "1px";
		borderStyle	 = "outset";
		borderColor	 = "buttonhighlight";
		borderTop    = "1px solid threedlightshadow";
		borderLeft   = "1px solid threedlightshadow";
		borderRight	 = "1px solid threeddarkshadow";
		borderBottom = "1px solid threeddarkshadow";
		padding	     = paddingOrg;
	}
};

/**
 * @access public
 * @static
 */
OutlookBar.lower = function( el )
{
	with ( el.style )
	{
		borderWidth	  = "1px";
		borderStyle	  = "inset";
		borderTop     = "1px solid threeddarkshadow";
		borderLeft    = "1px solid threeddarkshadow";
		borderRight   = "1px solid threedlightshadow";
		borderBottom  = "1px solid threedlightshadow";
		paddingLeft	  = paddingOrg + 1;
		paddingTop    = paddingOrg + 1;
		paddingRight  = paddingOrg - 1;
		paddingBottom = paddingOrg - 1;
	}
};

/**
 * @access public
 * @static
 */
OutlookBar.flat = function( el )
{
	with ( el.style )
	{
		border  = "1px solid buttonshadow";
		padding = paddingOrg;
	}
};

/**
 * @access public
 * @static
 */
OutlookBar.select = function( el )
{
	el.style.background = "highlight";
	el.style.color = "highlighttext";
};

/**
 * @access public
 * @static
 */
OutlookBar.unselect = function( el )
{
	el.style.background = "buttonshadow";
	el.style.color = "window";
};

/**
 * @access public
 * @static
 */
OutlookBar.checkScroll = function( el )
{
	var ub     = document.all( el.getAttribute( "upbutton" ) );
	var db     = document.all( el.getAttribute( "downbutton" ) );
	var p      = el.parentElement;
	var top    = el.style.pixelTop;
	var bottom = el.offsetHeight + top - p.clientHeight;
	
	if ( el.offsetHeight > p.clientHeight )
	{
		if ( ( top < 0 ) && ( bottom < 0 ) )
			OutlookBar.setBottom( el, 0 );

		if ( ub != null )
		{
			if ( top < 0 )
				ub.style.display = "block";
			else
				ub.style.display = "none";
		}
		
		if ( db != null )
		{
			if ( bottom > 0 )
				db.style.display = "block";
			else
				db.style.display = "none";
		}
	}
	else
	{
		OutlookBar.setTop( el, 0 );
		
		if ( db != null )
			db.style.display = "none";
		
		if ( ub != null )
			ub.style.display = "none";
	}
	
	db.style.top = el.parentElement.clientHeight - db.offsetHeight;
};

/**
 * @access public
 * @static
 */
OutlookBar.setTop = function( el, y )
{
	if ( y <= 0 )
	{
		el.style.pixelTop = y;
		el.style.clip = "rect(" + -y + "," +  el.parentElement.clientWidth + "," + (el.parentElement.clientHeight + -y) + "," + 0 + ")";
	}
};

/**
 * @access public
 * @static
 */
OutlookBar.setBottom = function( el, y )
{
	if ( y >= 0 )
	{
		var ph  = el.parentElement.clientHeight;
		var top = el.offsetHeight - ph - y;
		
		el.style.pixelTop = -top;
		el.style.clip = "rect(" + top + "," + el.parentElement.clientWidth + "," + ( el.offsetHeight ) + "," + 0 + ")";
	}
};

/**
 * @access public
 * @static
 */
OutlookBar.scrollContainer = function( el, type )
{
	var p      = el.parentElement;
	var oldTop = el.style.pixelTop;
	var top    = ( type == "down" )? oldTop - scrollAmount : oldTop + scrollAmount;
	var bottom = el.offsetHeight + top - p.clientHeight;
	
	if ( ( type == "down" ) && ( bottom < 0 ) )
		OutlookBar.setBottom( el, 0 );
	
	if ( ( type=="up" ) && ( top > 0 ) )
		OutlookBar.setTop( el, 0 );
	else if ( ( top < scrollAmount && type == "up" ) || ( type == "down" && bottom > -scrollAmount ) )
		OutlookBar.setTop( el, top );
	
	OutlookBar.checkScroll( el );
	window.clearTimeout( scrollTimer );
	scrollTimer = window.setTimeout( "OutlookBar.scrollContainer(document.all('" + el.id + "'),'" + type + "')", scrollInterval );
};
