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
ToolTip = function()
{
	this.Base = Base;
	this.Base();
}


ToolTip.prototype = new Base();
ToolTip.prototype.constructor = ToolTip;
ToolTip.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
ToolTip.tooltipDefaultStyle = "background: infobackground; color: infotext; font: statusbar; padding: 1; border: 1 solid black; position: absolute; z-index: 99; visibility: hidden;";

/**
 * @access public
 * @static
 */
ToolTip.tooltipStart = "<table id=\"internalTooltipSpan\" cellspacing=0 cellpadding=0 style=\"" + ToolTip.tooltipDefaultStyle + "\"><tr><td>&nbsp;";

/**
 * @access public
 * @static
 */
ToolTip.tooltipEnd = "&nbsp;</td></tr></table>";

/**
 * @access public
 * @static
 */
ToolTip.delayTime = 700;

/**
 * @access public
 * @static
 */
ToolTip.showTime = 5000;

/**
 * @access public
 * @static
 */
ToolTip.shown = false;

/**
 * @access public
 * @static
 */
ToolTip.handleMouseMove = function()
{
	x = window.event.x;
	y = window.event.y;
};

/**
 * @access public
 * @static
 */
ToolTip.handleMouseOver = function()
{
	fromEl = Util.getReal( event.fromElement );
	toEl   = Util.getReal( event.toElement );

	if ( ( toEl.getAttribute( "tooltip" ) ) && ( toEl != fromEl ) )
		ToolTip.showTimeout = window.setTimeout( "ToolTip.displayTooltip(toEl)", ToolTip.delayTime );
};

/**
 * @access public
 * @static
 */
ToolTip.handleMouseOut = function()
{
	fromEl = Util.getReal( event.fromElement );
	toEl   = Util.getReal( event.toElement );
	
	if ( ( fromEl.getAttribute( "tooltip" ) ) && ( toEl != fromEl ) )
	{
		window.clearTimeout( ToolTip.showTimeout );
		ToolTip.hideTooltip();
	}
};

/**
 * @access public
 * @static
 */
ToolTip.displayTooltip = function( el )
{
	if ( !document.all.internalTooltipSpan )
		document.body.insertAdjacentHTML( "BeforeEnd", ToolTip.tooltipStart + el.getAttribute( "tooltip" ) + ToolTip.tooltipEnd );
	else
		internalTooltipSpan.outerHTML = ToolTip.tooltipStart + el.getAttribute( "tooltip" ) + ToolTip.tooltipEnd;
	
	var toolStyle = el.getAttribute( "tooltipstyle" );
	
	if ( toolStyle != null )
		internalTooltipSpan.style.cssText = ToolTip.tooltipDefaultStyle + toolStyle;
	
	internalTooltipSpan.style.left = x - 3;
	internalTooltipSpan.style.top  = y + 20;
	dir = ToolTip.getDirection();

	if ( typeof( SwipeObject.swipe ) == "function" )
		window.setTimeout( "SwipeObject.swipe(internalTooltipSpan, dir);", 1 );
	else
		internalTooltipSpan.style.visibility = "visible";

	ToolTip.shown = true;
	ToolTip.hideTimeout = window.setTimeout( "ToolTip.hideTooltip()", ToolTip.showTime );
};

/**
 * @access public
 * @static
 */
ToolTip.hideTooltip = function()
{
	if ( ToolTip.shown )
	{
		window.clearTimeout( ToolTip.hideTimeout );
		internalTooltipSpan.style.visibility = "hidden";
		ToolTip.shown = false;
	}
};

/**
 * @access public
 * @static
 */
ToolTip.getDirection = function()
{
	var pageHeight    = document.body.clientHeight;
	var pageWidth     = document.body.clientWidth;
	var toolTipTop    = internalTooltipSpan.style.pixelTop;
	var toolTipLeft   = internalTooltipSpan.style.pixelLeft;
	var toolTipHeight = internalTooltipSpan.offsetHeight;
	var toolTipWidth  = internalTooltipSpan.offsetWidth;
	var scrollTop     = document.body.scrollTop;
	var scrollLeft    = document.body.scrollLeft;

	if ( toolTipWidth > pageWidth )
		internalTooltipSpan.style.left = scrollLeft;
	else if ( toolTipLeft + toolTipWidth - scrollLeft > pageWidth )
		internalTooltipSpan.style.left = pageWidth - toolTipWidth + scrollLeft;
			
	if ( toolTipTop + toolTipHeight - scrollTop > pageHeight )
	{
		internalTooltipSpan.style.top = toolTipTop - toolTipHeight - 22;
		return 8;
	}
	
	return 2;
};
