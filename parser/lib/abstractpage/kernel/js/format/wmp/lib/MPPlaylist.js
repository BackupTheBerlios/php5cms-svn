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
 * @package format_wmf_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
MPPlaylist = function( playlist )
{
	this.Base = Base;
	this.Base();
	
	this.playlist = playlist;
	
	this.onRemove = new Function;
	this.onAppend = new Function;
};


MPPlaylist.prototype = new Base();
MPPlaylist.prototype.constructor = MPPlaylist;
MPPlaylist.superclass = Base.prototype;

/**
 * Specifies the value of a playlist attribute.
 *
 * @access public
 */
MPPlaylist.prototype.setItemInfo = function( name, value )
{
	if ( name != null && value != null )
		this.playlist.setItemInfo( name, value );
};

/**
 * @access public
 */
MPPlaylist.prototype.getItemInfo = function( name )
{
	if ( name != null )
		return this.playlist.getItemInfo( name );
};

/**
 * Specifies or retrieves the name of the playlist.
 *
 * @access public
 */
MPPlaylist.prototype.setName = function( name )
{
	if ( name != null )
		this.playlist.name = name;
};

/**
 * @access public
 */
MPPlaylist.prototype.getName = function()
{
	return this.playlist.name;
};

/**
 * Removes the specified item from the playlist.
 *
 * @access public
 */
MPPlaylist.prototype.removeItem = function( item )
{
	if ( item != null )
	{
		this.playlist.removeItem( item );
		this.onRemove();
	}
};

/**
 * Retrieves a value indicating whether the supplied playlist object is identical to the current one.
 *
 * @access public
 */
MPPlaylist.prototype.isIdentical = function( playlist )
{
	if ( playlist != null )
		return this.playlist.isIdentical( playlist.playlist? playlist.playlist : playlist );
};

/**
 * Retrieves the number of media items in the playlist.
 *
 * @access public
 */
MPPlaylist.prototype.getCount = function()
{
	return this.playlist.count;
};

/**
 * Retrieves the number of attributes associated with the playlist.
 *
 * @access public
 */
MPPlaylist.prototype.getAttributeCount = function()
{
	return this.playlist.attributeCount;
};

/**
 * Retrieves the name of an attribute specified by an index.
 *
 * @access public
 */
MPPlaylist.prototype.getAttribute = function( index )
{
	if ( index != null && Util.is_int( index ) && index <= this.getAttributeCount() )
		return this.playlist.attributeName( index );
};

/**
 * Retrieves MPMedia object by the given index.
 *
 * @access public
 */
MPPlaylist.prototype.getItem = function( index )
{
	if ( index != null && Util.is_int( index ) && index <= this.getCount() )
		return new MPMedia( this.playlist.item( index ) );
};

/**
 * Adds a media item to the end of the playlist.
 *
 * @access public
 */
MPPlaylist.prototype.appendItem = function( item )
{
	if ( item != null )
	{
		this.playlist.appendItem( item );
		this.onAppend();
	}
};

/**
 * Changes the location of an item in the playlist.
 *
 * @access public
 */
MPPlaylist.prototype.moveItem = function( oldIndex, newIndex )
{
	if ( ( oldIndex != null && Util.is_int( oldIndex ) && oldIndex <= this.getCount() ) &&
		 ( newIndex != null && Util.is_int( newIndex ) && newIndex <= this.getCount() ) )
	{
		this.playlist.moveItem( oldIndex, newIndex );
	}
};

/**
 * Inserts a media item into the playlist at the specified location.
 *
 * @access public
 */
MPPlaylist.prototype.insertItem = function( index, item )
{
	if ( ( index != null && Util.is_int( index ) && index <= this.getCount() ) && item != null )
		this.playlist.insertItem( index, item );
};
