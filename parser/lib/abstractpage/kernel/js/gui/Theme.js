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
 * @package gui
 */
 
/**
 * Constructor
 *
 * @access public
 */
Theme = function()
{
	this.Base = Base;
	this.Base();

	this.css    = new CSS();
	this.themes = new Dictionary();
	this.map    = new Array();

	this.themeIsMapped = false;
	this.activeTheme   = false;
};


Theme.prototype = new Base();
Theme.prototype.constructor = Theme;
Theme.superclass = Base.prototype;

/**
 * @access public
 */
Theme.prototype.register = function( name, hash )
{
	if ( ( name != null ) && ( hash != null ) && ( typeof( hash ) == "object" ) )
	{
		this.themes.add( name, hash );
		
		if ( this.themeIsMapped == false )
		{
			this.mapTheme( hash );
			this.themeIsMapped = true;
		}
		
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
Theme.prototype.mapTheme = function( hash )
{
	var prop;
	var propVal;

	for ( prop in hash )
	{
		propVal = hash[prop];
		this.map[this.map.length] = prop;
	}
};

/**
 * @access public
 */
Theme.prototype.setActive = function( name )
{
	// no theme
	if ( this.map.length == 0 )
		return false;
	
	var prop, propVal;
	var cssobj = this.getTheme( name );
	
	if ( cssobj )
	{
		if ( this.activeTheme )
		{
			// remove existing rules of theme
			for ( var i in this.map )
				this.css.removeRule( this.map[i] )
		}
	
		// apply new theme
		for ( prop in cssobj )
		{
			propVal = cssobj[prop];
			this.css.addRule( prop, propVal )
		}
		
		this.activeTheme = true;
	}
};

/**
 * @access public
 */
Theme.prototype.hasTheme = function( name, getIndex )
{
	var themes = this.themes.getKeys();
	
	for ( var i in themes )
	{
		if ( themes[i] == name )
			return true;
	}

	return false;
};

/**
 * @access public
 */
Theme.prototype.getTheme = function( name )
{
	if ( !this.hasTheme( name ) )
		return false;
		
	var themes = this.themes.getKeys();
	
	for ( var i in themes )
	{
		if ( themes[i] == name )
			return this.themes.get( themes[i] );
	}
};
