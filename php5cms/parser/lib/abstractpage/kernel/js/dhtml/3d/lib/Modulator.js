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
 * @package dhtml_3d_lib
 */
 
/**
 * Constructor
 *
 * This is basically an object encapsulating a function of some sort. It can
 * be used to influence parameters such as the scaling of a model, its movement
 * or the color of a point.
 *
 * @access public
 */

Modulator = function()
{
	this.Base = Base;
	this.Base();
	
	this.matrix = new Matrix();
	
	this.getMatrix = ModulatorGetMatrix;
	this.animate   = ModulatorAnimate;
};


Modulator.prototype = new Base();
Modulator.prototype.constructor = Modulator;
Modulator.superclass = Base.prototype;

/**
 * @access public
 */
Modulator.prototype.getMatrix = function()
{
	return this.matrix;
};

/**
 * @access public
 */
Modulator.prototype.animate = function()
{
};
