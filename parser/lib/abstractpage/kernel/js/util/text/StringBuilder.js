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
 * @package util_text
 */
 
/**
 * Constructor
 *
 * @access public
 */
StringBuilder = function( sString )
{
	this.Base = Base;
	this.Base();
	
	this.length = 0;

	this._current = 0;
	this._parts   = [];
	
	// used to cache the string
	this._string  = null;
	
	if ( sString != null )
		this.append( sString );
};


StringBuilder.prototype = new Base();
StringBuilder.prototype.constructor = StringBuilder;
StringBuilder.superclass = Base.prototype;

/**
 * @access public
 */
StringBuilder.prototype.append = function( sString )
{
	// append argument
	this.length += ( this._parts[this._current++] = String( sString ) ).length;
		
	// reset cache
	this._string = null;
	return this;
};

/**
 * @access public
 */
StringBuilder.prototype.toString = function()
{
	if ( this._string != null )
		return this._string;
		
	return this._string = this._parts.join( "" );
};
