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
 * @package io_ini
 */
 
/**
 * Constructor
 *
 * @access public
 */
INIFile = function()
{
	this.Base = Base;
	this.Base();
	
	this.groups = new Dictionary();
	this.buffer = new HTTPBuffer();
	
	this.defaultGroup   = "settings";
	this.loaded         = false;
	this.actualFile     = null;
	this.filesNotLoaded = new Array();
	
	// event
	this.onload = new Function;
};


INIFile.prototype = new Base();
INIFile.prototype.constructor = INIFile;
INIFile.superclass = Base.prototype;

/**
 * @access public
 */
INIFile.prototype.load = function( file )
{
	this.loaded = false;
	this.actualFile = file;
	
	// Download behaviour has no error callback, i guess ...
	// So I better put the filename to this list and remove it later.
	this.filesNotLoaded[this.filesNotLoaded.length] = file;
	
	var me = this;
	
	// callback stuff
	this.buffer.onload = function( e )
	{
		var i, pair;
		var raw = this.getHTML();

		var actualGroup = me.defaultGroup;
		
		raw = raw.tokenize();
		
		for ( var i in raw )
		{			
			// group
			if ( ( raw[i].indexOf( "[" ) != -1 ) && ( raw[i].indexOf( "]" ) != -1 ) )
			{
				actualGroup = raw[i].eatWhitespace();
				actualGroup = actualGroup.substring( 1, actualGroup.length - 1 );
				
				me.groups.add( actualGroup, new Dictionary() );
			}
			
			// pair
			if ( raw[i].indexOf( "=" ) != -1 )
			{
				pair = raw[i].split( "=" );
				me.groups.get( actualGroup ).add( pair[0].trim(), pair[1].trim().removeQuotes() );
			}
			
			// skip empty line
			if ( raw[i].isEmpty() )
				continue;
				
			// skip comment
			if ( ( raw[i].charAt( 0 ) == "#" ) || ( raw[i].charAt( 0 ) == ";" ) )
				continue;
		}
		
		// success
		Util.removeFromArray( me.filesNotLoaded, me.actualFile );
		me.loaded = true;
		
		// fire event
		me.onload();
	}

	this.buffer.getURL( file );
};

/**
 * @access public
 */
INIFile.prototype.hasLoaded = function()
{
	return this.loaded;
};

/**
 * @access public
 */
INIFile.prototype.get = function( val, group )
{
	if ( this.groups.contains( group ) )
	{
		if ( this.groups.get( group ).contains( val ) )
			return this.groups.get( group ).get( val );
		else
			return false;
	}
	else
	{
		return false;
	}
};
