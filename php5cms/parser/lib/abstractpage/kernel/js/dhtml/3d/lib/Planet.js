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
 * @access public
 */
Planet = function( name, material, distance, rotation )
{
	this.Base = Base;
	this.Base();
	
	this.name     = name;
	this.distance = distance;
	this.rotation = rotation;
	
	// creates new planet model
	this.model = new Model( name, material );
	this.model.setPoints( new Array( new Point3D( 0, 0, 0, 0 ) ) );
	
	// moves the planet to its position
	var myMatrix = new Matrix();
	myMatrix.translate( this.distance, 0, 0 );
	this.model.transform( myMatrix );

	// renders animation matrix
	this.rotationMatrix = new Matrix();
	this.rotationMatrix.rotateY( this.rotation );
};


Planet.prototype = new Base();
Planet.prototype.constructor = Planet;
Planet.superclass = Base.prototype;

/**
 * @access public
 */
Planet.prototype.toString = function()
{
	return this.name + " @ " + this.distance + " with " + this.rotation + ".";
};

/**
 * @access public
 */
Planet.prototype.animate = function()
{
	this.model.transform( this.rotationMatrix );
};
