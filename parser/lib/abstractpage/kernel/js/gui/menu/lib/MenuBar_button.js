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
MenuBar_button = function( sLabelText, oSubMenu )
{
	this.MenuItem = MenuItem;
	this.MenuItem( sLabelText, null, null, oSubMenu );
	
	this.subMenuDirection = "vertical";
	
	// private
	this._hover = false;
	this._useInsets = false;	// should insets be taken into account when showing sub menu
	
	var oThis = this;
	this.subMenu._onclose = function ()
	{
		oThis.subMenuClosed();
	};
}


MenuBar_button.prototype = new MenuItem();
MenuBar_button.prototype.constructor = MenuBar_button;
MenuBar_button.superclass = MenuItem.prototype;

/**
 * @access public
 */
MenuBar_button.prototype.scrollIntoView = function()
{
};

/**
 * @access public
 */
MenuBar_button.prototype.toHtml = function()
{
	var cssClass = this.getCssClass();
	var toolTip  = this.getToolTip();
	
	return	"<span unselectable=\"on\" " +
			( cssClass != "" ? " class=\"" + cssClass + "\"" : "" ) +
			( toolTip  != "" ? " title=\"" + toolTip  + "\"" : "" ) +
			"><span unselectable=\"on\" class=\"left\"></span>" +
			"<span unselectable=\"on\" class=\"middle\">" + this.getTextHtml() + "</span>" +
			"<span unselectable=\"on\" class=\"right\"></span>" +
			"</span>";
};

/**
 * @access public
 */
MenuBar_button.prototype.getCssClass = function()
{
	if ( this.disabled && this._selected )
	{
		return "menu-button disabled-hover";
	}
	else if ( this.disabled )
	{
		return "menu-button disabled";
	}
	else if ( this._selected )
	{
		if ( this.parentMenu.getActiveState() == "open" )
			return "menu-button active";
		else
			return "menu-button hover";
	}
	else if ( this._hover )
	{
		return "menu-button hover";
	}
	
	return "menu-button ";
};

/**
 * @access public
 */
MenuBar_button.prototype.subMenuClosed = function()
{	
	if ( this.subMenu._closeReason == "escape" )
		this.setSelected( true );
	else
		this.setSelected( false );
		
	if ( this.parentMenu.getActiveState() == "inactive" )
		this.parentMenu.restoreFocused();
};

/**
 * @access public
 */
MenuBar_button.prototype.setSelected = function( bSelected )
{
	var oldSelected = this._selected;
	this._selected  = Boolean( bSelected );

	var tr = this._htmlElement;
	
	if ( tr )
		tr.className = this.getCssClass();

	if ( this._selected == oldSelected )
		return;
	
	if ( !this._selected )
		this.closeSubMenu( true );

	if ( bSelected )
	{
		this.parentMenu.setSelectedIndex( this.itemIndex );
		this.scrollIntoView();
	}
	else
	{
		this.parentMenu.setSelectedIndex( -1 );
	}
};
