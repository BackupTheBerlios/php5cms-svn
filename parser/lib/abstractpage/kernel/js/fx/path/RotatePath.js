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
RotatePath = function( p, xc, yc, v )
{
	this.Path = Path;
	this.Path();

	this.x = 0;
	this.y = 0;

	this._p  = p;
	this._xc = xc;
	this._yc = yc;
	this._v  = v;
};


RotatePath.prototype = new Path();
RotatePath.prototype.constructor = RotatePath;
RotatePath.superclass = Path.prototype;

/**
 * @access public
 */
RotatePath.prototype.step = function()
{
	var c = this._p.step();

	var pol = RotatePath.toPol( this._p.x - this._xc, this._p.y - this._yc );
	var rec = RotatePath.toRec( pol.r, pol.v + RotatePath.toRad( this._v ) );

	this.x = rec.x + this._xc;
	this.y = rec.y + this._yc;

	return c;
};

/**
 * @access public
 */
RotatePath.prototype.reset = function()
{
	this._p.reset();
	var pol = RotatePath.toPol( this._p.x - this._xc, this._p.y - this._yc );
	var rec = RotatePath.toRec( pol.r, pol.v + RotatePath.toRad( this._v ) );

	this.x = rec.x - this._xc;
	this.y = rec.y - this._yc;
};


/**
 * @access public
 * @static
 */
RotatePath.toPol = function( x, y )
{
	var o = new Object();
	o.r = Math.sqrt( x * x + y * y );
	
	if ( x == 0 )
		o.v = Math.PI / 2;
	else
		o.v = Math.atan( y / x );
	
	if ( x < 0 )
		o.v = o.v + Math.PI;
	
	return o;
};	

/**
 * @access public
 * @static
 */
RotatePath.toRec = function( r, v )
{
	var o = new Object();
	o.x = r * Math.cos( v );
	o.y = r * Math.sin( v );
	
	return o;
};

/**
 * @access public
 * @static
 */
RotatePath.toRad = function( deg )
{
	return deg * Math.PI / 180;
};
