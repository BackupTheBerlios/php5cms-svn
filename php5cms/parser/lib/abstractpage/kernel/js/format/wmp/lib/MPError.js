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
MPError = function( error )
{
	this.Base = Base;
	this.Base();
	
	this.error = error;
	this.onClearQueue = new Function;
};


MPError.prototype = new Base();
MPError.prototype.constructor = MPError;
MPError.superclass = Base.prototype;

/**
 * Retrieves the number of errors in the error queue.
 *
 * @access public
 */
MPError.prototype.getCount = function()
{
	return this.error.errorCount;
};

/**
 * Launches Microsoft Media Player help page.
 *
 * @access public
 */
MPError.prototype.webHelp = function()
{
	this.error.webHelp();
};

/**
 * Retrieves MPErrorItem object from the error queue.
 *
 * @access public
 */
MPError.prototype.getItem = function( index )
{
	if ( index != null && Util.is_int( index ) && index <= this.getCount() )
		return this.error.item( index );
};

/**
 * Clears the erros from the error queue.
 *
 * @access public
 */
MPError.prototype.clearQueue = function()
{
	this.error.clearErrorQueue();
	this.onClearQueue();
};
