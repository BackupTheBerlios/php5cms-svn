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
 * Constructs a MouseModulator.
 * This modulator returns a transformation matrix depending on mouse interactions.
 * 
 * When a mouseUp event occurs the rotation will fade to null (spins the object).
 *
 * @param  string  name   The name of the variable storing this object
 *                        e.g. var myMouseModulator = new MouseModulator("myMouseModulator"); 
 * @param  int     mode   The interaction mode of the modulator, use MouseModulator.MODE_xxx
 *                        constants here. You can rotate around x and y (MODE_ROTATE), just on of them
 *                        (MODE_ROTATE_X and MODE_ROTATE_Y), or move the model (MODE_MOVE).
 *
 * @access public
 */
MouseModulator = function( name, mode )
{
	this.Base = Base;
	this.Base();
	
	// the name of the variable storing this object
	this.name = name;
	
	// interaction mode
	this.mode = mode? mode : MouseModulator.MODE_ROTATE;

	this.matrix = new Matrix();

	// mouse coordinates
	this.mouseX = 0;
	this.mouseY = 0;
	
	// to store old mouse coordinates
	this.oldX  = 0;
	this.oldY  = 0;
	this.oldX2 = 0;
	this.oldY2 = 0;
	
	// flag to show if the mouse is down
	this.isDown = false;
	
	// responsible for deceleration
	this.decSteps = 0;
	
	// spinning speed
	this.spinDX = 0;
	this.spinDY = 0;
	
	return this;
};


MouseModulator.prototype = new Base();
MouseModulator.prototype.constructor = MouseModulator;
MouseModulator.superclass = Base.prototype;

/**
 * Returns the (rotation) matrix to transform with.
 *
 * @access public
 */
MouseModulator.prototype.getMatrix = function()
{
	m = this.matrix;
	this.matrix = new Matrix();
	
	return m;
};

/**
 * Updates the modulator's matrix according to mouse movements.
 *
 * @access public
 */
MouseModulator.prototype.animate = function()
{
	// spins the model
	if ( this.decSteps > 1 )
	{
		// decreases by factor 0.9
		this.decSteps *= 0.9;
		this.spinDX   *= 0.9;
		this.spinDY   *= 0.9;
      
		this.render( this.spinDX, this.spinDY );
    }
	else
	{
		if ( this.isDown )
		{
			// resets spinning speed (the difference of two old mouse positions)
			// each animate call because there is no event mouseNotMove.
			this.oldX2 = this.oldX;
			this.oldY2 = this.oldY;
		}
	}
};

/**
 * In the render method, the modulator's matrix 
 * is computed using the current control values.
 *
 * The way the connected object can be rotated
 * and/or moved depends on the interaction mode.
 *
 * @param  int  dX  X distance amount
 * @param  int  dY  Y distance amount
 * @access public
 */
MouseModulator.prototype.render = function( dX, dY )
{
	// As Netscape 4.x cannot handle case statements
	// with variables (!), we're using plain numbers
	// instead of the respective MouseModulator.MODE_XXX constants.
	switch ( this.mode )
	{
		case 0 : 
			// Rotate and Spin
			// MouseModulator.MODE_ROTATE == 0
			var mx = new Matrix();
			mx.rotateX(dY);
			mx.rotateY(dX);
			this.matrix = mx;

			break;

		case 2 :
			// Rotate and Spin X
			// MouseModulator.MODE_ROTATE_X == 2
			mx = new Matrix();
			// correct, because changing the y coordinate gives you 
			// the amount of rotation (which is done along the x-axis)
			mx.rotateX( dY ); 
			this.matrix = mx;

			break;

		case 3 :
			// Rotate and Spin Y
			// MouseModulator.MODE_ROTATE_Y == 3
			mx = new Matrix();
			mx.rotateY( dX );
			this.matrix = mx;
			
			break;
			
		case 1 :
			// Move and Slide
			// MouseModulator.MODE_MOVE == 1
			mt = new Matrix();
			mt.translate( dX * 20, -dY * 20, 0 );
			this.matrix = mt ;
			
			break;
		
		default :
	}
};

/**
 * @access public
 */
MouseModulator.prototype.up = function( e )
{
	// gets the speed (the difference of two old mouse positions)
	// at the moment of mouseUp
	this.decSteps = 100;
	this.spinDX   = ( this.oldX  - this.oldX2 ) / 20;
	this.spinDY   = ( this.oldY2 - this.oldY  ) / 20;
	
	// starts spinning
	this.animate();
	
    // stores old coordinates (mouseX and mouseY are set in mouseMove() )
	this.oldX = this.mouseX;
    this.oldY = this.mouseY;

	// to prohibit moveHandler to render
    this.isDown = false;
};

/**
 * @access public
 */
MouseModulator.prototype.down = function( e )
{
	if ( Browser.ns4 || Browser.ie || Browser.ns6 )
	{
	    this.mouseX = ( Browser.ns4 || Browser.ns6 )? e.pageX : event.x;
	    this.mouseY = ( Browser.ns4 || Browser.ns6 )? e.pageY : event.y;
	}
	
    // stores old coordinates
	this.oldX = this.mouseX;
    this.oldY = this.mouseY;
    
	this.decSteps = 0;

	// to allow moveHandler to render
    this.isDown = true;
};

/**
 * @access public
 */
MouseModulator.prototype.move = function( e )
{
	// just calculates new phi if mouse is down
	if ( this.isDown )
	{
		// gets mouse coordinates
		if ( Browser.ns4 || Browser.ie || Browser.ns6 )
		{
			this.mouseX = ( Browser.ns4 || Browser.ns6 )? e.pageX : event.x;
			this.mouseY = ( Browser.ns4 || Browser.ns6 )? e.pageY : event.y;
		}

		// calculates phi (rotation degree)
		var dX = ( ( this.mouseX - this.oldX   ) / 20 ) // Math.max( Math.min( ( ( mouseX - oldX   ) / 20 ), 0.1 ), -0.1 );
		var dY = ( ( this.oldY   - this.mouseY ) / 20 ) // Math.max( Math.min( ( ( oldY   - mouseY ) / 20 ), 0.1 ), -0.1 );
		
		// stores the old coordinates (see MouseModulatorUp)
		this.oldX2 = this.oldX;
		this.oldY2 = this.oldY;
		
		// stores the new coordinates
		this.oldX = this.mouseX;
		this.oldY = this.mouseY;
		
		// pings render method
		setTimeout( this.name + ".render(" + dX + "," + dY + ")", 1 );
		// this.render( dX, dY );
	}
};


/**
 * @access public
 * @static
 */
MouseModulator.MODE_ROTATE = 0;

/**
 * @access public
 * @static
 */
MouseModulator.MODE_MOVE = 1;

/**
 * @access public
 * @static
 */
MouseModulator.MODE_ROTATE_X = 2;

/**
 * @access public
 * @static
 */
MouseModulator.MODE_ROTATE_Y = 3;
