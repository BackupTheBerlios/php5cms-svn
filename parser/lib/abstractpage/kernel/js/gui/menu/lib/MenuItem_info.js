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
 * @package gui_menu_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
MenuItem_info = function( sLabelText, sIconSrc )
{
	this.Base = Base;
	this.Base();
	
	this.icon     = sIconSrc || "";
	this.text     = sLabelText;
	this.disabled = false;
	this.visible  = true;
	
	this.parentMenu = null;
	
	// private
	this._selected  = false;
	this._useInsets = true;	// should insets be taken into account when showing sub menu
};


MenuItem_info.prototype = new Base();
MenuItem_info.prototype.constructor = MenuItem_info;
MenuItem_info.superclass = Base.prototype;

/**
 * @access public
 */
MenuItem_info.prototype.toHtml = function()
{
	return	"<tr class=\"info\"" +
			( !this.visible? " style=\"display: none\"" : "" ) +
			">" +
			this.getIconCellHtml() +
			this.getTextCellHtml() +
			"</tr>";
};

/**
 * @access public
 */
MenuItem_info.prototype.getTextHtml = function()
{
	return this.text;
};

/**
 * @access public
 */
MenuItem_info.prototype.getIconHtml = function()
{
	return ( this.icon != "")? "<img src=\"" + this.icon + "\" />" : "<span>&nbsp;</span>";
};

/**
 * @access public
 */
MenuItem_info.prototype.getTextCellHtml = function()
{
	return "<td class=\"info-cell\" nowrap=\"nowrap\">" + this.makeDisabledContainer( this.getTextHtml() ) + "</td>";
};

/**
 * @access public
 */
MenuItem_info.prototype.getIconCellHtml = function()
{
	return "<td valign=\"top\" class=\"" + ( ( this.icon != "" )? "icon-cell" : "empty-icon-cell" ) + "\">" +
		this.makeDisabledContainer( this.getIconHtml() ) + "</td>";
};

/**
 * @access public
 */
MenuItem_info.prototype.makeDisabledContainer = function( s )
{
	if ( this.disabled )
		return	"<span class=\"disabled-container\"><span class=\"disabled-container\">" + s + "</span></span>";
	
	return s;
};

/**
 * @access public
 */
MenuItem_info.prototype.dispatchAction = function()
{
	if ( this.disabled )
		return;
	
	this.setSelected( true );
};

/**
 * @access public
 */
MenuItem_info.prototype.setSelected = function( bSelected )
{
	if ( this._selected == bSelected )
		return;
	
	this._selected = Boolean( bSelected );

	var tr = this._htmlElement;
	var pm = this.parentMenu;

	if ( bSelected )
	{	
		pm.setSelectedIndex( this.itemIndex );
		this.scrollIntoView();
		
		// select item in parent menu as well
		if ( pm.parentMenuItem )
			pm.parentMenuItem.setSelected( true );
	}
	else
	{
		pm.setSelectedIndex( -1 );
	}
	
	if ( this._selected )
	{
		// clear timers for parent menu
		window.clearTimeout( pm._closeTimer );
	}
};

/**
 * @access public
 */
MenuItem_info.prototype.getSelected = function()
{
	return this.itemIndex == this.parentMenu.selectedIndex;
};

/**
 * @access public
 */
MenuItem_info.prototype.scrollIntoView = function()
{
	if ( this.parentMenu._scrollingMode )
	{
		var d  = this.parentMenu.getDocument();
		var sc = d.getElementById( "scroll-container" );
		
		var scrollTop    = sc.scrollTop;
		var clientHeight = sc.clientHeight;
		var offsetTop    = this._htmlElement.offsetTop;
		var offsetHeight = this._htmlElement.offsetHeight;
		
		if ( offsetTop < scrollTop )
			sc.scrollTop = offsetTop;
		else if ( offsetTop + offsetHeight > scrollTop + clientHeight )
			sc.scrollTop = offsetTop + offsetHeight - clientHeight;
	}
};

/**
 * @access public
 */
MenuItem_info.prototype.showSubMenu = function()
{
	return true;
};
