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
INIParser = function( raw )
{
	this.Base = Base;
	this.Base();
	
	this.groups = new Dictionary();
	this.parsed = false;
	
	this.defaultGroup = "settings";
	
	if ( raw != null )
		this.parse( raw );
};


INIParser.prototype = new Base();
INIParser.prototype.constructor = INIParser;
INIParser.superclass = Base.prototype;

/**
 * @access public
 */
INIParser.prototype.parse = function( raw )
{
	if ( ( raw == null ) || ( typeof( raw ) != "string" ) )
		return false;
		
	var i, pair;
	var actualGroup = this.defaultGroup;
		
	raw = raw.tokenize();
		
	for ( var i in raw )
	{			
		// group
		if ( ( raw[i].indexOf( "[" ) != -1 ) && ( raw[i].indexOf( "]" ) != -1 ) )
		{
			actualGroup = raw[i].eatWhitespace();
			actualGroup = actualGroup.substring( 1, actualGroup.length - 1 );
				
			this.groups.add( actualGroup, new Dictionary() );
		}
			
		// pair
		if ( raw[i].indexOf( "=" ) != -1 )
		{
			pair = raw[i].split( "=" );
			this.groups.get( actualGroup ).add( pair[0].trim(), pair[1].trim().removeQuotes() );
		}
			
		// skip empty line
		if ( raw[i].isEmpty() )
			continue;
				
		// skip coment
		if ( ( raw[i].charAt( 0 ) == "#" ) || ( raw[i].charAt( 0 ) == ";" ) )
			continue;
	}
	
	this.parsed = true;
	return true;
};

/**
 * @access public
 */
INIParser.prototype.get = function( val, group )
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
