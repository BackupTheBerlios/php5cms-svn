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
 * @package util_array
 */
 
/**
 * Constructor
 *
 * @access public
 */
ArrayEnumerator = function( arr )
{
	this.Base = Base;
	this.Base();
	
	this.array = ( arr != null )? arr : new Array();
	this.arrayCount  = 0;
};


ArrayEnumerator.prototype = new Base();
ArrayEnumerator.prototype.constructor = ArrayEnumerator;
ArrayEnumerator.superclass = Base.prototype;

/**
 * @access public
 */
ArrayEnumerator.prototype.reset = function()
{
	this.arrayCount = 0;
};

/**
 * @access public
 */
ArrayEnumerator.prototype.hasMore = function()
{
	if ( this.arrayCount == this.array.length )
		return false;

	return true;
};

/**
 * @access public
 */
ArrayEnumerator.prototype.next = function()
{
	return this.array[this.arrayCount++];
};

/**
 * @access public
 */
ArrayEnumerator.prototype.count = function()
{
	return this.array.length;
};
