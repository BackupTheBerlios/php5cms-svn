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
MenuCollection = function()
{
	this.Base = Base;
	this.Base();
};


MenuCollection.prototype = new Base();
MenuCollection.prototype.constructor = MenuCollection;
MenuCollection.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
MenuCollection.imagePath = "../img/";

/**
 * @access public
 * @static
 */
MenuCollection.defaultFn = function( val )
{
	alert( val );
}


/**
 * @access public
 * @static
 */
MenuCollection.booleanCallback = MenuCollection.defaultFn;

/**
 * @access public
 * @static
 */
MenuCollection.getBooleanMenu = function( callback )
{
	MenuCollection.booleanCallback = ( typeof( callback ) == "function" )? callback : MenuCollection.defaultFn;
	
	var menu = new Menu();
	menu.add( new MenuItem( "true",  function() { MenuCollection.booleanCallback( true );  }, MenuCollection.imagePath + "icons16x16/bullet.gif" ) );
	menu.add( new MenuItem( "false", function() { MenuCollection.booleanCallback( false ); }, MenuCollection.imagePath + "icons16x16/bullet.gif" ) );
	
	return menu;
};


/**
 * @access public
 * @static
 */
MenuCollection.mediaControlsCallback = MenuCollection.defaultFn;

/**
 * @access public
 * @static
 */
MenuCollection.getMediaControls = function( callback )
{
	MenuCollection.mediaControlsCallback = ( typeof( callback ) == "function" )? callback : MenuCollection.defaultFn;
	
	var html = "";
	html += '<style type="text/css">';
	html += '.coolbutton          { behavior: url(../js/behaviours/apbutton.htc); cursor: default; font: icon; width: 1px; height: 1px; }';
	html += '.coolbutton img      { vertical-align: top; margin-right: 2px; }';
	html += '.menu-body .hover td { background-color: #ffffff; }'
	html += '</style>'
	html += '<table align="center" width="170" height="24" cellspacing="2" cellpadding="0" border="0"><tr>';
    html += '<td id="firstBt" align="center" class="coolButton" onclick="window.parent.parent.MenuCollection.mediaControlsCallback(\'first\')" nowrap><img src="../img/icons16x16/goto_first.gif" width="16" height="16" border="0"></td>';
    html += '<td id="prevBt"  align="center" class="coolButton" onclick="window.parent.parent.MenuCollection.mediaControlsCallback(\'prev\')"  nowrap><img src="../img/icons16x16/goto_prev.gif"  width="16" height="16" border="0"></td>';
    html += '<td id="playBt"  align="center" class="coolButton" onclick="window.parent.parent.MenuCollection.mediaControlsCallback(\'play\')"  nowrap><img src="../img/icons16x16/goto_next.gif"  width="16" height="16" border="0"></td>';
    html += '<td id="pauseBt" align="center" class="coolButton" onclick="window.parent.parent.MenuCollection.mediaControlsCallback(\'pause\')" nowrap><img src="../img/icons16x16/pause.gif"      width="16" height="16" border="0"></td>';
    html += '<td id="stopBt"  align="center" class="coolButton" onclick="window.parent.parent.MenuCollection.mediaControlsCallback(\'stop\')"  nowrap><img src="../img/icons16x16/stop.gif"       width="16" height="16" border="0"></td>';
	html += '<td id="nextBt"  align="center" class="coolButton" onclick="window.parent.parent.MenuCollection.mediaControlsCallback(\'next\')"  nowrap><img src="../img/icons16x16/goto_next.gif"  width="16" height="16" border="0"></td>';
    html += '<td id="lastBt"  align="center" class="coolButton" onclick="window.parent.parent.MenuCollection.mediaControlsCallback(\'last\')"  nowrap><img src="../img/icons16x16/goto_last.gif"  width="16" height="16" border="0"></td>';
	html += '</tr></table>';

	var menu = new MenuPane();
	menu.add( new MenuItem_pane( html ) );
	
	return menu;
};


/**
 * @access public
 * @static
 */
MenuCollection.intelliSenseCallback = MenuCollection.defaultFn;

/**
 * @access public
 * @static
 */
MenuCollection.getIntelliSenseMenu = function( callback, items )
{
	MenuCollection.intelliSenseCallback = ( typeof( callback ) == "function" )? callback : MenuCollection.defaultFn;
	
	var html = "<div style=\"overflow-y:auto;overflow-x:hidden;width:120px;height:120px;\"><div style=\"padding:2px;\">";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "Dummy text.<br>";
	html += "</div></div>";
	
	var menu = new MenuPane();
	menu.add( new MenuItem_pane( html ) );
	
	return menu;
};
