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
 * @package gui_tab_lib
 */
 
/**
 * Constructor
 *
 * @paramm  HTMLElement el       The html element used to represent the tab pane
 * @param   TabPane	    tabpane  The parent tab pane
 * @param   int         nindex	 The index of the page in the parent pane page array
 * @access  public
 */
TabPage = function( el, tabPane, nIndex )
{
	this.Base = Base;
	this.Base();
	
	if ( !TabPane.hasSupport() || el == null )
		return;
	
	this.element = el;
	this.element.tabPage = this;
	this.index = nIndex;
	
	var cs = el.childNodes;
	for ( var i = 0; i < cs.length; i++ )
	{
		if ( cs[i].nodeType == 1 && cs[i].className == "tab" )
		{
			this.tab = cs[i];
			break;
		}
	}
	
	// insert a tag around content to support keyboard navigation
	var a = document.createElement( "A" );
	// a.href = "javascript:void(0);";
	
	while ( this.tab.hasChildNodes() )
		a.appendChild( this.tab.firstChild );
	
	this.tab.appendChild( a );
	
	// hook up events, using DOM0
	var oThis = this;
	
	if ( this.tab.parentNode.pagedisabled != "true" )
	{
		this.tab.onclick = function()
		{
			oThis.select();
		};
		this.tab.onmouseover = function()
		{
			TabPage.tabOver( oThis );
		};
		this.tab.onmouseout = function()
		{
			TabPage.tabOut( oThis );
		};
	}
	else
	{
		this.tab.className = "notallowed";
	}
};


TabPage.prototype = new Base();
TabPage.prototype.constructor = TabPage;
TabPage.superclass = Base.prototype;

/**
 * @access public
 */
TabPage.prototype.show = function()
{
	var el = this.tab;
	var s  = el.className + " selected";
	s = s.replace(/ +/g, " ");
	el.className = s;
		
	this.element.style.display = "block";
};

/**
 * @access public
 */
TabPage.prototype.hide = function()
{
	var el = this.tab;
	var s  = el.className;
	s = s.replace(/ selected/g, "");
	el.className = s;

	this.element.style.display = "none";
};

/**
 * @access public
 */
TabPage.prototype.select =	function()
{
	this.tabPane.setSelectedIndex( this.index );
};


/**
 * @access public
 * @static
 */
TabPage.tabOver = function( tabpage )
{
	var el = tabpage.tab;
	var s  = el.className + " hover";
	s = s.replace(/ +/g, " ");
	el.className = s;
};

/**
 * @access public
 * @static
 */
TabPage.tabOut = function( tabpage )
{
	var el = tabpage.tab;
	var s  = el.className;
	s = s.replace(/ hover/g, "");
	el.className = s;
};
