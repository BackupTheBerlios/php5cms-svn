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
MPPlaylistCollection = function( playlistcollection )
{
	this.Base = Base;
	this.Base();
	
	this.playlistcollection = playlistcollection;
	
	this.onDelete = new Function;
	this.onRemove = new Function;
};


MPPlaylistCollection.prototype = new Base();
MPPlaylistCollection.prototype.constructor = MPPlaylistCollection;
MPPlaylistCollection.superclass = Base.prototype;

/**
 * Moves a playlist to the deleted items folder.
 *
 * @access public
 */
MPPlaylistCollection.prototype.setDeleted = function( playlist )
{
	if ( playlist != null )
	{
		this.onDelete();
		return this.playlistcollection.setDeletetd( playlist.playlist? playlist.playlist : playlist );
	}
};

/**
 * Removes a playlist from the collection.
 *
 * @access public
 */
MPPlaylistCollection.prototype.remove = function( playlist )
{
	if ( playlist != null )
	{
		this.onRemove();
		return this.playlistcollection.remove( playlist.playlist? playlist.playlist : playlist );
	}
};

/**
 * Creates a new playlist.
 *
 * @access public
 */
MPPlaylistCollection.prototype.newPlaylist = function( name )
{
	if ( name != null )
		return new MPPlaylist( this.playlistcollection.newPlaylist( name ) );
};

/**
 * Retrieves a value indicating whether the specified playlist is in the deleted items folder.
 *
 * @access public
 */
MPPlaylistCollection.prototype.isDeleted = function( playlist )
{
	if ( playlist != null )
		return this.playlistcollection.isDeleted( playlist.playlist? playlist.playlist : playlist );
};

/**
 * Retrieves MPPlaylistArray containing all the playlists in the media library.
 *
 * @access public
 */
MPPlaylistCollection.prototype.getAll = function()
{
	return new MPPlaylistArray( this.playlistcollection.getAll() );
};

/**
 * Adds a playlist to the PlaylistCollection.
 *
 * @access public
 */
MPPlaylistCollection.prototype.importPlaylist = function( playlist )
{
	if ( playlist != null )
		this.playlistcollection.importPlaylist( playlist.playlist? playlist.playlist : playlist );
};

/**
 * Retrieves MPPlaylistArray containing the specified playlist, if it exists.
 *
 * @access public
 */
MPPlaylistCollection.prototype.getByName = function( name )
{
	if ( name != null )
		return new MPPlaylistArray( this.playlistcollection.getByName( name ) );
};
