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
HeartBeatModulator = function( maxFrameNumber )
{
	this.Base = Base;
	this.Base();
	
	// sets the properties
	this.frameNumber    = 0;
	this.maxFrameNumber = maxFrameNumber? maxFrameNumber : HeartBeatModulator.MAX_FRAME_NUMBER;
	this.matrix         = new Matrix();
	
	// calulates and stores the values
	this.values = new Array();
	
	var t = 0;
	var dt = ( 2 * Math.PI ) / this.maxFrameNumber;
	
	// renders only first values [0 - n/2] (sin is symmetric funtion)
	for ( i = 0; i <= ( this.maxFrameNumber / 2 ); i++ )
	{
		t += dt;
		this.values[i] = ( Math.sin( t ) * 0.1 + 1.0 );
	}
	
	// renders second values [n/2 - n]
	for ( i = 0; i <= ( this.maxFrameNumber / 2 ); i++ )
	{
		// renders reciprocal values
		this.values[i + Math.round( ( this.maxFrameNumber / 2 ) )] = 1 / this.values[i];
	}
	
	return this;
};


HeartBeatModulator.prototype = new Base();
HeartBeatModulator.prototype.constructor = HeartBeatModulator;
HeartBeatModulator.superclass = Base.prototype;


/**
 * Returns the (scaling) matrix to transform with.
 *
 * @access public
 */
HeartBeatModulator.prototype.getMatrix = function()
{
	return this.matrix;
};

/**
 * @access public
 */
HeartBeatModulator.prototype.animate = function()
{
	// sets current frameNumber to go through the values
	if ( this.frameNumber < this.maxFrameNumber ) 
		this.frameNumber++;
	else 
		this.frameNumber = 0;

	// gets scale value from prerendered array
	var value = HeartBeatModulator.TEST_VALUES[this.frameNumber];
		
	// sets scale matrix
	this.matrix = new Matrix();
	this.matrix.scale( value, value, value );
};


/**
 * @access public
 * @static
 */
HeartBeatModulator.MAX_FRAME_NUMBER = 15;

/**
 * @access public
 * @static
 */
HeartBeatModulator.TEST_VALUES = new Array(
	1.05, 
	1.05, 
	1.05, 
	1.05, 
	1.05, 
	1.05, 
	1.05, 
	1.05, 
	1 / 1.05, 
	1 / 1.05, 
	1 / 1.05, 
	1 / 1.05, 
	1 / 1.05, 
	1 / 1.05, 
	1 / 1.05, 
	1 / 1.05 
);
