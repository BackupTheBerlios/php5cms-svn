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
MPMediaCollection = function( mediacollection )
{
	this.Base = Base;
	this.Base();
	
	this.mediacollection = mediacollection;
	
	this.onDelete = new Function;
	this.onRemove = new Function;
};


MPMediaCollection.prototype = new Base();
MPMediaCollection.prototype.constructor = MPMediaCollection;
MPMediaCollection.superclass = Base.prototype;

/**
 * Moves the specified media item to the delted items folder.
 *
 * @access public
 */
MPMediaCollection.prototype.setDeleted = function( item )
{
	if ( item != null )
	{
		this.onDelete();
		return this.mediacollection.setDeleted( item );
	}
};

/**
 * Removes an item from the media collection.
 *
 * @access public
 */
MPMediaCollection.prototype.remove = function( item, del )
{
	if ( item != null && del != null && Util.is_bool( del ) )
	{
		this.onRemove();
		this.mediacollection.remove( item, del );
	}
};

/**
 * Retrieves a value indicating whether the specified media item is in the deleted items folder.
 *
 * @access public
 */
MPMediaCollection.prototype.isDeleted = function( item )
{
	if ( item != null )
		return this.mediacollection.isDeleted( item );
};

/**
 * Retrieves the index at which a given attribute resides within the set of available attribues.
 *
 * @access public
 */
MPMediaCollection.prototype.isDeleted = function( attribute )
{
	if ( attribute != null && (
		 attribute == "Album"           ||
		 attribute == "Artist"          ||
		 attribute == "Author"          ||
		 attribute == "Bitrate"         ||
		 attribute == "Copyright"       ||
		 attribute == "CreationDate"    ||
		 attribute == "DigitallySecure" ||
		 attribute == "Genre"           ||
		 attribute == "MediaType"       ||
		 attribute == "Name"            ||
		 attribute == "PlayCount"       ||
		 attribute == "SourceURL"       ||
		 attribute == "TOC" ) )
	{
		return this.mediacollection.getMediaAtom( attribute );
	}
};

/**
 * Retrieves MPPlaylist object of the media items with the specified name.
 *
 * @access public
 */
MPMediaCollection.prototype.getByName = function( name )
{
	if ( name != null )
		return new MPPlaylist( this.mediacollection.getByName( name ) );
};

/**
 * Retrieves MPPlaylist object of the media items with the specified genre.
 *
 * @access public
 */
MPMediaCollection.prototype.getByName = function( genre )
{
	if ( genre != null )
		return new MPPlaylist( this.mediacollection.getByGenre( genre ) );
};

/**
 * Retrieves MPPlaylist object of the media items with the specified author.
 *
 * @access public
 */
MPMediaCollection.prototype.getByAuthor = function( author )
{
	if ( author != null )
		return new MPPlaylist( this.mediacollection.getByAuthor( author ) );
};

/**
 * Retrieves MPPlaylist object of the media items from the specified album.
 *
 * @access public
 */
MPMediaCollection.prototype.getByAlbum = function( album )
{
	if ( album != null )
		return new MPPlaylist( this.mediacollection.getByAlbum( album ) );
};

/**
 * Retrieves MPPlaylist object of media items with the specified attribute having the specified value.
 *
 * @access public
 */
MPMediaCollection.prototype.getByAttribute = function( attribute, value )
{
	if ( attribute != null && value != null )
		return new MPPlaylist( this.mediacollection.getByAttribute( attribute, value ) );
};

/**
 * Retrieves MPStringCollection object representing the set of all values
 * for a given attribute within a given media type.
 *
 * @access public
 */
MPMediaCollection.prototype.getAttributeStringCollection = function( attribute, mediaType )
{
	if ( attribute != null && mediaType != null (
		 attribute == "Album"  ||
		 attribute == "Author" ||
		 attribute == "Artist" ||
		 attribute == "Genre" ) )
	{
		return MPStringCollection( this.mediacollection.getAttributeStringCollection( attribute, mediaType ) );
	}
};

/**
 * Retrieves MPPlaylist object containing all media items in the media library.
 *
 * @access public
 */
MPMediaCollection.prototype.getAll = function()
{
	return new MPPlaylist( this.mediacollection.getAll() );
};

/**
 * Adds a new media item to the media library.
 *
 * @access public
 */
MPMediaCollection.prototype.add = function( url )
{
	if ( url != null )
		this.mediacollection.add( url );
};
