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
ClipButton = function( layerName, width, height, states, nestedRef )
{
	this.Base = Base;
	this.Base();
	
	// gets reference to ClipButton layer
	this.lyr = new LyrObj( layerName, nestedRef );
	
	// sets object variables
	this.x = this.lyr.getPos( "left" );
	this.y = this.lyr.getPos( "top"  );
	
	this.width  = width;
	this.height = height;
	
	this.states = states;
	this.state  = -1;
};


ClipButton.prototype = new Base();
ClipButton.prototype.constructor = ClipButton;
ClipButton.superclass = Base.prototype;

/**
 * @access public
 */
ClipButton.prototype.getState = function()
{
	return this.state;
};

/**
 * Sets the current state, if n is a valid button state.
 *
 * @access public
 */
ClipButton.prototype.setState = function( n )
{
	// checks boundaries
	if ( ( n < this.states ) && ( n >= 0 ) )
	{
		this.state = n;
		ay = this.state * this.height;
	  
		// adjusts the layer's clip rect
		this.lyr.setClip( 0, ay, this.width, ay + this.height );
		
		// repositions the layer to have the visible area unchanged
		// after changing the clipping
		this.lyr.setPos( "top", this.y - ay );	
	}
};

/**
 * Returns a string of the format.
 *
 * @access public
 */
ClipButton.prototype.toString = function( n )
{
	return "ClipButton '" + this.lyr.lyrname + "'\nx = " + this.x + ", y = " + this.y + "\n" + "state = " + this.state + "/" + this.states;
};
