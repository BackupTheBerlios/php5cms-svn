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
 * Constructs a 4x4 Matrix you will need to transform points.
 * Preconfigured as identity matrix, i.e. 
 *   1 0 0 0
 *   0 1 0 0
 *   0 0 1 0
 *   0 0 0 1
 *
 * @access public
 */
Matrix = function()
{
	this.Base = Base;
	this.Base();
	
	// sets identity matrix
	this.a01 = this.a02 = this.a03 = 0;
	this.a10 = this.a12 = this.a13 = 0;
	this.a20 = this.a21 = this.a23 = 0;
	this.a30 = this.a31 = this.a32 = 0;
	this.a00 = this.a11 = this.a22 = this.a33 = 1;
};


Matrix.prototype = new Base();
Matrix.prototype.constructor = Matrix;
Matrix.superclass = Base.prototype;

/**
 * Rotates along the x-axis with phi degree.
 *
 * @access public
 */
Matrix.prototype.rotateX = function( phi )
{
	var myMatrix = new Matrix();

	myMatrix.a00 = 1;
	myMatrix.a01 = myMatrix.a02 = myMatrix.a03 = 0;
	myMatrix.a10 = 0;
	myMatrix.a11 = Math.cos( phi );
	myMatrix.a12 = -Math.sin( phi );
	myMatrix.a13 = 0;
	myMatrix.a20 = 0;
	myMatrix.a21 = Math.sin( phi );
	myMatrix.a22 = Math.cos( phi );
	myMatrix.a23 = 0;
	myMatrix.a30 = myMatrix.a31 = myMatrix.a32 = 0;
	myMatrix.a33 = 1;

	myMatrix.compose( this );
	this.setMatrixValues( myMatrix );
};

/**
 * Rotates along the y-axis with phi degree.
 *
 * @access public
 */
Matrix.prototype.rotateY = function( phi )
{
	var myMatrix = new Matrix();

	myMatrix.a00 = Math.cos( phi );
	myMatrix.a01 = 0;
	myMatrix.a02 = Math.sin( phi );
	myMatrix.a03 = 0;
	myMatrix.a10 = myMatrix.a13 = myMatrix.a12 = 0;
	myMatrix.a11 = 1;
	myMatrix.a20 = -Math.sin( phi );
	myMatrix.a21 = 0;
	myMatrix.a22 = Math.cos( phi );
	myMatrix.a23 = 0;
	myMatrix.a30 = myMatrix.a31 = myMatrix.a32 = 0;
	myMatrix.a33 = 1;
	
	myMatrix.compose( this );
	this.setMatrixValues( myMatrix );
};

/**
 * Rotates along the z-axis with phi degree.
 *
 * @access public
 */
Matrix.prototype.rotateZ = function( phi )
{
	var myMatrix = new Matrix();

	myMatrix.a00 = Math.cos( phi );
	myMatrix.a01 = -Math.sin( phi );
	myMatrix.a02 = 0;
	myMatrix.a03 = 0;
	myMatrix.a10 = Math.sin( phi );
	myMatrix.a11 = Math.cos( phi );
	myMatrix.a12 = 0;
	myMatrix.a13 = 0;
	myMatrix.a20 = myMatrix.a21 = 0;
	myMatrix.a22 = 1;
	myMatrix.a23 = 0;
	myMatrix.a30 = myMatrix.a31 = myMatrix.a32 = 0;
	myMatrix.a33 = 1;
	
	myMatrix.compose( this );
	this.setMatrixValues( myMatrix );
};

/**
 * Scale with the scale factors.
 * 
 * @param  float  sx  The x scale factor
 * @param  float  sy  The y scale factor
 * @param  float  sz  The z scale factor
 *
 * @access public
 */
Matrix.prototype.scale = function( sx, sy, sz )
{
	var myMatrix = new Matrix();

	myMatrix.a00 = sx;
	myMatrix.a01 = myMatrix.a02 = myMatrix.a03 = 0;
	myMatrix.a10 = 0;
	myMatrix.a11 = sy;
	myMatrix.a12 = myMatrix.a13 = 0;
	myMatrix.a20 = myMatrix.a21 = 0;
	myMatrix.a22 = sz;
	myMatrix.a23 = 0;
	myMatrix.a30 = myMatrix.a31 = myMatrix.a32 = 0;
	myMatrix.a33 = 1;
	
	myMatrix.compose( this );
	this.setMatrixValues( myMatrix );
};

/**
 * Translate with the translation values.
 * 
 * @param  float  dx   The x value to translate with
 * @param  float  dy   The y value to translate with
 * @param  float  dz   The z value to translate with
 * @access public
 */
Matrix.prototype.translate = function( dx, dy, dz )
{
	var myMatrix = new Matrix();

	myMatrix.a00 = 1;
	myMatrix.a01 = myMatrix.a02 = 0;
	myMatrix.a03 = dx;
	myMatrix.a10 = 0;
	myMatrix.a11 = 1;
	myMatrix.a12 = 0;
	myMatrix.a13 = dy;
	myMatrix.a20 = myMatrix.a21 = 0;
	myMatrix.a22 = 1;
	myMatrix.a23 = dz;
	myMatrix.a30 = myMatrix.a31 = myMatrix.a32 = 0;
	myMatrix.a33 = 1;
	
	myMatrix.compose( this );
	this.setMatrixValues( myMatrix );
};

/**
 * Composes this matrix with the given matrix m.
 *
 * @access public
 */
Matrix.prototype.compose = function( m )
{
	var r, c;
	var myMatrix = new Matrix();
  
	// matrices multiplication
	for ( r = 0; r < 4; r++ )
	{
		for ( c = 0; c < 4; c++ )
		{
			myMatrix["a" + r + c] = this["a" + r + "0"] * m["a0" + c] 
				+ this["a" + r + "1"] * m["a1" + c]
				+ this["a" + r + "2"] * m["a2" + c]
				+ this["a" + r + "3"] * m["a3" + c];
		}
	}
	
	// copies the new matrix to this
	for ( r = 0; r < 4; r++ )
	{
		for ( c = 0; c < 4; c++ )
			this["a" + r + c] = myMatrix["a" + r + c];
	}
};

/**
 * Sets the values of this matrix to the values of the sourceMatrix.
 *
 * @access public
 */
Matrix.prototype.setMatrixValues = function( sourceMatrix )
{
	with ( sourceMatrix )
	{
		this.a00 = a00; this.a01 = a01; this.a02 = a02; this.a03 = a03;
		this.a10 = a10; this.a11 = a11; this.a12 = a12; this.a13 = a13;
		this.a20 = a20; this.a21 = a21; this.a22 = a22; this.a23 = a23;
		this.a30 = a30; this.a31 = a31; this.a32 = a32; this.a33 = a33;
	}
};

/**
 * Copies the values of this matrix to a new one and returns it.
 *
 * @access public
 */
Matrix.prototype.getCopy = function()
{
	var destMatrix = new Matrix();
	
	with ( destMatrix )
	{
		a00 = this.a00; a01 = this.a01; a02 = this.a02; a03 = this.a03;
		a10 = this.a10; a11 = this.a11; a12 = this.a12; a13 = this.a13;
		a20 = this.a20; a21 = this.a21; a22 = this.a22; a23 = this.a23;
		a30 = this.a30; a31 = this.a31; a32 = this.a32; a33 = this.a33;
	}
	
	return destMatrix;	
};

/**
 * Returns a string representation of the matrix
 *
 * Returns 
 *   a string representation of the Matrix object, 
 *   e.g. 
 *    1 0 0 0
 *    0 1 0 0
 *    0 0 1 0
 *    0 0 0 1
 *
 * @access public
 */
Matrix.prototype.toString = function()
{
	var tab = "\t";
	
	return
		this.a00 + tab + this.a01 + tab + this.a02 + tab + this.a03 + "\n" +
		this.a10 + tab + this.a11 + tab + this.a12 + tab + this.a13 + "\n" +
		this.a20 + tab + this.a21 + tab + this.a22 + tab + this.a23 + "\n" + 
		this.a30 + tab + this.a31 + tab + this.a32 + tab + this.a33 + "\n";
};
