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
Animator = function( id, p, period )
{
	this.Base = Base;
	this.Base();

	this.onanimationdone;
	
	this.elstyle = null;	
	this.path    = p;
	this.msec    = period;
	this.id      = id;
	
	this.index = Animator.animIndex;
	Animator.animArray[this.index] = this;
	this.thisString = "Animator.animArray[" + this.index + "]";
	Animator.animIndex++;
};


Animator.prototype = new Base();
Animator.prototype.constructor = Animator;
Animator.superclass = Base.prototype;

/**
 * @access public
 */
Animator.prototype.play = function()
{
	if ( this.elstyle == null )
	{
		if ( document.all )
			this.elstyle = document.all[this.id].style;
		else if ( document.getElementById )
			this.elstyle = document.getElementById( this.id ).style;
		else if ( document.layers )
			this.elstyle = document.layers[this.id]
		else
			return;
	}
	
	if ( this.path.step() )
	{
		this.elstyle.left = this.path.x;
		this.elstyle.top  = this.path.y;
		
		Animator.animArray[this.index].timer = setTimeout( this.thisString + ".play()", this.msec );
	}
	else if ( this.onanimationdone != null )
	{
		if ( typeof( this.onanimationdone ) == "string" )
			eval( this.onanimationdone );
		else if ( typeof( this.onanimationdone ) == "function" )
			this.onanimationdone();
	}
};

/**
 * @access public
 */
Animator.prototype.pause = function()
{
	clearTimeout( Animator.animArray[this.index].timer );
};

/**
 * @access public
 */
Animator.prototype.stop = function()
{
	clearTimeout( Animator.animArray[this.index].timer );
	this.path.reset();
};


/**
 * @access public
 * @static
 */
Animator.animIndex = 0;

/**
 * @access public
 * @static
 */
Animator.animArray = new Array();
