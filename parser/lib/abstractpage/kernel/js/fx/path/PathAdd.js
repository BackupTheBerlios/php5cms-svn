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
 * @package fx_path
 */
 
/**
 * Constructor
 *
 * @access public
 */
PathAdd = function( p1, p2 )
{
	this.Path = Path;
	this.Path();

	this.x = 0;
	this.y = 0;

	this._p1 = p1;
	this._p2 = p2;
};


PathAdd.prototype = new Path();
PathAdd.prototype.constructor = PathAdd;
PathAdd.superclass = Path.prototype;

/**
 * @access public
 */
PathAdd.prototype.step = function()
{
	var c1 = this._p1.step();
	var c2 = this._p2.step();

	this.x = this._p1.x + this._p2.x;
	this.y = this._p1.y + this._p2.y;

	return ( c1 || c2 );
};

/**
 * @access public
 */
PathAdd.prototype.reset = function()
{
	this._p1.reset();
	this._p2.reset();

	this.x = this._p1.x + this._p2.x;
	this.y = this._p1.y + this._p1.y;
};
