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
SimpleFavBar = function()
{
	this.Base = Base;
	this.Base();
};


SimpleFavBar.prototype = new Base();
SimpleFavBar.prototype.constructor = SimpleFavBar;
SimpleFavBar.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
SimpleFavBar.selectedItem = null;

/**
 * @access public
 * @static
 */
SimpleFavBar.overflowTimeout = 1;


/**
 * @access public
 * @static
 */
SimpleFavBar.handleClick = function()
{
	el = Util.getReal( window.event.srcElement, "tagName", "DIV" );
	
	if ( ( el.className == "topFolder" ) || ( el.className == "subFolder" ) )
	{
		el.sub = eval( el.id + "Sub" );
		
		if ( el.sub.style.display == null )
			el.sub.style.display = "none";
		
		if ( el.sub.style.display != "block" ) // hidden
		{
			//any other sub open?
			if ( el.parentElement.openedSub != null )
			{
				var opener = eval( el.parentElement.openedSub + ".opener" );
				SimpleFavBar.hide( el.parentElement.openedSub );

				if ( opener.className == "topFolder" )
					SimpleFavBar.outTopItem( opener );
			}
			
			el.sub.style.display = "block";
			el.sub.parentElement.openedSub = el.sub.id;
			el.sub.opener = el;
		}
		else
		{
			if ( el.sub.openedSub != null )
				SimpleFavBar.hide( el.sub.openedSub );
			else
				SimpleFavBar.hide( el.sub.id );
		}
	}
	
	if ( ( el.className == "subItem" ) || ( el.className == "subFolder" ) )
	{
		if ( SimpleFavBar.selectedItem != null )
			SimpleFavBar.restoreSubItem( SimpleFavBar.selectedItem );
		
		SimpleFavBar.highlightSubItem( el );
	}
	
	if ( ( el.className == "topItem" ) || ( el.className == "topFolder" ) )
	{
		if ( SimpleFavBar.selectedItem != null )
			SimpleFavBar.restoreSubItem( SimpleFavBar.selectedItem );
	}

	if ( ( el.className == "topItem" ) || ( el.className == "subItem" ) )
	{
		if ( ( el.href != null ) && ( el.href != "" ) )
		{
			if ( ( el.target == null ) || ( el.target == "" ) )
			{
				if ( window.opener == null )
				{
					if ( document.all.tags("BASE").item(0) != null )
						window.open( el.href, document.all.tags("BASE").item(0).target );
					else
						window.location = el.href; // loading
				}
				else
				{
					window.opener.location =  el.href;
				}
			}
			else
			{
				window.open( el.href, el.target );
			}
		}
	}
	
	var tmp  = Util.getReal( el, "className", "favMenu" );
	
	if ( tmp.className == "favMenu" )
		SimpleFavBar.fixScroll( tmp );
};

/**
 * @access public
 * @static
 */
SimpleFavBar.handleOver = function()
{
	var fromEl = Util.getReal( window.event.fromElement, "tagName", "DIV" );
	var toEl   = Util.getReal( window.event.toElement,   "tagName", "DIV" );

	if ( fromEl == toEl )
		return;
	
	el = toEl;
	
	if ( ( el.className == "topFolder" ) || ( el.className == "topItem" ) )
		SimpleFavBar.overTopItem( el );
	
	if ( ( el.className == "subFolder" ) || ( el.className == "subItem" ) )
		SimpleFavBar.overSubItem( el );
	
	if ( ( el.className == "topItem" ) || ( el.className == "subItem" ) )
	{
		if ( el.href != null )
		{
			if ( el.oldtitle == null )
				el.oldtitle = el.title;
			
			if ( el.oldtitle != "" )
				el.title = el.oldtitle + "\n" + el.href;
			else
				el.title = el.oldtitle + el.href;
		}
	}
	
	if ( el.className == "scrollButton" )
		SimpleFavBar.overscrollButton( el );
};

/**
 * @access public
 * @static
 */
SimpleFavBar.handleOut = function()
{
	var fromEl = Util.getReal( window.event.fromElement, "tagName", "DIV" );
	var toEl   = Util.getReal( window.event.toElement,   "tagName", "DIV" );
	
	if ( fromEl == toEl )
		return;
	
	el = fromEl;

	if ( ( el.className == "topFolder" ) || ( el.className == "topItem" ) )
		SimpleFavBar.outTopItem( el );
	
	if ( ( el.className == "subFolder" ) || ( el.className == "subItem" ) )
		SimpleFavBar.outSubItem( el );
	
	if ( el.className == "scrollButton" )
		SimpleFavBar.outscrollButton( el );
};

/**
 * @access public
 * @static
 */
SimpleFavBar.handleDown = function()
{
	el = Util.getReal( window.event.srcElement, "tagName", "DIV" );
		
	if ( el.className == "scrollButton" )
	{
		SimpleFavBar.downscrollButton( el );
		
		var mark   = Math.max( el.id.indexOf( "Up" ), el.id.indexOf( "Down" ) );
		var type   = el.id.substr( mark );
		var menuID = el.id.substring( 0, mark );
		
		eval( "scroll" + type + "(" + menuID + ")" );
	}
};

/**
 * @access public
 * @static
 */
SimpleFavBar.handleUp = function()
{
	el = Util.getReal( window.event.srcElement, "tagName", "DIV" );
		
	if ( el.className == "scrollButton" )
	{
		SimpleFavBar.upscrollButton( el );
		window.clearTimeout( scrolltimer );
	}
};

/**
 * @access public
 * @static
 */
SimpleFavBar.hide = function( elID )
{
	var el = eval( elID );
	el.style.display = "none";
	el.parentElement.openedSub = null;
	
	if ( el.openedSub != null )
		SimpleFavBar.hide( el.openedSub );
};

/**
 * @access public
 * @static
 */
SimpleFavBar.writeSubPadding = function( depth )
{
	var str, str2, val;
	var str = "<style type='text/css'>\n";
	
	for ( var i = 0; i < depth; i++ )
	{
		str2 = "";
		val  = 0;
		
		for ( var j = 0; j < i; j++ )
		{
			str2 += ".sub ";
			val  += 22;
		}
		
		str += str2 + ".subFolder {padding-left: " + val + "px;}\n";
		str += str2 + ".subItem   {padding-left: " + val + "px;}\n";
	}
	
	str += "</style>\n";
	return str;
};

/**
 * @access public
 * @static
 */
SimpleFavBar.overTopItem = function( el )
{
	with ( el.style )
	{
		background    = "buttonface";
		borderLeft    = "1px solid buttonhighlight";
		borderRight   = "1px solid buttonshadow";
		borderTop     = "1px solid buttonhighlight";
		borderBottom  = "1px solid buttonshadow";
		paddingBottom = "2px";
	}
};

/**
 * @access public
 * @static
 */
SimpleFavBar.outTopItem = function( el )
{
	if ( ( el.sub != null ) && ( el.parentElement.openedSub == el.sub.id ) )
	{
		with ( el.style )
		{
			borderTop     = "1px solid buttonshadow";
			borderLeft    = "1px solid buttonshadow";
			borderRight   = "1px solid buttonhighlight";
			borderBottom  = "0px";
			paddingBottom = "3px";
			background    = "url(../img/misc/tileback.gif) buttonface";
		}
	}
	else
	{
		with ( el.style )
		{
			border     = "1px solid buttonface";
			background = "buttonface";
			padding    = "2px";
		}
	}
};

/**
 * @access public
 * @static
 */
SimpleFavBar.overSubItem = function( el )
{
	el.style.textDecoration = "underline";
};

/**
 * @access public
 * @static
 */
SimpleFavBar.outSubItem = function( el )
{
	el.style.textDecoration = "none";
};

/**
 * @access public
 * @static
 */
SimpleFavBar.highlightSubItem = function( el )
{
	el.style.background = "buttonshadow";
	el.style.color = "white";
	SimpleFavBar.selectedItem = el;
};

/**
 * @access public
 * @static
 */
SimpleFavBar.restoreSubItem = function( el )
{
	el.style.background = "url(../img/misc/tileback.gif) buttonface";
	el.style.color = "menutext";
	SimpleFavBar.selectedItem = null;
};

/**
 * @access public
 * @static
 */
SimpleFavBar.overscrollButton = function( el )
{
	SimpleFavBar.overTopItem( el );
	el.style.padding = "0px";
};

/**
 * @access public
 * @static
 */
SimpleFavBar.outscrollButton = function( el )
{
	SimpleFavBar.outTopItem( el );
	el.style.padding = "0px";
};

/**
 * @access public
 * @static
 */
SimpleFavBar.downscrollButton = function( el )
{
	with ( el.style )
	{
		borderRight  = "1px solid buttonhighlight";
		borderLeft   = "1px solid buttonshadow";
		borderBottom = "1px solid buttonhighlight";
		borderTop    = "1px solid buttonshadow";
	}
};

/**
 * @access public
 * @static
 */
SimpleFavBar.upscrollButton = function( el )
{
	SimpleFavBar.overTopItem( el );
	el.style.padding = "0px";
};

/**
 * @access public
 * @static
 */
SimpleFavBar.fixScroll = function( el )
{
	SimpleFavBar.globalScrollContainer = el;
	window.setTimeout( 'SimpleFavBar.changeOverflow(SimpleFavBar.globalScrollContainer)', SimpleFavBar.overflowTimeout );
};

/**
 * @access public
 * @static
 */
SimpleFavBar.changeOverflow = function( el )
{
	if ( el.offsetHeight > el.parentElement.clientHeight )
		window.setTimeout( 'SimpleFavBar.globalScrollContainer.parentElement.style.overflow = "auto";', SimpleFavBar.overflowTimeout );
	else
		window.setTimeout( 'SimpleFavBar.globalScrollContainer.parentElement.style.overflow = "hidden";', SimpleFavBar.overflowTimeout );
};
