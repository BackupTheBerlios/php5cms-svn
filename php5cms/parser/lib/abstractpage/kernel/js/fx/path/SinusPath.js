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
SinusPath = function( length, amplitude, period, phase, steps )
{
	this.Path = Path;
	this.Path();

	this.x = 0;
	this.y = 0;

	this.length      = length;
	this.currentStep = 0;
	this.amplitude   = amplitude;
	
	if ( period > 0 )
		this.period = period;
	else
		this.period = length;

	this.phase = phase;

	if ( steps <= 0 )
	{
		this.totalSteps = 0;
		this.x = this.length;
	}
	else
	{
		this.totalSteps = steps;
	}
	
	this.y = SinusPath.getY( this.currentStep, this.totalSteps, this.amplitude, this.length, this.period, this.phase );
};


SinusPath.prototype = new Path();
SinusPath.prototype.constructor = SinusPath;
SinusPath.superclass = Path.prototype;

/**
 * @access public
 */
SinusPath.prototype.step = function()
{
	if ( this.currentStep <= this.totalSteps && this.totalSteps > 0 )
	{
		this.x = this.currentStep / this.totalSteps * this.length;
		this.y = SinusPath.getY( this.currentStep, this.totalSteps, this.amplitude, this.length, this.period, this.phase );
		this.currentStep++;
		
		return true;
	}
	else
	{
		return false;
	}
};

/**
 * @access public
 */
SinusPath.prototype.reset = function()
{
	this.currentStep = 0;
	
	if ( this.totalSteps > 0 )
		this.x = 0;
	else
		this.x = this.length;
	
	this.y = SinusPath.getY( this.currentStep, this.totalSteps, this.amplitude, this.length, this.period, this.phase );
};


/**
 * @access public
 * @static
 */
SinusPath.getY = function( currentStep, totalSteps, amplitude, length, period, phase )
{
	var q;
	
	if ( totalSteps > 0 )
		q = currentStep / totalSteps;
	else
		q = 0;
	
	return amplitude * Math.sin( 2 * Math.PI * length / period * q  - phase / length * 2 * Math.PI );
};
