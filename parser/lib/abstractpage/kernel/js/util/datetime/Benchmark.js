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
 * @package util_datetime
 */
 
/**
 * Constructor
 *
 * @access public
 */
Benchmark = function( nPauseTime ) 
{
	this.Base = Base;
	this.Base();
	
	this._pauseTime = typeof nPauseTime == "undefined"? 1000 : nPauseTime;
	this._timer     = null;
	this._isStarted = false;
};


Benchmark.prototype = new Base();
Benchmark.prototype.constructor = Benchmark;
Benchmark.superclass = Base.prototype;

/**
 * @access public
 */
Benchmark.prototype.start = function() 
{
	if ( this.isStarted() )
		this.stop();
		
	var oThis = this;
	
	this._timer = window.setTimeout( function()
	{
		if ( typeof oThis.ontimer == "function" )
			oThis.ontimer();
	}, this._pauseTime );
	
	this._isStarted = false;
};

/**
 * @access public
 */
Benchmark.prototype.stop = function()
{
	if ( this._timer != null )
		window.clearTimeout( this._timer );
		
	this._isStarted = false;
};

/**
 * @access public
 */
Benchmark.prototype.isStarted = function ()
{
	return this._isStarted;
};

/**
 * @access public
 */
Benchmark.prototype.getPauseTime = function() 
{
	return this._pauseTime;
};

/**
 * @access public
 */
Benchmark.prototype.setPauseTime = function( nPauseTime ) 
{
	this._pauseTime = nPauseTime;
};
