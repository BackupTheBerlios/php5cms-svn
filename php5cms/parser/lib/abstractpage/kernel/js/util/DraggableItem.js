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
 * @package util
 */
 
/**
 * Constructor
 *
 * @access public
 */
DraggableItem = function( hash )
{
	this.Base = Base;
	this.Base();
	
	this.item = hash || new Dictionary();

	// this.addValue( "id", "" );
	// this.addValue( "reqcontext", "" );	
	// this.addValue( "desc", "" );	
};


DraggableItem.prototype = new Base();
DraggableItem.prototype.constructor = DraggableItem;
DraggableItem.superclass = Base.prototype;

/**
 * @access public
 */
DraggableItem.prototype.getItem = function()
{
	return this.item;
};

/**
 * @access public
 */
DraggableItem.prototype.feed = function( hash )
{
	if ( hash == -1 )
		return false;
		
	this.item = hash;
};

/**
 * @access public
 */
DraggableItem.prototype.addValue = function( key, val )
{
	this.item.add( key, val );
};

/**
 * @access public
 */
DraggableItem.prototype.getValue = function( val )
{
	return this.item.get( val );
};
