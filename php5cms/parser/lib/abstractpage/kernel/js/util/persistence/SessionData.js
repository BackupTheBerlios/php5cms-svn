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
 * @package util_persistence
 */
 
/**
 * Constructor
 *
 * @access public
 */
SessionData = function()
{
	this.Base = Base;
	this.Base();

	this.pd = new PersistentData();
	this.allowOverwriting = true;
	
	if ( !this.hasSessionData() )
		this.pd.set( "sessiondata", "true" );
};


SessionData.prototype = new Base();
SessionData.prototype.constructor = SessionData;
SessionData.superclass = Base.prototype;

/**
 * @access public
 */
SessionData.prototype.hasSessionData = function()
{
	return this.pd.has( "sessiondata" );
};

/**
 * @access public
 */
SessionData.prototype.getKeys = function()
{
	var list = this.pd.get( "sessiondata" );
	list = list.split( SessionData.arrayDelimiter );
	
	// strip header and return array
	list = this._removeFromList( list, "true" );
	return list;
};

/**
 * @access public
 */
SessionData.prototype.has = function( name )
{
	return this.pd.has( name );
};

/**
 * @access public
 */
SessionData.prototype.get = function( name )
{
	if ( name == null || !this.has( name ) )
		return false;
	
	return this.pd.get( name );
};

/**
 * Returns all session vars as a Dictionary.
 *
 * @access public
 */
SessionData.prototype.getAll = function()
{
	var keys = this.getKeys();
	var dict = new Dictionary();
	
	for ( var i in keys )
		dict.add( keys[i], this.get( keys[i] ) );
		
	return dict;
};

/**
 * @access public
 */
SessionData.prototype.add = function( name, val, name_prefix )
{
	if ( name == null || val == null )
		return false;
	
	if ( this.allowOverwriting )
	{
		// append only if new to the list
		if ( !this.has( name ) )
		{
			var list = this.pd.get( "sessiondata" );
			list = list.split( SessionData.arrayDelimiter );
			list[list.length] = ( name_prefix || "" ) + name;
			list = list.join( SessionData.arrayDelimiter );
			this.pd.set( "sessiondata", list );
		}
			
		// save value
		this.pd.set( name, val );
	}
};

/**
 * @access public
 */
SessionData.prototype.remove = function( name )
{
	if ( name == null || !this.has( name ) )
		return false;

	// update
	var list = this.pd.get( "sessiondata" );
	list = list.split( SessionData.arrayDelimiter );
	
	// strip header
	list = this._removeFromList( list, name );

	list = list.join( SessionData.arrayDelimiter );
	this.pd.set( "sessiondata", list );
	
	this.pd.del( name );
};

/**
 * @access public
 */
SessionData.prototype.removeAll = function()
{
	var list = this.getKeys();
	
	for ( var i in list )
		this.pd.del( list[i] );
		
	this.pd.del( "sessiondata" );
};

/**
 * @access public
 */
SessionData.prototype.addDictionary = function( dict, prefix )
{
	if ( dict != null && dict.contains )
	{
		var keys = dict.getKeys();
		
		for ( var i in keys )
			this.add( ( prefix || "" ) + keys[i], dict.get( keys[i] ) );

		return true;
	}
	
	return false;
};

/**
 * @access public
 */
SessionData.prototype.addObject = function( o, prefix )
{
	if ( o != null && ( typeof o != "object" ) )
		return false

	var prop;
	var propVal;

	for ( prop in o )
	{
		propVal = o[prop];
		this.add( ( prefix || "" ) + prop, propVal );
	}
	
	return true;
};

/**
 * @access public
 */
SessionData.prototype.dump = function( lbr )
{
	var str  = "";
	var keys = this.getKeys();
	
	for ( var i in keys )
		str += keys[i] + ": " + this.get( keys[i] ) + ( lbr || "<br>\n" );
		
	return str;
};


// private methods

/**
 * @access private
 */
SessionData.prototype._removeFromList = function( list, item )
{
	if ( list == null || item == null )
		return false;

	for ( var i = 0; i < list.length; i++ )
	{
		if ( list[i] == item )
			list.splice( i, 1 );
	}
	
	return list;
};


/**
 * @access public
 * @static
 */
SessionData.arrayDelimiter = ":";
