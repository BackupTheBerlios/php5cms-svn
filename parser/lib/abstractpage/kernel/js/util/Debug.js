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
Debug = function( mode, active )
{
	this.Base = Base;
	this.Base();
	
	this.active = active || false;
	this.queue  = new Array();
	
	this.setMode( mode || 'alert' );
};


Debug.prototype = new Base();
Debug.prototype.constructor = Debug;
Debug.superclass = Base.prototype;

/**
 * @access public
 */
Debug.prototype.setMode = function( mode )
{
	if ( ( mode != null ) && ( ( mode == 'alert' ) || ( mode == 'status' ) || ( mode == 'queue' ) ) )
	{
		this.mode = mode;
		return this.getMode();
	}
	
	return false;
};

/**
 * @access public
 */
Debug.prototype.getMode = function()
{
	return this.mode;
};

/**
 * @access public
 */
Debug.prototype.on = function()
{
	this.active = true;
};

/**
 * @access public
 */
Debug.prototype.off = function()
{
	this.active = false;
};

/**
 * @access public
 */
Debug.prototype.message = function( msg )
{
	if ( !this.active || ( msg == null ) )
		return;
		
	switch( this.mode )
	{
		case 'alert':
			alert( msg );
			break;
			
		case 'status':
			window.status = msg;
			break;
		
		case 'queue':
			this.queue[this.queue.length] = msg;
	}
};

/**
 * @access public
 */
Debug.prototype.getQueue = function()
{
	return this.queue;
};

/**
 * @access public
 */
Debug.prototype.flushQueue = function()
{
	this.queue.length = 0;
};

/**
 * @access public
 */
Debug.prototype.hasQueue = function()
{
	if ( this.queue.length > 0 )
		return true;
	else
		return false;
};

/**
 * @access public
 */
Debug.prototype.dumpQueue = function()
{
	var str = "Queue dump:\n\n";
	
	for ( var i in this.queue )
		str += this.queue[i] + "\n";
		
	return this.hasQueue()? str : ""; 
};

/*
Debug.showConsole = function()
{
};
*/
