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
 * Represents a single point in 3-dimensional space.
 *
 * @access public
 */
Point3D = function( x, y, z, materialId )
{
	this.Base = Base;
	this.Base();
	
	// set point position
	this.x = x;
	this.y = y;
	this.z = z;
	
	// w coordinate
	this.w = 1;
	
	// if no material sub id has been submitted, use 0
	this.materialId = materialId? materialId : 0;
};


Point3D.prototype = new Base();
Point3D.prototype.constructor = Point3D;
Point3D.superclass = Base.prototype;

/**
 * Transforms the point with the given matrix.
 *
 * @param  Matrix  matrix  The matrix to transform the point with
 * @access public
 */
Point3D.prototype.transform = function(matrix) {
	
	// duplicates the point to calculate correctly (with old values)
	p = this.duplicate();
	
	this.x = p.x * matrix.a00 + p.y * matrix.a01 + p.z * matrix.a02 + p.w * matrix.a03;
	this.y = p.x * matrix.a10 + p.y * matrix.a11 + p.z * matrix.a12 + p.w * matrix.a13;
	this.z = p.x * matrix.a20 + p.y * matrix.a21 + p.z * matrix.a22 + p.w * matrix.a23;
	this.w = p.x * matrix.a30 + p.y * matrix.a31 + p.z * matrix.a32 + p.w * matrix.a33;
};

/**
 * Homogenizes the point.
 *
 * @access public
 */
Point3D.prototype.homogenize = function()
{	
	// if not yet homogenized
	if ( this.w != 1 )
	{
		this.x /= this.w;
		this.y /= this.w;
		this.z /= this.w;
		this.w  = 1;
	}
};

/**
 * This handler is called when the point is drawn.
 *
 * @access public
 */
Point3D.prototype.refresh = function()
{
};

/**
 * Duplicates this Point3D.
 *
 * @access public
 */
Point3D.prototype.duplicate = function()
{
	return new Point3D( this.x, this.y, this.z, this.materialId );
};

/**
 * Sets the position of this Point3D.
 *
 * @access public
 */
Point3D.prototype.setPosition = function()
{
	this.x = x;
	this.y = y;
	this.z = z;
	this.w = 1;
};

/**
 * Returns a string representation of the point.
 *
 * @access public
 */
Point3D.prototype.toString = function()
{
 	return "(" + this.x + ", " + this.y + ", " + this.z + ", " + this.w + ")";
};
