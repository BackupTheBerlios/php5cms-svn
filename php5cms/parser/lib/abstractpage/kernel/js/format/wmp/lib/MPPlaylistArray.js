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
MPPlaylistArray = function( playlistarray )
{
	this.Base = Base;
	this.Base();
	
	this.playlistarray = playlistarray;
};


MPPlaylistArray.prototype = new Base();
MPPlaylistArray.prototype.constructor = MPPlaylistArray;
MPPlaylistArray.superclass = Base.prototype;

/**
 * Retrieves the number of playlists in the playlist array.
 *
 * @access public
 */
MPPlaylistArray.prototype.getCount = function()
{
	return this.playlistarray.count;
};

/**
 * Retrieves a MPPlaylist object at the given index.
 *
 * @access public
 */
MPPlaylistArray.prototype.getItem = function( index )
{
	if ( index != null && Util.is_int( index ) && index <= this.getCount() )
		return new MPPlaylist( this.playlistarray.item( index ) );
};
