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
 * @package html_css
 */
 
/**
 * Constructor
 * CSS wrapper (IE)
 *
 * @access public
 */
CSS = function( id, context )
{
	this.Base = Base;
	this.Base();
	
	this.id = id || CSS.defaultID;
	this.indexmap = new Array();
	
	if ( ( this.id == CSS.defaultID ) && ( CSS.styleElementCreated == false ) )
	{
		var styleobj = document.createElement( "style" );
		styleobj.setAttribute( "id", this.id );
		document.body.appendChild( styleobj );
		
		CSS.styleElementCreated = true;
		this.setContext( context || "" );
	}
};


CSS.prototype = new Base();
CSS.prototype.constructor = CSS;
CSS.superclass = Base.prototype;

/**
 * @access public
 */
CSS.prototype.setContext = function( context )
{
	if ( context != null )
		this.context = context;
};

/**
 * @access public
 */
CSS.prototype.clearContext = function()
{
	this.setContext( "" );
};

/**
 * @access public
 */
CSS.prototype.getContext = function()
{
	return this.context;
};

/**
 * @access public
 */
CSS.prototype.addRule = function( selector, style )
{
	if ( selector == null )
		return;
	
	// @import?
	if ( style != null )
		this.indexmap[this.indexmap.length] = selector;
	
	document.styleSheets[this.id].addRule(
		( this.context != "" )? this.context + " " + selector : selector,
		style || null,
		( style != null )? this.indexmap.length : null
	);
};

/**
 * @access public
 */
CSS.prototype.removeRule = function( selector )
{
	var selpos = this.getSelectorIndex( selector );
	
	if ( selpos == -1 )
	{
		return false;
	}
	else
	{
		document.styleSheets[this.id].removeRule( selpos );
		Util.removeFromArray( this.indexmap, selpos );
		
		return true;
	}
};

/**
 * @access public
 */
CSS.prototype.importRules = function( file )
{
	document.styleSheets[this.id].addImport( file )
};

/**
 * @access public
 */
CSS.prototype.getSelectorIndex = function( selector )
{
	for ( var i in this.indexmap )
	{
		if ( this.indexmap[i] == selector )
			return i;
	}
	
	return -1;
};


/**
 * @access public
 * @static
 */
CSS.defaultID = "mystyles";

/**
 * @access public
 * @static
 */
CSS.styleElementCreated = false;
