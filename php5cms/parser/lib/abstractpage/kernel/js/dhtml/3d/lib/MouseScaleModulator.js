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
MouseScaleModulator = function( name, mode )
{
	this.Base = Base;
	this.Base();
	
	// the name of the variable storing this object
	this.name = name;
	
	// interaction mode
	this.mode = mode? mode : MouseScaleModulator.MODE_NONUNIFORM;

	// the matrix to transform with
	this.matrix = new Matrix();
	
	this.scaleValues = MouseScaleModulator.SCALE_VALUES;

	// flag to show if the mouse is down
	this.isDown = false;

	// responsible for scaling
	this.scalingSteps = 0;
	
	return this;
};


MouseScaleModulator.prototype = new Base();
MouseScaleModulator.prototype.constructor = MouseScaleModulator;
MouseScaleModulator.superclass = Base.prototype;

/**
 * Returns the (rotation) matrix to transform with.
 *
 * @access public
 */
MouseScaleModulator.prototype.getMatrix = function()
{
	return this.matrix;
};

/**
 * Does nothing. This MouseModulator has no mouse independent rendering.
 *
 * @access public
 */
MouseScaleModulator.prototype.animate = function()
{
	if ( this.isDown )
	{
		if ( this.scalingSteps <= 5 )
		{
			var stretchMatrix = new Matrix();
			var sv = this.scaleValues[this.scalingSteps];
			
			if ( this.mode == MouseScaleModulator.MODE_NONUNIFORM )
				stretchMatrix.scale( 1, 1, sv );   // non uniform
			else
				stretchMatrix.scale( sv, sv, sv ); // uniform

			this.matrix = stretchMatrix;
			this.scalingSteps++;
		}
		else
		{
			this.matrix = new Matrix();
		}
	}
	else
	{
		if ( this.scalingSteps > 0 )
		{
			this.scalingSteps--;

			var stretchMatrix = new Matrix();
			var sv = this.scaleValues[this.scalingSteps];

			if ( this.mode == MouseScaleModulator.MODE_NONUNIFORM )
				stretchMatrix.scale( 1, 1, 1 / sv ); // non uniform
			else
				stretchMatrix.scale( 1 / sv, 1 / sv, 1 / sv ); // uniform

			this.matrix = stretchMatrix;
		}
		else
		{
			this.matrix = new Matrix();
		}
	}
};

/**
 * The mouseUp eventhandler. Pass the event object e when calling
 * this method in the page's event handler.
 *
 * @access public
 */
MouseScaleModulator.prototype.up = function( e )
{
	// to prohibit animate to render
    this.isDown = false;
};

/**
 * The mouseDown eventhandler. Pass the event object e when calling
 * this method in the page's event handler.
 *
 * @access public
 */
MouseScaleModulator.prototype.down = function( e )
{
	// to allow animate to render
    this.isDown = true;
};

/**
 * Does nothing. This MouseModulator has no mousemove dependent rendering.
 *
 * @access public
 */
MouseScaleModulator.prototype.move = function( e )
{
	// does nothing
};


/**
 * @access public
 * @static
 */
MouseScaleModulator.MODE_NONUNIFORM = 0;

/**
 * @access public
 * @static
 */
MouseScaleModulator.MODE_UNIFORM = 1;

/**
 * @access public
 * @static
 */
MouseScaleModulator.SCALE_VALUES = new Array( 1.1, 1.1, 1.1, 1.1, 1.05, 1.03 );
