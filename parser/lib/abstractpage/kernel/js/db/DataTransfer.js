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
 * DataTransfer
 * Just a little wrapper for IE Data Transfer
 * Effects: move, copy, link
 *
 * @package db
 */

/**
 * Constructor
 *
 * @access public
 */
DataTransfer = function()
{
	this.Base = Base;
	this.Base();
};


DataTransfer.prototype = new Base();
DataTransfer.prototype.constructor = DataTransfer;
DataTransfer.superclass = Base.prototype;

/**
 * Sets the data format and provides the data.
 *
 * @access public
 * @static
 */
DataTransfer.set = function( format, data, effect )
{
	if ( format == null || data == null )
		return false;
	
	event.dataTransfer.setData( format, data );
	event.dataTransfer.effectAllowed = effect || "copy";
};

/**
 * Called by the target object in the ondrop event. It cancels the
 * default action and sets the cursor to the system icon. Then it
 * specifies the data format to retrieve.
 *
 * @access public
 * @static
 */
DataTransfer.get = function( format, effect )
{
	if ( format == null )
		return false;
	
	event.returnValue = false;
	event.dataTransfer.dropEffect = effect || "copy";
	
	return event.dataTransfer.getData( format );
};

/**
 * Cancels the default action in ondragenter and ondragover so that
 * the copy cursor is displayed until the selection is dropped.
 *
 * @access public
 * @static
 */
DataTransfer.cancelDefault = function( effect )
{
	event.returnValue = false;
	event.dataTransfer.dropEffect = effect || "copy";
};

/**
 * @access public
 * @static
 */
DataTransfer.free = function( format )
{
	if ( format == null )
		return false;
		
	event.dataTransfer.clearData( format );
};
