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
FavBar = function()
{
	this.Base = Base;
	this.Base();
};


FavBar.prototype = new Base();
FavBar.prototype.constructor = FavBar;
FavBar.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
FavBar.scrollAmount = 20;

/**
 * @access public
 * @static
 */
FavBar.selectedItem = null;


/**
 * @access public
 * @static
 */
FavBar.handleClick = function()
{
	el = Util.getReal( window.event.srcElement, "tagName", "DIV" );
	
	if ( ( el.className == "topFolder" ) || ( el.className == "subFolder" ) )
	{
		el.sub = eval( el.id + "Sub" );
		
		if ( el.sub.style.display == null )
			el.sub.style.display = "none";
		
		// hidden
		if ( el.sub.style.display != "block" )
		{
			//any other sub open?
			if ( el.parentElement.openedSub != null )
			{
				var opener = eval( el.parentElement.openedSub + ".opener" );
				FavBar.hide( el.parentElement.openedSub );
				
				if ( opener.className == "topFolder" )
					FavBar.outTopItem( opener );
			}
			
			el.sub.style.display = "block";
			el.sub.parentElement.openedSub = el.sub.id;
			el.sub.opener = el;
		}
		else
		{
			if ( el.sub.openedSub != null )
				FavBar.hide( el.sub.openedSub );
			else
				FavBar.hide( el.sub.id );
		}
	}
	
	if ( ( el.className == "subItem" ) || ( el.className == "subFolder" ) )
	{
		if ( FavBar.selectedItem != null )
			FavBar.restoreSubItem( FavBar.selectedItem );
			
		FavBar.highlightSubItem( el );
	}
	
	if ( ( el.className == "topItem" ) || ( el.className == "topFolder" ) )
	{
		if ( FavBar.selectedItem != null )
			FavBar.restoreSubItem( FavBar.selectedItem );
	}

	if ( ( el.className == "topItem" ) || ( el.className == "subItem" ) )
	{
		if ( ( el.href != null) && ( el.href != "" ) )
		{
			if ( ( el.target == null ) || ( el.target == "" ) )
			{
				if ( window.opener == null )
				{
					if ( document.all.tags( "BASE" ).item( 0 ) != null )
						window.open( el.href, document.all.tags( "BASE" ).item( 0 ).target );
					else 
						window.location = el.href; // HERE IS THE LOADING!!!
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
		FavBar.fixScroll( tmp );
};

/**
 * @access public
 * @static
 */
FavBar.handleOver = function()
{
	var fromEl = Util.getReal( window.event.fromElement, "tagName", "DIV" );
	var toEl   = Util.getReal( window.event.toElement,   "tagName", "DIV" );
	
	if ( fromEl == toEl )
		return;
	
	el = toEl;
	
	if ( ( el.className == "topFolder" ) || ( el.className == "topItem" ) )
		FavBar.overTopItem( el );
	
	if ( ( el.className == "subFolder" ) || ( el.className == "subItem" ) )
		FavBar.overSubItem( el );
	
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
		FavBar.overscrollButton(el);
};

/**
 * @access public
 * @static
 */
FavBar.handleOut = function()
{
	var fromEl = Util.getReal( window.event.fromElement, "tagName", "DIV" );
	var toEl   = Util.getReal( window.event.toElement,   "tagName", "DIV" );
	
	if ( fromEl == toEl )
		return;
	
	el = fromEl;

	if ( ( el.className == "topFolder" ) || ( el.className == "topItem" ) )
		FavBar.outTopItem( el );
		
	if ( ( el.className == "subFolder" ) || ( el.className == "subItem" ) )
		FavBar.outSubItem( el );
		
	if ( el.className == "scrollButton" )
		FavBar.outscrollButton( el );
};

/**
 * @access public
 * @static
 */
FavBar.handleDown = function()
{
	el = Util.getReal( window.event.srcElement, "tagName", "DIV" );
		
	if ( el.className == "scrollButton" )
	{
		FavBar.downscrollButton( el );
		var mark   = Math.max( el.id.indexOf( "Up" ), el.id.indexOf( "Down" ) );
		var type   = el.id.substr( mark );
		var menuID = el.id.substring( 0, mark );
		eval( "FavBar.scroll" + type + "(" + menuID + ")" );
	}
};

/**
 * @access public
 * @static
 */
FavBar.handleUp = function()
{
	el = Util.getReal( window.event.srcElement, "tagName", "DIV" );
		
	if ( el.className == "scrollButton" )
	{
		FavBar.upscrollButton( el );
		window.clearTimeout( FavBar.scrolltimer );
	}
};

/**
 * @access public
 * @static
 */
FavBar.hide = function( elID )
{
	var el = eval( elID );
	el.style.display = "none";
	el.parentElement.openedSub = null;
	
	if ( el.openedSub != null )
		FavBar.hide( el.openedSub );
};

/**
 * @access public
 * @static
 */
FavBar.writeSubPadding = function( depth )
{
	var str;
	var str2;
	var val;
	var str = "<style type='text/css'>\n";
	
	for ( var i = 0; i < depth; i++ )
	{
		str2 = "";
		val  = 0;
		
		for ( var j = 0; j < i; j++ )
		{
			str2 += ".sub "
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
FavBar.overTopItem = function( el )
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
FavBar.outTopItem = function( el )
{
	// opened
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
FavBar.overSubItem = function( el )
{
	el.style.textDecoration = "underline";
};

/**
 * @access public
 * @static
 */
FavBar.outSubItem = function( el )
{
	el.style.textDecoration = "none";
};

/**
 * @access public
 * @static
 */
FavBar.highlightSubItem = function( el )
{
	el.style.background = "buttonshadow";
	el.style.color      = "white"; // "highlighttext";
	FavBar.selectedItem = el;
};

/**
 * @access public
 * @static
 */
FavBar.restoreSubItem = function( el )
{
	el.style.color      = "menutext";
	FavBar.selectedItem = null;
};

/**
 * @access public
 * @static
 */
FavBar.overscrollButton = function( el )
{
	FavBar.overTopItem( el );
	el.style.padding = "0px";
};

/**
 * @access public
 * @static
 */
FavBar.outscrollButton = function( el )
{
	FavBar.outTopItem( el );
	el.style.padding = "0px";
};

/**
 * @access public
 * @static
 */
FavBar.downscrollButton = function( el )
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
FavBar.upscrollButton = function( el )
{
	FavBar.overTopItem( el );
	el.style.padding = "0px";
};

/**
 * @access public
 * @static
 */
FavBar.scrollDown = function( el )
{
	if ( el.offsetHeight > el.parentElement.offsetHeight )
	{
		var mt = parseInt( el.style.marginTop );
		mt -= FavBar.scrollAmount;
		
		if ( mt >= el.parentElement.offsetHeight - el.offsetHeight - 2 )
		{
			el.style.marginTop = mt;
			FavBar.scrolltimer = window.setTimeout( "FavBar.scrollDown(" + el.id + ")", 100 );
		}
		else
		{
			el.style.marginTop = el.parentElement.offsetHeight - el.offsetHeight - 2;
		}
	}
	
	FavBar.fixScroll( el )
};

/**
 * @access public
 * @static
 */
FavBar.scrollUp = function( el )
{
	var mt = parseInt( el.style.marginTop );
	mt += FavBar.scrollAmount;
	
	if ( mt >= 0 )
	{
		el.style.marginTop = 0;
	}
	else
	{
		el.style.marginTop = mt;
		FavBar.scrolltimer = window.setTimeout( "FavBar.scrollUp(" + el.id + ")", 100 );
	}
	
	FavBar.fixScroll( el );
};

/**
 * @access public
 * @static
 */
FavBar.fixScroll = function( el )
{
	if ( el.style.marginTop == "" )
		el.style.margin = "0px";
	
	mt = parseInt( el.style.marginTop );
	var downButton = eval( el.id + "Down" );
	var upButton   = eval( el.id + "Up"   );

	// Positioning of scroll buttons. You never now when someone changes something!
	upButton.style.left    = FavBar.leftPos( el.parentElement ) + 2;
	upButton.style.top     = FavBar.topPos( el.parentElement ) + 2;
	upButton.style.width   = el.parentElement.offsetWidth - 2;
	downButton.style.left  = FavBar.leftPos( el.parentElement ) + 2;
	downButton.style.top   = FavBar.topPos( el.parentElement ) + el.parentElement.offsetHeight - 16;		
	downButton.style.width = el.parentElement.offsetWidth - 2;

	upButton.style.display   = ( mt < 0 ) ? "block" : "none";
	downButton.style.display = ( ( mt == el.parentElement.offsetHeight - el.offsetHeight - 2 ) || ( el.offsetHeight <= el.parentElement.offsetHeight ) ) ? "none" : "inline";
		 
	if ( el.offsetHeight < el.parentElement.offsetHeight )
	{
		el.style.marginTop = 0;
		upButton.style.display = "none";
	}
};

/**
 * @access public
 * @static
 */
FavBar.topPos = function( el )
{
	return FavBar.doPosLoop( el, "Top" );
};

/**
 * @access public
 * @static
 */
FavBar.leftPos = function( el )
{
	return FavBar.doPosLoop( el, "Left" );
};

/**
 * @access public
 * @static
 */
FavBar.doPosLoop = function( el, val )
{
	var temp = el;
	var x = eval( "temp.offset" + val );
	
	while ( ( temp.tagName != "BODY" ) && ( temp.offsetParent.style.position != "absolute" ) )
	{
		temp = temp.offsetParent;
		x += eval( "temp.offset" + val );
	}
	
	return x;
};
