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
StraightPath = function( fromX, fromY, toX, toY, n )
{
	this.Path = Path;
	this.Path();
	
	this.x = fromX;
	this.y = fromY;

	this.startX = fromX;
	this.startY = fromY;
	this.endX   = toX;
	this.endY   = toY;

	// Initiate steps.
 	this.steps = n;
 	this.totalSteps = n;
 	
	// No Amimation!
	if ( this.totalSteps < 1 )
	{
 		this.x = this.endX;
 		this.y = this.endY;
		
 		this.deltaX = 0; // NN work around
 		this.deltaY = 0;
 	}
	else
	{
	 	this.deltaX = ( this.endX - this.startX ) / this.totalSteps;
		this.deltaY = ( this.endY - this.startY ) / this.totalSteps;
	}
};


StraightPath.prototype = new Path();
StraightPath.prototype.constructor = StraightPath;
StraightPath.superclass = Path.prototype;

/**
 * @access public
 */
StraightPath.prototype.step = function()
{
	if ( this.steps >= 0 )
	{
		this.steps--;
		
		this.x += this.deltaX;
		this.y += this.deltaY;
	}
	
	return ( this.steps >= 0 );
};

/**
 * @access public
 */
StraightPath.prototype.reset = function()
{
	if ( this.totalSteps < 1 )
	{
		this.steps = 0;
		
		this.x = this.endX;
		this.y = this.endY;
	}
	else
	{
		this.steps = this.totalSteps;

		this.x = this.startX;
		this.y = this.startY;
	}
};
