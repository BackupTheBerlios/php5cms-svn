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
Stack = function()
{
	this.Base = Base;
	this.Base();
	
	this.stack = new Array();
};


Stack.prototype = new Base();
Stack.prototype.constructor = Stack;
Stack.superclass = Base.prototype;

/**
 * @access public
 */
Stack.prototype.dump = function( comment )
{
	var i;
	var s = "";
	var j = 0;
	
	if ( arguments.length == 1 )
		s += comment + "\n\n";
	
	if ( this.isEmpty() )
	{
		s += "<emtpy>";
	}
	else
	{
		for ( i = this.stack.length - 1; i >= 0; i-- )
		{
			s += j + ": " + this.stack[i];
			
			if ( j == 0 )
				s += " <--";
			
			s += "\n";
			j++;
		}
	}
	
	return s;
};

/**
 * @access public
 */
Stack.prototype.isEmpty = function()
{
	return ( this.stack.length == 0 );
};

/**
 * @access public
 */
Stack.prototype.modifyTop = function( o )
{
	if ( !this.isEmpty() )
		this.stack[this.stack.length-1] = o;
};

/**
 * @access public
 */
Stack.prototype.pop = function()
{
	if ( !this.isEmpty() )
	{
		var o = this.stack[this.stack.length-1];
		this.stack.length--;
		
		return o;
	}
	else
	{
		return null;
	}
};

/**
 * @access public
 */
Stack.prototype.push = function( o )
{
	this.stack[this.stack.length] = o;
};

/**
 * @access public
 */
Stack.prototype.top = function()
{
	if ( !this.isEmpty() )
		return this.stack[this.stack.length-1];
	else
		return null;
};
