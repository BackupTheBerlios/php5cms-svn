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
MenuItem_checkbox = function( sLabelText, bChecked, fAction, oSubMenu )
{
	this.MenuItem = MenuItem;
	this.MenuItem( sLabelText, fAction, null, oSubMenu );

	// public
	this.checked = bChecked;
};


MenuItem_checkbox.prototype = new MenuItem();
MenuItem_checkbox.prototype.constructor = MenuItem_checkbox;
MenuItem_checkbox.superclass = MenuItem.prototype;

/**
 * @access public
 */
MenuItem_checkbox.prototype.getIconHtml = function()
{
	return "<span class=\"check-box\">"   +
		( this.checked? "a" : "&nbsp;" ) +
		"</span>";
};

/**
 * @access public
 */
MenuItem_checkbox.prototype.getIconCellHtml = function()
{
	return "<td class=\"icon-cell\">" + this.makeDisabledContainer( this.getIconHtml() ) + "</td>";
};

/**
 * @access public
 */
MenuItem_checkbox.prototype.getCssClass = function ()
{
	var s = ( this.checked? " checked" : "" );

	if ( this.disabled && this._selected )
		return "disabled-hover" + s;
	else if ( this.disabled )
		return "disabled" + s;
	else if ( this._selected )
		return "hover" + s;
	
	return s;
};


/**
 * @access public
 */
MenuItem_checkbox.prototype._menuItem_dispatchAction = MenuItem.prototype.dispatchAction;

/**
 * @access public
 */
MenuItem_checkbox.prototype.dispatchAction = function()
{
	this.checked = !this.checked;

	this._menuItem_dispatchAction();
	this.parentMenu.invalidate();
	this.parentMenu.closeAllMenus();
};
