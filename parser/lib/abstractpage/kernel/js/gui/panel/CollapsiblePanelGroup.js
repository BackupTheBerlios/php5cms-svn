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
 * @package gui_panel
 */
 
/**
 * Constructor
 *
 * @access public
 */
CollapsiblePanelGroup = function( x, y )
{
	this.Base = Base;
	this.Base();
	
	this.left  = x || null;
	this.top   = y || null;
	
	this.panels = new Array();
};


CollapsiblePanelGroup.prototype = new Base();
CollapsiblePanelGroup.prototype.constructor = CollapsiblePanelGroup;
CollapsiblePanelGroup.superclass = Base.prototype;

/**
 * @access public
 */
CollapsiblePanelGroup.prototype.addPanel = function( panel )
{
	if ( typeof panel == "object" && panel.constructor && panel.constructor == CollapsiblePanel ) /*Util.is_a( panel, "CollapsiblePanel" ) */
		this.panels[this.panels.length] = panel;
};

/**
 * @access public
 */
CollapsiblePanelGroup.prototype.init = function()
{
	for ( var i in this.panels )
		this.panels[i].init();
};

/**
 * @access public
 */
CollapsiblePanelGroup.prototype.expandAll = function()
{
	for ( var i in this.panels )
		this.panels[i].expand();
};

/**
 * @access public
 */
CollapsiblePanelGroup.prototype.collapseAll = function()
{
	for ( var i in this.panels )
		this.panels[i].collapse();
};

/**
 * @access public
 */
CollapsiblePanelGroup.prototype.toggleAll = function()
{
	for ( var i in this.panels )
		this.panels[i].toggle();
};

/**
 * @access public
 */
CollapsiblePanelGroup.prototype.toHTML = function()
{
	var str   = "";
	var begin = "";
	var end   = "";
		
	if ( this.left && this.top )
	{
		begin = "<div style=\"position:absolute;left:" + this.left + "px;top:" + this.top + "px;\">\n";
		end   = "</div>\n";
	}

	for ( var i in this.panels )
		str += this.panels[i].toHTML();
		
	return begin + str + end;
};
