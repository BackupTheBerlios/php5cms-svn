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
MPStringCollection = function( stringcollection )
{
	this.Base = Base;
	this.Base();
	
	this.stringcollection = stringcollection;
};


MPStringCollection.prototype = new Base();
MPStringCollection.prototype.constructor = MPStringCollection;
MPStringCollection.superclass = Base.prototype;

/**
 * Retrieves the number of items in the string collection.
 *
 * @access public
 */
MPCDRomCollection.prototype.getCount = function()
{
	return this.stringcollection.count;
};

/**
 * Retrieves the string at the given index.
 *
 * @access public
 */
MPCDRomCollection.prototype.getItem = function( index )
{
	if ( index != null && Util.is_int( index ) && index <= this.getCount() )
		return this.stringcollection.item( index );
};
