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
 * @param  HTMLElement  el         	The html element used to represent the tab pane
 * @param  boolean      bUseCookie  Optional. Default is true. Used to determine whether to us persistance using cookies or not
 * @access public
 */
TabPane = function( el, bUseCookie )
{
	this.Base = Base;
	this.Base();
	
	if ( !TabPane.hasSupport() || el == null )
		return;
	
	this.element = el;
	this.element.tabPane = this;
	this.pages = [];
	this.selectedIndex = null;
	this.useCookie = ( bUseCookie != null )? bUseCookie : true;
	
	// add class name tag to class name
	this.element.className = this.classNameTag + " " + this.element.className;
	
	// add tab row
	this.tabRow = document.createElement( "div" );
	this.tabRow.className = "tab-row";
	el.insertBefore( this.tabRow, el.firstChild );

	var tabIndex = 0;
	if ( this.useCookie )
	{
		tabIndex = Number( CookieUtil.read( "tab_" + this.element.id ) );
		
		if ( isNaN( tabIndex ) )
			tabIndex = 0;
	}
	
	this.selectedIndex = tabIndex;
	
	// loop through child nodes and add them
	var cs = el.childNodes;
	var n;
	
	for ( var i = 0; i < cs.length; i++ )
	{
		if (cs[i].nodeType == 1 && cs[i].className == "tab-page")
			this.addTabPage( cs[i] );
	}
};


TabPane.prototype = new Base();
TabPane.prototype.constructor = TabPane;
TabPane.superclass = Base.prototype;

/**
 * @access public
 */
TabPane.prototype.classNameTag = "tab-pane-control";

/**
 * @access public
 */
TabPane.prototype.setSelectedIndex = function( n )
{
	if ( this.selectedIndex != n )
	{
		if ( this.selectedIndex != null && this.pages[ this.selectedIndex ] != null )
			this.pages[ this.selectedIndex ].hide();
			
		this.selectedIndex = n;
		this.pages[ this.selectedIndex ].show();
			
		if ( this.useCookie )
			CookieUtil.save( "tab_" + this.element.id, n );	// session cookie
	}
};

/**
 * @access public
 */
TabPane.prototype.getSelectedIndex = function()
{
	return this.selectedIndex;
};

/**
 * @access public
 */
TabPane.prototype.addTabPage =	function( oElement )
{
	if ( !TabPane.hasSupport() )
		return;
		
	if ( oElement.tabPage == this )	// already added
		return oElement.tabPage;
	
	var n  = this.pages.length;
	var tp = this.pages[n] = new TabPage( oElement, this, n );
	tp.tabPane = this;
		
	// move the tab out of the box
	this.tabRow.appendChild( tp.tab );
				
	if ( n == this.selectedIndex )
		tp.show();
	else
		tp.hide();
			
	return tp;
};


/**
 * @access public
 * @static
 */
TabPane.setupAllTabs = function( useCookies )
{
	if ( !TabPane.hasSupport() )
		return;

	var all = document.getElementsByTagName( "*" );
	var l = all.length;
	
	var cn, el;
	var parentTabPane;
	
	var tabPaneRe = /tab\-pane/;
	var tabPageRe = /tab\-page/;
		
	for ( var i = 0; i < l; i++ )
	{
		el = all[i]
		cn = el.className;

		// no className
		if ( cn == "" )
			continue;
		
		// uninitiated tab pane
		if ( tabPaneRe.test( cn ) && !el.tabPane )
		{
			new TabPane( el, useCookies );
		}
		// unitiated tab page wit a valid tab pane parent
		else if ( tabPageRe.test( cn ) && !el.tabPage && tabPaneRe.test( el.parentNode.className ) )
		{
			el.parentNode.tabPane.addTabPage( el );			
		}
	}
};

/**
 * This function is used to define if the browser supports the needed features.
 *
 * @access public
 * @static
 */
TabPane.hasSupport = function()
{
	if ( typeof TabPane.hasSupport.support != "undefined" )
		return TabPane.hasSupport.support;
	
	var ie55 = /msie 5\.[56789]/i.test( navigator.userAgent );
	
	TabPane.hasSupport.support = ( typeof document.implementation != "undefined" && document.implementation.hasFeature( "html", "1.0" ) || ie55 )
			
	// IE55 has a serious DOM1 bug... Patch it!
	if ( ie55 )
	{
		document._getElementsByTagName = document.getElementsByTagName;
		document.getElementsByTagName = function ( sTagName )
		{
			if ( sTagName == "*" )
				return document.all;
			else
				return document._getElementsByTagName( sTagName );
		};
	}

	return TabPane.hasSupport.support;
};

/**
 * @access public
 * @static
 */
TabPane.setPageNotAllowed = function( element )
{
	if ( element )
		element.setAttribute( "pagedisabled", "true" );
};
