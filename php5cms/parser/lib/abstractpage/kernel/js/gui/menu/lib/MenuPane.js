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
MenuPane = function()
{
	this.Menu = Menu;
	this.Menu();
};


MenuPane.prototype = new Menu();
MenuPane.prototype.constructor = MenuPane;
MenuPane.superclass = Menu.prototype;

/**
 * @access public
 */
MenuPane.prototype.toHtml = function()
{	
	var items = this.items;
	var l = items.length
	var itemsHtml = new Array( l );
	
	for ( var i = 0; i < l; i++ )
		itemsHtml[i] = items[i].toHtml();

	return  "<html><head>" +
			"<link type=\"text/css\" rel=\"StyleSheet\" href=\"" + this.cssFile + "\" />" +
			"</head><body class=\"menu-body\">" +
			"<div class=\"outer-border\"><div class=\"canvas\">" +
			"<table id=\"scroll-up-item\" cellspacing=\"0\" cellpadding=\"0\" style=\"display: none\">" +
			"<tr class=\"disabled\"><td>" +
			"<span class=\"disabled-container\"><span class=\"disabled-container\">" +
			"5" +
			"</span></span>" + "</td></tr></table>" +
			"<div id=\"scroll-container\">" +
			"<table cellspacing=\"0\" cellpadding=\"0\">" + itemsHtml.join( "" ) + "</table>" +
			"</div>" +
			"<table id=\"scroll-down-item\" cellspacing=\"0\" style=\"display: none\">" +
			"<tr><td>" +
			"<span class=\"disabled-container\"><span class=\"disabled-container\">" +
			"6" +
			"</span></span>" +
			"</td></tr></table>" +
			"</div></div>" +
			"</body></html>";
};
