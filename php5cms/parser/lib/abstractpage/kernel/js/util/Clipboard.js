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
 * Clipboard Class
 * Just a little wrapper for IE Clipboard access.
 *
 * @package util
 */

/**
 * Constructor
 *
 * @access public
 */
Clipboard = function()
{
	this.Base = Base;
	this.Base();
};


Clipboard.prototype = new Base();
Clipboard.prototype.constructor = Clipboard;
Clipboard.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
Clipboard.set = function( format, data )
{
	if ( format == null || data == null )
		return false;
	
	window.clipboardData.setData( format, data );
};

/**
 * @access public
 * @static
 */
Clipboard.get = function( format )
{
	// event.returnValue = false;
	return window.clipboardData.getData( format || "Text" );
};

/**
 * @access public
 * @static
 */
Clipboard.setFromSelection = function()
{
	objSelection = document.selection.createRange();
	Clipboard.set( "Text", objSelection.text );
};

/**
 * @access public
 * @static
 */
Clipboard.hasData = function( format )
{
	// event.returnValue = false;
	return window.clipboardData.getData( format || "Text" )? true : false;
};

/**
 * @access public
 * @static
 */
Clipboard.free = function( format )
{
	window.clipboardData.clearData( format || "Text" );
};
