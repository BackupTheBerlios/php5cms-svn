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
 * @package util
 */
 
/**
 * Constructor
 *
 * @access public
 */
Thread = function()
{
	this.Base = Base;
	this.Base();
	
	Thread.register( this );
};


Thread.prototype = new Base();
Thread.prototype.constructor = Thread;
Thread.superclass = Base.prototype;

/**
 * @access public
 */
Thread.prototype.interval = 50;

/**
 * @access public
 */
Thread.prototype.active = false;

/**
 * @access public
 */
Thread.prototype.cancelThread = false;

/**
 * @access public
 */
Thread.prototype.sleep = function( ms )
{
	this.interval = Math.abs( parseInt( ms ) );

	if ( this.active )
	{
		this.stop();
		setTimeout( this + '.start()', this.interval + 1 );
	}
};

/**
 * @access public
 */
Thread.prototype.setFPS = function( fps )
{
	this.sleep( Math.floor( 1000 / fps ) );
};

/**
 * @access public
 */
Thread.prototype.cancel = function()
{
	this.cancelThread = true;
	this.stop();
};

/**
 * @access public
 */
Thread.prototype.start = function()
{
	if ( !this.active )
	{
		this.active = true;
		
		if ( !this.cancelThread )
			this.timer = setInterval( this + '.run()', this.interval );
	}
};

/**
 * @access public
 */
Thread.prototype.run = function()
{
	// overload
};

/**
 * @access public
 */
Thread.prototype.stop = function()
{
	this.active = false;
	
	if ( !this.cancelThread && this.timer )
	{
		clearInterval( this.timer );
		delete this.timer;
	}
};


/**
 * @access public
 * @static
 */
Thread.collection = new Array();

/**
 * @access public
 * @static
 */
Thread.register = function( thread )
{
	Thread.collection[Thread.collection.length] = thread;
};

/**
 * @access public
 * @static
 */
Thread.startAll = function()
{
	for ( var i in Thread.collection )
		Thread.collection[i].start();
};

/**
 * @access public
 * @static
 */
Thread.stopAll = function()
{
	for ( var i in Thread.collection )
		Thread.collection[i].stop();
};
