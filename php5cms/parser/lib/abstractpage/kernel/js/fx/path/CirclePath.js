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
CirclePath = function( x, y, _xr, _yr, fromV, toV, n )
{
	this.Path = Path;
	this.Path();

	this.x = 0;
	this.y = 0;

	// NN workaround. NN can't handle local variables!
	this.steps     = n;
	this.stepsLeft = n;
	this.xp        = x;
	this.yp        = y;
	this.v         = -CirclePath.toRad( fromV );
	this.startV    = this.v;
	this.endV      = -CirclePath.toRad( toV );
	this.xr        = _xr;
	this.yr        = _yr;
	
	this.x = CirclePath.getX( this.xp, this.xr, this.v );
	this.y = CirclePath.getY( this.yp, this.yr, this.v );

	// initate steps
	if ( this.steps > 0 )
	{
		this.deltaV = ( this.endV - this.startV ) / n;
	}
	else
	{
		this.deltaV = 0;
		
		this.x = CirclePath.getX( this.xp, this.xr, this.endV );
		this.y = CirclePath.getY( this.yp, this.yr, this.endV );
	}
};


CirclePath.prototype = new Path();
CirclePath.prototype.constructor = CirclePath;
CirclePath.superclass = Path.prototype;

/**
 * @access public
 */
CirclePath.prototype.step = function()
{
	if ( this.stepsLeft > 0 )
	{
		this.v += this.deltaV;
		this.x  = CirclePath.getX( this.xp, this.xr, this.v );
		this.y  = CirclePath.getY( this.yp, this.yr, this.v );
		
		this.stepsLeft--;
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
CirclePath.prototype.reset = function()
{
	if ( this.steps < 1 )
	{
		this.x = CirclePath.getX( this.xp, this.xr, this.endV );
		this.y = CirclePath.getY( this.yp, this.yr, this.endV );
	}
	else
	{
		this.v = this.startV;
		this.x = CirclePath.getX( this.xp, this.xr, this.v );
		this.y = CirclePath.getY( this.yp, this.yr, this.v );
		
		this.stepsLeft = this.steps;
	}
};


/**
 * @access public
 * @static
 */
CirclePath.toRad = function( deg )
{
	return deg * Math.PI / 180;
};

/**
 * @access public
 * @static
 */
CirclePath.getX = function( xp, xr, v )
{
	return xp + xr * Math.cos( v );
};

/**
 * @access public
 * @static
 */
CirclePath.getY = function( yp, yr, v )
{
	return yp + yr * Math.sin( v );
};
	