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
MenuItem_separator = function()
{
	this.MenuItem = MenuItem;
	this.MenuItem();
}


MenuItem_separator.prototype = new MenuItem();
MenuItem_separator.prototype.constructor = MenuItem_separator;
MenuItem_separator.superclass = MenuItem.prototype;

/**
 * @access public
 */
MenuItem_separator.prototype.toHtml = function()
{
	return "<tr class=\"" + this.getCssClass() + "\"" +
		( !this.visible? " style=\"display: none\"" : "") +
		"><td colspan=\"4\">" +
		"<div class=\"separator-line\"></div>" +
		"</td></tr>";
};

/**
 * @access public
 */
MenuItem_separator.prototype.getCssClass = function()
{
	return "separator";
};
