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
MPMedia = function( mediaitem )
{
	this.Base = Base;
	this.Base();
	
	this.media = mediaitem;
};


MPMedia.prototype = new Base();
MPMedia.prototype.constructor = MPMedia;
MPMedia.superclass = Base.prototype;

/**
 * Specifies or retrieves the name of the media item.
 *
 * @access public
 */
MPMedia.prototype.setName = function( name )
{
	if ( name != null )
		this.media.name = name;
};

/**
 * @access public
 */
MPMedia.prototype.getName = function()
{
	return this.media.name;
};

/**
 * @access public
 */
MPMedia.prototype.setItemInfo = function( attribute, value )
{
	if ( attribute != null && value != null )
		this.media.setItemInfo( attribute, value );
};

/**
 * @access public
 */
MPMedia.prototype.getItemInfo = function( attribute )
{
	if ( attribute != null )
		this.media.getItemInfo( attribute );
};

/**
 * Retrieves the URL of the current media item.
 *
 * @access public
 */
MPMedia.prototype.getSourceURL = function()
{
	return this.media.sourceURL;
};

/**
 * Retrieves the number of markers in the media item.
 *
 * @access public
 */
MPMedia.prototype.getMarkerCount = function()
{
	return this.media.markerCount;
};

/**
 * Retrieves the width of the current media item in pixels.
 *
 * @access public
 */
MPMedia.prototype.getImageSourceWidth = function()
{
	return this.media.imageSourceWidth;
};

/**
 * Retrieves the height of the current media item in pixels.
 *
 * @access public
 */
MPMedia.prototype.getImageSourceHeight = function()
{
	return this.media.imageSourceHeight;
};

/**
 * Retrieves the duration of the current media item in seconds.
 *
 * @access public
 */
MPMedia.prototype.getDuration = function()
{
	return this.media.duration;
};

/**
 * Retrieves a string value indicating the duration of the current media item in hh:mm:ss format.
 *
 * @access public
 */
MPMedia.prototype.getDurationString = function()
{
	return this.media.durationString;
};

/**
 * Retrieves the number of attributes that can be queried and/or set for the media item.
 *
 * @access public
 */
MPMedia.prototype.getAttributeCount = function()
{
	return this.media.attributeCount;
};

/**
 * Returns a value indicating whether the specified attribute of the media item can be edited.
 *
 * @access public
 */
MPMedia.prototype.isReadOnlyItem = function( attribute )
{
	if ( attribute != null )
		return this.media.isReadOnlyItem( attribute );
};

/**
 * Retrieves a value indicating whether the supplied object is the same as the current one.
 *
 * @access public
 */
MPMedia.prototype.isIdentical = function( media )
{
	if ( media != null )
		return this.media.isIdentical( media.media? media.media : media ); 
};

/**
 * Returns a value indicating whether the media item is a member of the specified playlist.
 *
 * @access public
 */
MPMedia.prototype.isMemberOf = function( playlist )
{
	if ( plalist != null )
		return this.media.isMemberOf( playlist.playlist? playlist.playlist : playlist )
};

/**
 * Retrieves the time of the marker at the specified index.
 *
 * @access public
 */
MPMedia.prototype.getMarkerTime = function( markerNum )
{
	if ( markerNum != null && Util.is_int( markerNum ) && markerNum >= 1 )
		return this.media.getMarkerTime( markerNum );
};

/**
 * Retrieves the name of the marker at the specified index.
 *
 * @access public
 */
MPMedia.prototype.getMarkerName = function( markerNum )
{
	if ( markerNum != null && Util.is_int( markerNum ) && markerNum >= 1 )
		return this.media.getMarkerName( markerNum );
};

/**
 * Retrieves the value of the attribute with the specified index number.
 *
 * @access public
 */
MPMedia.prototype.getItemInfoByAtom = function( atom )
{
	if ( atom != null && Util.is_int( atom ) )
		return this.media.getItemInfoByAtom( atom );
};

/**
 * Retrieves the name of the attribute corresponding to the specified index.
 *
 * @access public
 */
MPMedia.prototype.getAttributeName = function( index )
{
	if ( index != null && Util.is_int( index ) )
		return this.media.getAttributeName( index );
};
