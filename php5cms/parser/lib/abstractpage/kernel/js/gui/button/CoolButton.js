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
 * @package gui_button
 */
 
/**
 * Constructor
 *
 * @access public
 */
CoolButton = function()
{
	this.Base = Base;
	this.Base();
};


CoolButton.prototype = new Base();
CoolButton.prototype.constructor = CoolButton;
CoolButton.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
CoolButton.over = function()
{
	var toEl   = Util.getReal( window.event.toElement,   "className", "coolButton" );
	var fromEl = Util.getReal( window.event.fromElement, "className", "coolButton" );
	
	if ( toEl == fromEl )
		return;
	
	var el = toEl;
	var cDisabled = el.cDisabled;
	cDisabled = ( cDisabled != null );
	
	if ( el.className == "coolButton" )
		el.onselectstart = new Function( "return false" );
	
	if ( ( el.className == "coolButton" ) && !cDisabled )
	{
		CoolButton.makeRaised( el );
		CoolButton.makeGray( el, false );
	}
};

/**
 * @access public
 * @static
 */
CoolButton.out = function()
{
	var toEl   = Util.getReal( window.event.toElement,   "className", "coolButton" );
	var fromEl = Util.getReal( window.event.fromElement, "className", "coolButton" );
	
	if ( toEl == fromEl )
		return;
	
	var el = fromEl;
	var cDisabled = el.cDisabled;
	cDisabled = ( cDisabled != null );
	var cToggle = el.cToggle;
	toggle_disabled = ( cToggle != null );

	if ( cToggle && el.value )
	{
		CoolButton.makePressed( el );
		CoolButton.makeGray( el, true );
	}
	else if ( ( el.className == "coolButton" ) && !cDisabled )
	{
		CoolButton.makeFlat( el );
		CoolButton.makeGray( el, true );
	}
};

/**
 * @access public
 * @static
 */
CoolButton.down = function()
{
	var el = Util.getReal( window.event.srcElement, "className", "coolButton" );
	var cDisabled = el.cDisabled;
	cDisabled = ( cDisabled != null );
	
	if ( ( el.className == "coolButton" ) && !cDisabled )
		CoolButton.makePressed( el );
};

/**
 * @access public
 * @static
 */
CoolButton.up = function()
{
	var el = Util.getReal( window.event.srcElement, "className", "coolButton" );
	var cDisabled = el.cDisabled;
	cDisabled = ( cDisabled != null );
	
	if ( ( el.className == "coolButton" ) && !cDisabled )
		CoolButton.makeRaised( el );
};

/**
 * @access public
 * @static
 */
CoolButton.findChildren = function( el, type, value )
{
	var children = el.children;
	var tmp = new Array();
	var j = 0;
	
	for ( var i = 0; i < children.length; i++ )
	{
		if ( eval( "children[i]." + type + "==\"" + value + "\"" ) )
			tmp[tmp.length] = children[i];
		
		tmp = tmp.concat( CoolButton.findChildren( children[i], type, value ) );
	}

	return tmp;
};

/**
 * @access public
 * @static
 */
CoolButton.disable = function( el )
{
	if ( document.readyState != "complete" )
	{
		window.setTimeout( "CoolButton.disable( " + el.id + ")", 100 );
		return;
	}
	
	var cDisabled = el.cDisabled;
	cDisabled = ( cDisabled != null );

	if ( !cDisabled )
	{
		el.cDisabled = true;
		
		// ie5
		if ( document.getElementsByTagName )
		{
			el.innerHTML =	"<span style='background: buttonshadow; filter: chroma(color=red) dropshadow(color=buttonhighlight, offx=1, offy=1); width: 100%; height: 100%; text-align: center;'>" +
				"<span style='filter: mask(color=red); width: 100%; height: 100%; text-align: center;'>" +
				el.innerHTML +
				"</span>" +
				"</span>";
		}
		// ie4
		else
		{
			el.innerHTML =	'<span style="background: buttonshadow; width: 100%; height: 100%; text-align: center;">' +
				'<span style="filter:Mask(Color=buttonface) DropShadow(Color=buttonhighlight, OffX=1, OffY=1, Positive=0); height: 100%; width: 100%%; text-align: center;">' +
				el.innerHTML +
				'</span>' +
				'</span>';
		}
		
		if ( el.onclick != null )
		{
			el.cDisabled_onclick = el.onclick;
			el.onclick = null;
		}
	}
};

/**
 * @access public
 * @static
 */
CoolButton.enable = function( el )
{
	var cDisabled = el.cDisabled;
	cDisabled = ( cDisabled != null );
	
	if ( cDisabled )
	{
		el.cDisabled = null;
		el.innerHTML = el.children[0].children[0].innerHTML;

		if ( el.cDisabled_onclick != null )
		{
			el.onclick = el.cDisabled_onclick;
			el.cDisabled_onclick = null;
		}
	}
};

/**
 * @access public
 * @static
 */
CoolButton.addToggle = function( el )
{
	var cDisabled = el.cDisabled;
	cDisabled = ( cDisabled != null );
	
	var cToggle = el.cToggle;
	cToggle = ( cToggle != null );

	if ( !cToggle && !cDisabled )
	{
		el.cToggle = true;
		
		if ( el.value == null )
			el.value = 0; // start as not pressed down
		
		if ( el.onclick != null )
			el.cToggle_onclick = el.onclick;
		else
			el.cToggle_onclick = "";

		el.onclick = new Function( "CoolButton.toggle(" + el.id +"); " + el.id + ".cToggle_onclick();" );
	}
};

/**
 * @access public
 * @static
 */
CoolButton.removeToggle = function( el )
{
	var cDisabled = el.cDisabled;
	cDisabled = ( cDisabled != null );
	
	var cToggle = el.cToggle;
	cToggle = ( cToggle != null );
	
	if ( cToggle && !cDisabled )
	{
		el.cToggle = null;
		
		if ( el.value )
			CoolButton.toggle( el );
		
		CoolButton.makeFlat( el );
		
		if ( el.cToggle_onclick != null )
		{
			el.onclick = el.cToggle_onclick;
			el.cToggle_onclick = null;
		}
	}
};

/**
 * @access public
 * @static
 */
CoolButton.toggle = function( el )
{
	el.value = !el.value;
	
	/*
	if ( el.value )
		el.style.background = "URL(../img/misc/tileback.gif)";
	else
		el.style.backgroundImage = "";
	*/
};

/**
 * @access public
 * @static
 */
CoolButton.makeFlat = function( el )
{
	with ( el.style )
	{
		background = "";
		border     = "1px solid buttonface";
		padding    = "1px";
	}
};

/**
 * @access public
 * @static
 */
CoolButton.makeRaised = function( el )
{
	with ( el.style )
	{
		borderLeft   = "1px solid buttonhighlight";
		borderRight  = "1px solid buttonshadow";
		borderTop    = "1px solid buttonhighlight";
		borderBottom = "1px solid buttonshadow";
		padding      = "1px";
	}
};

/**
 * @access public
 * @static
 */
CoolButton.makePressed = function( el )
{
	with ( el.style )
	{
		borderLeft    = "1px solid buttonshadow";
		borderRight   = "1px solid buttonhighlight";
		borderTop     = "1px solid buttonshadow";
		borderBottom  = "1px solid buttonhighlight";
		paddingTop    = "2px";
		paddingLeft   = "2px";
		paddingBottom = "0px";
		paddingRight  = "0px";
	}
};

/**
 * @access public
 * @static
 */
CoolButton.makeGray = function( el, b )
{
	var filtval;
	
	if ( b )
		filtval = "gray()";
	else
		filtval = "";

	var imgs = CoolButton.findChildren( el, "tagName", "IMG" );
	
	for ( var i = 0; i < imgs.length; i++ )
		imgs[i].style.filter = filtval;
};
