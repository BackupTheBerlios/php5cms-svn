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
MenuItem_radiobutton = function( sLabelText, bChecked, sRadioGroupName, fAction, oSubMenu )
{
	this.MenuItem = MenuItem;
	this.MenuItem( sLabelText, fAction, null, oSubMenu );

	// public
	this.checked = bChecked;
	this.radioGroupName = sRadioGroupName;
};


MenuItem_radiobutton.prototype = new MenuItem();
MenuItem_radiobutton.prototype.constructor = MenuItem_radiobutton;
MenuItem_radiobutton.superclass = MenuItem.prototype;

/**
 * @access public
 */
MenuItem_radiobutton.prototype.getIconHtml = function()
{
	return "<span class=\"radio-button\">" + ( this.checked ? "n" : "&nbsp;" ) + "</span>";
};

/**
 * @access public
 */
MenuItem_radiobutton.prototype.getIconCellHtml = function()
{
	return "<td class=\"icon-cell\">" + this.makeDisabledContainer( this.getIconHtml() ) + "</td>";
};

/**
 * @access public
 */
MenuItem_radiobutton.prototype.getCssClass = function()
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
MenuItem_radiobutton.prototype._menuItem_dispatchAction = MenuItem.prototype.dispatchAction;

/**
 * @access public
 */
MenuItem_radiobutton.prototype.dispatchAction = function()
{
	if ( !this.checked )
	{
		// loop through items in parent menu
		var items = this.parentMenu.items;
		var l = items.length;
		
		for ( var i = 0; i < l; i++ )
		{
			if ( items[i] instanceof MenuItem_radiobutton )
			{
				if ( items[i].radioGroupName == this.radioGroupName )
					items[i].checked = items[i] == this;
			}
		}
		
		this.parentMenu.invalidate();
	}

	this._menuItem_dispatchAction();
	this.parentMenu.closeAllMenus();
};
